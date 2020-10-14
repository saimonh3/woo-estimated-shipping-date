<?php

namespace Saimon\WCESD;

defined( 'ABSPATH' ) || exit;

class Order {

	const ORDER_PLACED   = 'wc_esd_date_for_order_is_sated';
	const DATE_FOR_ORDER = 'wc_esd_date_for_order_';

	public function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action( 'woocommerce_order_status_changed', [ $this, 'set_date' ] );
	}

	/**
	 * Set date to order on purchase
	 *
	 * @since 4.0.5
	 *
	 * @param $order_id
	 *
	 * @return void
	 */
	public function set_date( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $order->get_meta( self::ORDER_PLACED ) ) {
			return;
		}

		foreach ( $order->get_items() as $item ) {
			$product_id = $item->get_product()->get_id();

			if ( 'yes' !== Helper::get_option( 'wc_esd_date_enable', $product_id ) ) {
				continue;
			}

			$wc_esd_date = Helper::get_option( 'wc_esd_date', $product_id );
			$wc_esd_date = $wc_esd_date ? $wc_esd_date : 5;
			$date        = date_i18n( Helper::get_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );

			if ( Helper::is_weekend_excluded() ) {
				$from          = strtotime( current_time( 'mysql' ) );
				$to            = strtotime( $date );
				$weekend_count = Helper::get_weekend_count( $from, $to );
				$wc_esd_date   += $weekend_count;
				$date          = date_i18n( Helper::get_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );
			}

			$date = strtotime( $date );
			$order->update_meta_data( self::DATE_FOR_ORDER . $product_id, $date );
		}

		$order->update_meta_data( self::ORDER_PLACED, true );
		$order->save();
	}
}