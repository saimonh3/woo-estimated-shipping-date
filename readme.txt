=== WooCommerce Estimated Shipping Date ===
Contributors: saimonh
Tags: woocommerce, estimated shipping date, delivery date, estimated delivery date, shipping date, shipping time, delivery time
Requires at least: 4.0
Tested up to: 5.4.2
WC tested up to: 4.2.0
Stable tag: 4.1.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple WooCommerce based plugin to show the estimated shipping date on the product, cart, checkout page

== Description ==

This plugin will allow you to insert how many days it will take to deliver the product to your customer once it’s purchased. Once you set the required days, it will show up on the individual product page, cart page, checkout page, customer order page and also in their email.

This way your potential customers will get to know how long will it take to receive the intend product and it will greatly help your potential customers to being paid customers.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==

= How to set the estimated shipping date? =

You will have to set the estimated shipping date on the product edit page

=  =


== Screenshots ==
1. Settings Page
2. Settings Options Under Product Edit Page
3. Individual Product Page
4. Individual Product Page
5. Checkout Page
6. Order Received Page
7. Email

== Changelog ==

v4.1.1 -> Oct 18, 2020
------------------------
-   **Fix*** Shipping date calculation.

v4.1.0 -> Oct 11, 2020
------------------------
-   **Fix*** Add option to exclude weekend from Shipping date calculation.
-   **Fix*** Shipping date message border is broken if the text is long.

v4.0.5 -> July 04, 2020
------------------------
-   **Fix*** Shipping date is recalculated even after an order is placed

v4.0.1 -> June 12, 2020
------------------------
-   **Feat*** Allow adding shipping date for all the product at once
-   **Tweak*** Rewrite the whole plugin

v3.0.7 -> June 12, 2020
------------------------
-   **Tweak*** Rename appsero-client to appsero

v3.0.7 -> June 12, 2020
------------------------
-   **Fix*** Fatal error due to Appsero file not found

v3.0.6 -> June 11, 2020
------------------------
-   **Fix*** If shipping date or shipping date message is not inserted, use the default settings

v3.0.4 -> Aug 13, 2019
------------------------
-   **Fix*** Remove default shipping date settings value

v3.0.3 -> Oct 12, 2018
------------------------
-   **Fix*** date format internalization

== Upgrade Notice ==



## Privacy Policy
WooCommerce Estimated Shipping Date uses [Appsero](https://appsero.com) SDK to collect some telemetry data upon user's confirmation. This helps us to troubleshoot problems faster & make product improvements.

Appsero SDK **does not gather any data by default.** The SDK only starts gathering basic telemetry data **when a user allows it via the admin notice**. We collect the data to ensure a great user experience for all our users.

Integrating Appsero SDK **DOES NOT IMMEDIATELY** start gathering data, **without confirmation from users in any case.**

Learn more about how [Appsero collects and uses this data](https://appsero.com/privacy-policy/).