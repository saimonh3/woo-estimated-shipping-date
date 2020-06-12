<?php

namespace Saimon\WCESD\Views;

use Saimon\WCESD\Helper;

defined( 'ABSPATH' ) || exit;

class Single_Product {

	public function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action( 'woocommerce_after_add_to_cart_form', [ self::class, 'show_date' ] );
	}

	public static function show_date() {
		if ( 'yes' !== Helper::get_option( 'wc_esd_date_enable' ) ) {
			return;
		}

		$wc_esd_date         = Helper::get_option( 'wc_esd_date' );
		$wc_esd_date         = $wc_esd_date ? $wc_esd_date : 5;
		$wc_esd_date_message = Helper::get_option( 'wc_esd_date_message' );
		$wc_esd_date_message = $wc_esd_date_message ? $wc_esd_date_message : __( 'Estimated Delivery Date', 'wcesd' );
		$date                = date_i18n( wc_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );

		printf(
			wp_kses(
				__( "<strong class='shipping-date'> %s %s</strong>", "wcesd" ),
				array( 'strong' => array( 'class' => true ) )
			),
			$wc_esd_date_message, $date
		);
	}
}