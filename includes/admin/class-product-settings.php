<?php
/**
 * No cheating please
 */
if ( ! defined( 'WPINC' ) ) exit;

/**
 * WCESD_Product_Settings Class
 */
class WCESD_Product_Settings {
	/**
	 * Hold the instance
	 *
	 * @var string
	 */
	private static $instance;

	use helperMethods;

	/**
	 * Constructor method
	 *
	 * @return void
	 */
	public function __construct() {
		if ( ! $this->enabled() ) {
			return;
		}

		$this->init_hooks();
	}

	/**
	 * Init all the hooks
	 *
	 * @return void
	 */
	protected function init_hooks() {
		add_action( 'woocommerce_product_options_shipping', array( $this, 'wc_esd_add_estimated_shipping_date' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'wc_esd_save_shipping_date') );
	}

	/**
	 * Add wcesd form
	 *
	 * @return void
	 */
	public function wc_esd_add_estimated_shipping_date() {
		woocommerce_wp_checkbox( array(
			'id'            => 'wc_esd_date_enable',
			'label'         => __( 'Enable WC Estimated Date', 'wcesd' ),
			'description'   => __( 'Enable or Disable woocommerce estimated shipping date', 'wcesd' ),
			'desc_tip'      => true,
		) );
		woocommerce_wp_text_input( array(
			'id'            => 'wc_esd_date',
			'label'         => __( 'Estimated Delivery Time in Days', 'wcesd' ),
			'description'   => __( 'Insert how many days it will take to deliver the product after purchase', 'wcesd' ),
			'desc_tip'      => true,
			'type'          => 'number',
			'placeholder'   => 5,
		) );
		woocommerce_wp_text_input( array(
			'id'            => 'wc_esd_date_message',
			'label'         => __( 'Estimated Delivery Date Message', 'wcesd' ),
			'description'   => __( 'Insert your message', 'wcesd' ),
			'desc_tip'      => true,
			'placeholder'   => 'Estimated Delivery Date',
		) );

		do_action( 'wc_esd_add_estimated_shipping_date' );
	}

	/**
	 * Save wcesd form data
	 *
	 * @param  int $product_id
	 *
	 * @return void
	 */
	public function wc_esd_save_shipping_date( $product_id ) {
		if ( ! is_admin() || get_post_type() !== 'product' ) {
			return;
		}

		$wc_esd_date_enable  = isset( $_POST['wc_esd_date_enable'] ) ? sanitize_text_field( $_POST['wc_esd_date_enable'] ) : '';
		$wc_esd_date         = isset( $_POST['wc_esd_date'] ) ? sanitize_text_field( $_POST['wc_esd_date'] ) : '';
		$wc_esd_date_message = isset( $_POST['wc_esd_date_message'] ) ? sanitize_text_field( $_POST['wc_esd_date_message'] ) : '';

		update_post_meta( $product_id, 'wc_esd_date_enable', $wc_esd_date_enable );
		update_post_meta( $product_id, 'wc_esd_date', $wc_esd_date );
		update_post_meta( $product_id, 'wc_esd_date_message', $wc_esd_date_message );

		do_action( 'wc_esd_save_shipping_date', $product_id );
	}

	/**
	 * Get instance
	 *
	 * @return object
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Disable cloning this class
	 *
	 * @return void
	 */
	private function __clone() {
		//
	}

	private function __wakeup() {
		//
	}
}

WCESD_Product_Settings::init();