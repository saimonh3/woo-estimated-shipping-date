<?php
/**
 * Plugin Name: WooCommerce Estimated Shipping Date
 * Description: A simple WooCommerce based plugin to show the estimated shipping date on the product, cart, checkout page
 * Author: Mohammed Saimon
 * Version: 4.0.6
 * Tested up to: 5.4.2
 * WC requires at least:
 * WC tested up to: 4.2.0
 * Requires PHP: 7.0
 * Text Domain: wcesd
 * License: GPLv2 or later
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_ESD_FILE' ) || define( 'WC_ESD_FILE', __FILE__ );

require_once __DIR__ . '/vendor/autoload.php';

if ( ! function_exists( 'wcesd' ) ) {
	function wcesd() {
		\Saimon\WCESD\Base::boot();
		appsero_tracker();
	}
}

if ( ! function_exists( 'appsero_tracker' ) ) {
	function appsero_tracker() {
		if ( class_exists( 'Appsero\Client' ) ) {
			$client = new Appsero\Client( 'cb89d144-7d16-4036-817a-c38653c19b05', 'WooCommerce Estimated Shipping Date', __FILE__ );
			$client->insights()->init();
		}
	}
}

// kick off
wcesd();