<?php

namespace Saimon\WCESD;

defined( 'ABSPATH' ) || exit;

class Product_Settings {

	public function __construct() {
		if ( ! is_admin() || ! Helper::is_ready() ) {
			return;
		}

		$this->hooks();
	}


	/**
	 * Init all the hooks
	 *
	 * @return void
	 */
	private function hooks() {
		add_action( 'woocommerce_product_options_shipping', [ $this, 'add_estimated_shipping_date' ] );
		add_action( 'woocommerce_process_product_meta', [ $this, 'save_shipping_date' ] );
	}


	/**
	 * Add wcesd form
	 *
	 * @return void
	 */
	public function add_estimated_shipping_date() {
		woocommerce_wp_checkbox( [
			'id'            => 'wc_esd_date_enable',
			'label'         => __( 'Enable WC Estimated Date', 'wcesd' ),
			'description'   => __( 'Enable or Disable WooCommerce estimated shipping date', 'wcesd' ),
			'desc_tip'      => true,
		] );
		woocommerce_wp_text_input( [
			'id'            => 'wc_esd_date',
			'label'         => __( 'Estimated Delivery Time in Days', 'wcesd' ),
			'description'   => __( 'Insert how many days it will take to deliver the product after purchase', 'wcesd' ),
			'desc_tip'      => true,
			'type'          => 'number',
			'placeholder'   => 5,
		] );
		woocommerce_wp_text_input( [
			'id'            => 'wc_esd_date_message',
			'label'         => __( 'Estimated Delivery Date Message', 'wcesd' ),
			'description'   => __( 'Insert your message', 'wcesd' ),
			'desc_tip'      => true,
			'placeholder'   => __( 'Estimated Delivery Date', 'wcesd' ),
		] );

		do_action( 'saimon_wcesd_add_estimated_shipping_date' );
	}

	/**
	 * Save wcesd form data
	 *
	 * @param  int $product_id
	 *
	 * @return void
	 */
	public function save_shipping_date( $product_id ) {
		if ( get_post_type() !== 'product' ) {
			return;
		}

		$wc_esd_date_enable  = isset( $_POST['wc_esd_date_enable'] ) ? sanitize_text_field( $_POST['wc_esd_date_enable'] ) : '';
		$wc_esd_date         = isset( $_POST['wc_esd_date'] ) ? sanitize_text_field( $_POST['wc_esd_date'] ) : '';
		$wc_esd_date_message = isset( $_POST['wc_esd_date_message'] ) ? sanitize_text_field( $_POST['wc_esd_date_message'] ) : '';

		update_post_meta( $product_id, 'wc_esd_date_enable', $wc_esd_date_enable );
		update_post_meta( $product_id, 'wc_esd_date', $wc_esd_date );
		update_post_meta( $product_id, 'wc_esd_date_message', $wc_esd_date_message );

		do_action( 'saimon_wcesd_save_shipping_date', $product_id );
	}
}