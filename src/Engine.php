<?php

namespace Saimon\WCESD;

defined( 'ABSPATH' ) || exit;

class Engine {

	public function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action( 'wp_ajax_enabled_for_all_products', [ $this, 'enable_for_all_products' ] );
	}

	public function enable_for_all_products() {
		$data = wp_unslash( $_POST );

		if ( ! isset( $data['nonce'], $data['limit'], $data['offset'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $data['nonce'], 'enabled_for_all_products' ) ) {
			wp_send_json_error();
		}

		$limit          = $data['limit'];
		$offset         = $data['offset'];
		$total_products = isset( $data['total_products'] ) ? $data['total_products'] : 0;

		if ( ! is_admin() || ! Helper::is_ready() ) {
			return;
		}

		if ( ! Helper::enabled_all_products() ) {
			return;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$users = Helper::get_admin_users();

		$args = [
			'post_type'   => 'product',
			'post_status' => 'publish',
			'numberposts' => $limit,
			'offset'      => $offset,
			'author__in'  => $users,
			'fields'      => 'ids'
		];

		$products       = get_posts( $args );
		$total_products = get_posts( [
			'post_type'   => 'product',
			'post_status' => 'publish',
			'numberposts' => - 1,
			'author__in'  => $users,
			'fields'      => 'ids'
		] );

		if ( $products ) {
			$wc_esd_date_default         = Helper::get_settings( 'wc_esd_date_default' );
			$wc_esd_date_message_default = Helper::get_settings( 'wc_esd_date_message_default' );

			foreach ( $products as $product ) {
				update_post_meta( $product, 'wc_esd_date_enable', 'yes' );
				update_post_meta( $product, 'wc_esd_date', $wc_esd_date_default );
				update_post_meta( $product, 'wc_esd_date_message', $wc_esd_date_message_default );
			}

			$done           = count( $products );
			$total_products = count( $total_products );

			wp_send_json_success( [
				'offset'         => $offset + $limit,
				'total_products' => $total_products,
				'done'           => $done,
				'message'        => sprintf( __( '%d products are completed out of %d', 'wcesd' ), $done, $total_products )
			] );
		} else {
			wp_send_json_success( [
				'offset'  => 0,
				'done'    => 'All',
				'message' => sprintf( __( 'WooCommerce estimated shipping date has been applied to all products.', 'wcesd' ) )
			] );
		}
	}
}