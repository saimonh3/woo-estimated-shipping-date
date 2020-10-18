<?php

namespace Saimon\WCESD\Views;

use Saimon\WCESD\Date_Calculator;
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
		$today               = strtotime( current_time( 'mysql' ) );

		if ( Helper::is_weekend_excluded() ) {
			$date = ( new Date_Calculator( $today, $wc_esd_date ) )->get_date();
		} else {
			$date = ( new Date_Calculator( $today, $wc_esd_date, false ) )->get_date();
		}

		$date = Helper::display_date( $date );
		$html = '<div class="wesd-box">';
		$html .= '<strong class="shipper-date">';
		$html .= $wc_esd_date_message . ' ' . $date;
		$html .= '</strong>';
		$html .= '</div>';

		echo wp_kses_post(
			$html
		);
	}
}