# WooCommerce Guest Order Linker

Automatically link WooCommerce orders to customers by billing email—both for new orders and past guest orders.

## Why Use This Plugin?

By default, WooCommerce treats guest checkouts as “Guest,” even if the same email later registers or logs in. This plugin:

- **Keeps history tidy:** As soon as a customer places or creates an account, all past “guest” orders with that email become linked to their user account.
- **Shows real names/emails:** Your Orders screen will display the actual customer name or email, not “Guest.”
- **Non-destructive:** Only orders with no existing customer linkage (`customer_id = 0`) are updated.

## Quick Start

1. **Install via WordPress.org**  
   - Go to **Plugins → Add New** in your WordPress admin.  
   - Search for **“Guest Order Assigner”**.  
   - Click **Install Now** and then **Activate**.

2. **Manual Upload (optional)**  
   - Download the ZIP from https://wordpress.org/plugins/guest-order-assigner/.  
   - In your admin, go to **Plugins → Add New → Upload Plugin**.  
   - Choose the ZIP file, click **Install Now**, then **Activate**.

3. **Configure (none needed)**  
   - The plugin works out of the box—no settings page required.  
   - From now on, new orders and matching past guest orders will automatically be linked by billing email.

## How It Works

1. **On Every Order**  
   - Reads the billing email.  
   - If a matching WordPress user exists, sets `customer_id` on that order.

2. **Retroactive Pass**  
   - Calls WooCommerce’s core method to update *all* previous guest orders for that email.