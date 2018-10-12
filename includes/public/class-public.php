<?php
/**
 * No cheating please
 */

if ( ! defined( 'WPINC' ) ) exit;

/**
 * WCESD_Public Class
 */
class WCESD_Public {
	/**
	 * Hold the instance
	 *
	 * @var string
	 */
	private static $instance;

	use helperMethods;

	/**
	 * Constructor method
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Init all the hooks
	 *
	 * @return void
	 */
	private function init_hooks() {
		if ( ! $this->enabled() ) {
			return;
		}

		// enqueue all the scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'wc_esd_enqueue_scripts' ) );

		add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'wc_esd_show_date' ) );
		// checkout page
		add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'wc_esd_show_date_chekcout_page' ), 10, 2 );
		// cart page
		add_filter( 'woocommerce_cart_item_name', array( $this, 'wc_esd_show_date_cart_page' ), 10, 2 );
		// thankyou page
		add_action( 'woocommerce_order_item_meta_start', array( $this, 'wc_esd_show_date_thankyou_page' ), 10, 3 );
	}

	/**
	 * [wc_esd_enqueue_scripts description]
	 * @return [type] [description]
	 */
	function wc_esd_enqueue_scripts() {
		wp_enqueue_style( 'wcesd-css', WC_ESD_ASSETS . 'css/public-style.css', null, time(), 'all' );
	}

	/**
	 * [wc_esd_show_date description]
	 * @return [type] [description]
	 */
	public function wc_esd_show_date() {
		$wc_esd_date         = get_post_meta( get_the_ID(), 'wc_esd_date', true );
		$wc_esd_date_enable  = get_post_meta( get_the_ID(), 'wc_esd_date_enable', true );
		$wc_esd_date_message = get_post_meta( get_the_ID(), 'wc_esd_date_message', true );

		if ( $wc_esd_date_enable !== 'yes' || empty( $wc_esd_date ) || empty( $wc_esd_date_message ) ) {
			return;
		}

		$date = date_i18n( wc_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );

		printf(
			wp_kses( __("<strong class='shipping-date'> %s %s</strong>", "wcesd" ), array( 'strong' => array( 'class' => true ) ) ),
			$wc_esd_date_message, $date
		);
	}

	/**
	 * [wc_esd_show_date_cart_page description]
	 * @param  [type] $cart_item     [description]
	 * @param  [type] $cart_item_key [description]
	 * @return [type]                [description]
	 */
	public function wc_esd_show_date_cart_page( $cart_item, $cart_item_key ) {
		if ( is_checkout() ) {
			return $cart_item;
		}

		$wc_esd_date         = get_post_meta( $cart_item_key['product_id'], 'wc_esd_date', true );
		$wc_esd_date_enable  = get_post_meta( $cart_item_key['product_id'], 'wc_esd_date_enable', true );
		$wc_esd_date_message = get_post_meta( $cart_item_key['product_id'], 'wc_esd_date_message', true );

		if ( $wc_esd_date_enable !== 'yes' || empty( $wc_esd_date ) || empty( $wc_esd_date_message ) ) {
			return;
		}

		$date = date_i18n( wc_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );

		$cart_item .= '<br>';
		$cart_item .= sprintf( wp_kses( __("<strong>%s %s</strong>", "wcesd" ), array( 'strong' => array() ) ), $wc_esd_date_message, $date );
		$cart_item .= '</br>';

		return $cart_item;
	}

	/**
	 * [wc_esd_show_date_chekcout_page description]
	 * @param  [type] $cart_item     [description]
	 * @param  [type] $cart_item_key [description]
	 * @return [type]                [description]
	 */
	public function wc_esd_show_date_chekcout_page( $cart_item, $cart_item_key ) {
		$wc_esd_date         = get_post_meta( $cart_item_key['product_id'], 'wc_esd_date', true );
		$wc_esd_date_enable  = get_post_meta( $cart_item_key['product_id'], 'wc_esd_date_enable', true );
		$wc_esd_date_message = get_post_meta( $cart_item_key['product_id'], 'wc_esd_date_message', true );

		if ( $wc_esd_date_enable !== 'yes' || empty( $wc_esd_date ) || empty( $wc_esd_date_message ) ) {
			return;
		}

		$date = date_i18n( wc_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );

		$cart_item .= '<br>';
		$cart_item .= sprintf( wp_kses( __("<strong>%s %s</strong>", "wcesd" ), array( 'strong' => array() ) ), $wc_esd_date_message, $date );
		$cart_item .= '</br>';

		return $cart_item;
	}

	/**
	 * wc_esd_show_date_thankyou_page
	 *
	 * @param  [type] $item_id [description]
	 * @param  [type] $item    [description]
	 * @param  [type] $order   [description]
	 * @return [type]          [description]
	 */
	public function wc_esd_show_date_thankyou_page( $item_id, $item, $order ) {
		$wc_esd_date         = get_post_meta( $item['product_id'], 'wc_esd_date', true );
		$wc_esd_date_enable  = get_post_meta( $item['product_id'], 'wc_esd_date_enable', true );
		$wc_esd_date_message = get_post_meta( $item['product_id'], 'wc_esd_date_message', true );

		if ( $wc_esd_date_enable !== 'yes' || empty( $wc_esd_date ) || empty( $wc_esd_date_message ) ) {
			return;
		}

		$date = date_i18n( wc_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );

		$massage = '<br>';
		$massage .= sprintf( wp_kses( __("<strong>%s %s</strong>", "wcesd" ), array( 'strong' => array() ) ), $wc_esd_date_message, $date );
		$massage .= '</br>';

		echo $massage;
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

WCESD_Public::init();
