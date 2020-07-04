<?php

namespace Saimon\WCESD;

defined( 'ABSPATH' ) || exit;

final class Base {
	const VERSION = '4.0.6';

	private static $controllers = [];

	public static function boot() {
		self::bootstrap();
	}

	private static function bootstrap() {
		add_action( 'init', [ self::class, 'load_text_domain' ] );
		add_action( 'woocommerce_loaded', [ self::class, 'set_controllers' ] );
	}

	public static function load_text_domain() {
		load_plugin_textdomain( 'wcesd', false, dirname( plugin_basename( WC_ESD_FILE ) ) . '/languages/' );
	}

	public static function set_controllers() {
		$controllers = [
			'constants'        => new Constants(),
			'settings'         => new Settings(),
			'product_settings' => new Product_Settings(),
			'views'            => new Views(),
			'engine'           => new Engine(),
			'order'            => new Order(),
		];

		self::$controllers = apply_filters( 'saimon_wcesd_set_controllers', $controllers );
	}

	public function get_version() {
		return self::VERSION;
	}
}