<?php

namespace Saimon\WCESD;

defined( 'ABSPATH' ) || exit;

final class Base {
	const VERSION = '4.0.0';

	private static $controllers = [];

	public static function boot() {
		self::bootstrap();
	}

	private static function bootstrap() {
		add_action( 'woocommerce_loaded', [ self::class, 'set_controllers' ] );
	}

	public static function set_controllers() {
		$controllers = [
			'constants'        => new Constants(),
			'settings'         => new Settings(),
			'product_settings' => new Product_Settings(),
			'views'            => new Views(),
			'engine'           => new Engine(),
		];

		self::$controllers = apply_filters( 'saimon_wcesd_set_controllers', $controllers );
	}

	public function get_version() {
		return self::VERSION;
	}
}