<?php
/**
 * Plugin Name:       Guest Order Assigner
 * Plugin URI:        https://www.kazverse.com/plugins/guest-order-assigner
 * Description:       Automatically attaches WooCommerce guest orders to matching user accounts by billing email.
 * Version:           1.0.1
 * Author:            Kazmi
 * Author URI:        https://www.kazverse.com
 * Text Domain:       guest-order-assigner
 * Domain Path:       /languages
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue our admin CSS only on the GOA settings page.
 */
add_action( 'admin_enqueue_scripts', 'goa_enqueue_admin_assets' );
function goa_enqueue_admin_assets( string $hook_suffix ) {
    // Only enqueue when our custom action=goa_settings page is being displayed
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'goa_settings' ) {
        wp_enqueue_style(
            'goa-admin-css',
            plugin_dir_url( __FILE__ ) . 'assets/css/goa-admin.css',
            [],    // no dependencies
            '1.0.0'
        );
    }
}

/**
 * Attach an order to a user if it’s still a guest (user_id = 0).
 */
/**
 * Attach this order to $user if it's still marked as a guest.
 *
 * @param WC_Order $order
 * @param WP_User  $user
 */
function goa_attach_if_guest( WC_Order $order, WP_User $user ): void {
    // Only run on pure guest orders
    if ( 0 !== (int) $order->get_user_id() ) {
        return;
    }

    // Bail if there’s no billing email
    $email = sanitize_email( $order->get_billing_email() );
    if ( ! $email ) {
        return;
    }
    // Confirm the passed-in user matches the billing email
    if ( strtolower( $user->user_email ) !== strtolower( $email ) ) {
        return;
    }

    // All checks passed — assign & persist
    $order->set_customer_id( $user->ID );
    $order->save();
}

/**
 * Back-fill all past guest orders sharing this user’s billing email.
 */
function goa_backfill_guest_orders( WP_User $user ): void {
    $email = sanitize_email( $user->user_email );
    if ( ! $email ) {
        return;
    }

    $orders = wc_get_orders( [
        'limit'         => -1,
        'status'        => array_keys( wc_get_order_statuses() ),
        'billing_email' => $email,
    ] );

    foreach ( $orders as $order ) {
        goa_attach_if_guest( $order, $user );
    }
}

/**
 * Assign the current order (and then back-fill) on every checkout—
 * this covers:
 *   • Pure guest checkouts (even if the user already exists)
 *   • Logged-in checkouts
 *   • “Create account during checkout” checkouts
 */
// Fires for every new order, including guests
add_action( 'woocommerce_new_order', 'attach_guest_order_to_existing_user', 20, 1 );
function attach_guest_order_to_existing_user( int $order_id ): void {

    $order = wc_get_order( $order_id );
    if ( ! $order || (int) $order->get_user_id() !== 0 ) {
        return;
    }

    $billing_email = sanitize_email( $order->get_billing_email() );
    if ( ! $billing_email ) {
        return;
    }

    $user = get_user_by( 'email', $billing_email );
    if ( ! $user ) {
        return;
    }

    $order->set_customer_id( $user->ID );
    $order->save();
}

/**
 * Also back-fill as soon as an account is created via checkout.
 */
add_action( 'woocommerce_created_customer', function ( $customer_id ) {
    if ( $user = get_user_by( 'ID', $customer_id ) ) {
        goa_backfill_guest_orders( $user );
    }
}, 20, 1 );

/**
 * And again on any user login—just in case.
 */
add_action( 'wp_login', function ( $login, $user ) {
    if ( $user instanceof WP_User ) {
        goa_backfill_guest_orders( $user );
    }
}, 20, 2 );

/**
 * On activation, backfill every existing guest order in WooCommerce.
 */
function goa_run_on_activation() {
    // Make sure WC is active
    if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_orders' ) ) {
        return;
    }

    $orders = wc_get_orders( [
        'limit'    => -1,
        'status'   => array_keys( wc_get_order_statuses() ),
        'customer' => 0,
    ] );

    foreach ( $orders as $order ) {
        $email = sanitize_email( $order->get_billing_email() );
        if ( ! $email ) {
            continue;
        }

        // If we find a WP user with that billing email, attach them
        $user = get_user_by( 'email', $email );

        goa_attach_if_guest( $order, $user );
    }

}
register_activation_hook( __FILE__, 'goa_run_on_activation' );


/**
 * 1) Add “Settings” link next to “View details”
 */
add_filter( 'plugin_row_meta', function( array $links, string $file ) {
    if ( plugin_basename( __FILE__ ) === $file ) {
        // Note: we link to admin.php?action=goa_settings
        $url = admin_url( 'admin.php?action=goa_settings' );
        $links[] = sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url( $url ),
            esc_html__( 'Settings', 'guest-order-assigner' )
        );
    }
    return $links;
}, 10, 2 );

/**
 * 1) Register a Settings page under Settings → Guest Order Assigner
 */
add_action( 'admin_menu', 'goa_register_settings_page' );
function goa_register_settings_page() {
    // Build the full dynamic URL
    $settings_action_url = admin_url( 'admin.php?action=goa_settings' );

    // Add under Settings → Guest Order Assigner
    // We pass the full URL as the $menu_slug, and omit the callback
    add_options_page(
        __( 'Guest Order Assigner Settings', 'guest-order-assigner' ), // Page title
        __( 'Guest Order Assigner Settings', 'guest-order-assigner' ), // Menu label
        'manage_options',                                             // Capability
        $settings_action_url                                          // Menu slug = full URL
        // no callback here
    );
}

// Already have this elsewhere in your plugin:
add_action( 'admin_action_goa_settings', 'goa_render_settings_page' );




