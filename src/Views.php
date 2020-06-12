<?php

namespace Saimon\WCESD;

use Saimon\WCESD\Views\Single_Product;
use Saimon\WCESD\Views\Thankyou;
use Saimon\WCESD\Views\Cart;

defined( 'ABSPATH' ) || exit;

class Views {

	public function __construct() {
		$this->hooks();

		new Single_Product();
		new Cart();
		new Thankyou();
	}

	private function hooks() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'wcesd-css', WC_ESD_ASSETS . 'css/public-style.css', null, time(), 'all' );
	}
}