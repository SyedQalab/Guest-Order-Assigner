<br />
<p align="center">
  <a href="link to the projects website">
    <img src="./assets/banner-772x250.png" alt="Logo" width="100%" height="350" style="background-color: white">
  </a>

</p>

# Guest Order Assigner for WooCommerce

[![WordPress Version](https://img.shields.io/badge/Requires-5.0%2B-blue.svg)](https://wordpress.org/)
[![WooCommerce Version](https://img.shields.io/badge/Requires-WooCommerce%203.0%2B-orange.svg)](https://woocommerce.com/)
[![PHP Version](https://img.shields.io/badge/Requires-PHP%207.2%2B-lightgrey.svg)](https://www.php.net/)
[![License: GPLv2+](https://img.shields.io/badge/License-GPLv2%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

  <p align="center">
  <a href="https://github.com/SyedQalab/Guest-Order-Assigner/archive/refs/tags/v1.0.1.zip">Download latest release</a>
</p>
<br />

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration & Customization](#configuration--customization)
- [How It Works](#how-it-works)

---

## Features

- 🔗 **Instant Assignment**  
  New guest orders with a billing email matching an existing account are immediately re-assigned.
- ⏪ **Historic Back-fill**  
  When a user registers or logs in, all previous guest orders with their email are attached.
- ⚡ **Zero Configuration**  
  Works out-of-the-box—no admin UI required.
- 🛠️ **Developer Hooks**  
  Extend via custom actions before/after assignment.

---

## Requirements

- **WordPress** 5.0 or higher
- **WooCommerce** 3.0 or higher
- **PHP** 7.2 or higher

---

## Installation

1. **Instal** `guest-order-assigner` from `wordpress` plugins.

   ![install zip file](./static/install.gif)

2. Activate **Guest Order Assigner** in Plugins.

   ![activate gif file](./static/activate.gif)

---

## How It Works

3. Create a **WooCommerce Product**.

   ![create wooCommerce prdouct gif file](./static/create-wooCommerce-product.gif)

4. Create a **Guest Order** and then check the order created.

   ![create guest order gif file](./static/guest-order.gif)

5. Create a **Order** after logging in or create accout at checkout time.

   ![create guest order gif file](./static/logged-in-product.gif)

6. Check a **WooCommerce Order Listing**.

   ![create guest order gif file](./static/wooCommerce-orders-listing.gif)
