<?php

namespace Saimon\WCESD;

defined( 'ABSPATH' ) || exit;

class Constants {

	public function __construct() {
		$this->define_constants();
	}

	private function define_constants() {
		$this->define( 'WC_ESD_INC',  WC_ESD_FILE . '/includes' );
		$this->define( 'WC_ESD_ASSETS', plugin_dir_url( WC_ESD_FILE ) . 'assets/' );
	}

	private function define( $key, $value ) {
		defined( $key ) || define( $key, $value );
	}
}