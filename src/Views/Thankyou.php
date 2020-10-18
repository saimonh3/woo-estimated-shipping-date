<?php

namespace Saimon\WCESD\Views;

use Saimon\WCESD\Date_Calculator;
use Saimon\WCESD\Helper;

defined( 'ABSPATH' ) || exit;

class Thankyou {

	public function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action( 'woocommerce_order_item_meta_start', [ self::class, 'show_date' ], 10, 2 );
	}

	/**
	 * @param $cart_item
	 * @param $cart_item_key
	 */
	public static function show_date( $cart_item, $cart_item_key ) {
		if ( 'yes' !== Helper::get_option( 'wc_esd_date_enable', $cart_item_key['product_id'] ) ) {
			return $cart_item;
		}

		$wc_esd_date         = Helper::get_option( 'wc_esd_date', $cart_item_key['product_id'] );
		$wc_esd_date         = $wc_esd_date ? $wc_esd_date : 5;
		$wc_esd_date_message = Helper::get_option( 'wc_esd_date_message', $cart_item_key['product_id'] );
		$wc_esd_date_message = $wc_esd_date_message ? $wc_esd_date_message : __( 'Estimated Delivery Date', 'wcesd' );
		$today               = strtotime( current_time( 'mysql' ) );
		$to_date             = '';

		if ( Helper::is_weekend_excluded() ) {
			$date = ( new Date_Calculator( $today, $wc_esd_date ) )->get_date();

			if ( Helper::is_date_range_enabled() ) {
				$wc_esd_date = $wc_esd_date + Helper::get_date_range_gap();
				$to_date = ( new Date_Calculator( $today, $wc_esd_date ) )->get_date();
			}
		} else {
			$date = ( new Date_Calculator( $today, $wc_esd_date, false ) )->get_date();

			if ( Helper::is_date_range_enabled() ) {
				$wc_esd_date = $wc_esd_date + Helper::get_date_range_gap();
				$to_date = ( new Date_Calculator( $today, $wc_esd_date ) )->get_date();
			}
		}

		if ( ! empty( $to_date ) ) {
			$message = $wc_esd_date_message . ' ' . Helper::display_date( $date ) . ' - ' . Helper::display_date( $to_date );
		} else {
			$message = $wc_esd_date_message . ' ' . Helper::display_date( $date );
		}

		if ( is_view_order_page() ) {
			$product_id        = $cart_item_key['product_id'];
			$order             = wc_get_order( $cart_item_key['order_id'] );
			$purchased_date    = $order->get_meta( 'wc_esd_date_for_order_' . $product_id );
			$purchased_to_date = $order->get_meta( 'wc_esd_date_for_order_to_date_' . $product_id );

			if ( empty( $purchased_date ) ) {
				return;
			}

			$message = date_i18n( Helper::get_date_format( 'date' ), $purchased_date );

			if ( ! empty( $purchased_to_date ) ) {
				$message .= ' - ' . date_i18n( Helper::get_date_format( 'date' ), $purchased_to_date );
			}
		}

		$html = '<br>';
		$html .= sprintf( wp_kses( __("<strong>%s</strong>", "wcesd" ), array( 'strong' => array() ) ), $message );
		$html .= '</br>';

		echo $html;
	}
}
