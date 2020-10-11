<?php

namespace Saimon\WCESD\Views;

use Saimon\WCESD\Helper;

defined( 'ABSPATH' ) || exit;

class Cart {

	public function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action( 'woocommerce_cart_item_name', [ self::class, 'show_date' ], 10, 2 );
	}

	public static function show_date( $cart_item, $cart_item_key ) {
		if ( 'yes' !== Helper::get_option( 'wc_esd_date_enable', $cart_item_key['product_id'] ) ) {
			return $cart_item;
		}

		$wc_esd_date         = Helper::get_option( 'wc_esd_date', $cart_item_key['product_id'] );
		$wc_esd_date         = $wc_esd_date ? $wc_esd_date : 5;
		$wc_esd_date_message = Helper::get_option( 'wc_esd_date_message', $cart_item_key['product_id'] );
		$wc_esd_date_message = $wc_esd_date_message ? $wc_esd_date_message : __( 'Estimated Delivery Date', 'wcesd' );
		$date                = date_i18n( wc_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );

		if ( Helper::is_weekend_excluded() ) {
			$from          = date_i18n( wc_date_format() );
			$to            = $date;
			$weekend_count = Helper::get_weekend_count( $from, $to );
			$wc_esd_date   += $weekend_count;
			$date          = date_i18n( wc_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );
		}

		$cart_item .= '<br>';
		$cart_item .= sprintf( wp_kses( __("<strong>%s %s</strong>", "wcesd" ), array( 'strong' => array() ) ), $wc_esd_date_message, $date );
		$cart_item .= '</br>';

		return $cart_item;
	}
}