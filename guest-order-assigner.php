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