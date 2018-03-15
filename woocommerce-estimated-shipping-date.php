<?php
/**
* Plugin Name: Woocommerce Estimated Shipping Date
* Description: A simple woocommerce based plugin to show the estimated shipping date on the product, cart, checkout page
* Author: Mohammed Saimon
* Author URI: http://saimon.info
* Version: 1.0
* Tested up to: 4.9.4
* Requires PHP: 5.6
* Text Domain: wcesd
* License: GPLv2 or later
**/

if ( ! defined( 'ABSPATH' ) ) exit;

final class Woocommerce_Estimated_Shipping_Date {
	protected $version = 1.0;
	private static $instance;

	public function __construct() {
		$this->define_constants();

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        $this->init_hooks();
	}

	public function define_constants() {
		define( 'WC_ESD', plugin_dir_path( __FILE__ ) );
		define( 'WC_ESD_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/' );
	}

    public function init_hooks() {
        add_action( 'wp_enqueue_scripts', array( $this, 'wc_esd_enqueue_scripts' ) );
        add_action( 'woocommerce_product_options_shipping', array( $this, 'wc_esd_add_estimated_shipping_date' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'wc_esd_save_shipping_date') );
        add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'wc_esd_show_date' ) );

		// checkout page
		add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'wc_esd_show_date_chekcout_page' ), 10, 2 );
		// cart page
		add_filter( 'woocommerce_cart_item_name', array( $this, 'wc_esd_show_date_cart_page' ), 10, 2 );
		// thankyou page
		add_action( 'woocommerce_order_item_meta_start', array( $this, 'wc_esd_show_date_thankyou_page' ), 10, 3 );
    }

    function wc_esd_enqueue_scripts() {
		wp_enqueue_style( 'wcesd-css', WC_ESD_ASSETS . 'css/style.css', null, time(), 'all' );
    }

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
            'placeholder'   => 5
        ) );
        woocommerce_wp_text_input( array(
            'id'            => 'wc_esd_date_message',
            'label'         => __( 'Estimated Delivery Date Message', 'wcesd' ),
            'description'   => __( 'Insert your message', 'wcesd' ),
            'desc_tip'      => true,
            'placeholder'   => 'Estimated Delivery Date'
        ) );

		do_action( 'wc_esd_add_estimated_shipping_date' );
    }

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

    public function wc_esd_show_date() {
		$wc_esd_date         = get_post_meta( get_the_ID(), 'wc_esd_date', true );
		$wc_esd_date_enable  = get_post_meta( get_the_ID(), 'wc_esd_date_enable', true );
		$wc_esd_date_message = get_post_meta( get_the_ID(), 'wc_esd_date_message', true );

        if ( $wc_esd_date_enable !== 'yes' || empty( $wc_esd_date ) || empty( $wc_esd_date_message ) ) {
            return;
        }

        $date = date( wc_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );

        printf(
			wp_kses( __("<strong class='shipping-date'> %s %s</strong>", "wcesd" ), array( 'strong' => array( 'class' => true ) ) ),
			$wc_esd_date_message, $date
		);
    }

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

    	$date = date( wc_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );

		$cart_item .= '<br>';
		$cart_item .= sprintf( wp_kses( __("<strong>%s %s</strong>", "wcesd" ), array( 'strong' => array() ) ), $wc_esd_date_message, $date );
		$cart_item .= '</br>';

		return $cart_item;
	}

	public function wc_esd_show_date_chekcout_page( $cart_item, $cart_item_key ) {
		$wc_esd_date         = get_post_meta( $cart_item_key['product_id'], 'wc_esd_date', true );
        $wc_esd_date_enable  = get_post_meta( $cart_item_key['product_id'], 'wc_esd_date_enable', true );
		$wc_esd_date_message = get_post_meta( $cart_item_key['product_id'], 'wc_esd_date_message', true );

        if ( $wc_esd_date_enable !== 'yes' || empty( $wc_esd_date ) || empty( $wc_esd_date_message ) ) {
            return;
        }

		$date = date( wc_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );

		$cart_item .= '<br>';
		$cart_item .= sprintf( wp_kses( __("<strong>%s %s</strong>", "wcesd" ), array( 'strong' => array() ) ), $wc_esd_date_message, $date );
		$cart_item .= '</br>';

		return $cart_item;
	}

	public function wc_esd_show_date_thankyou_page( $item_id, $item, $order ) {
		$wc_esd_date         = get_post_meta( $item['product_id'], 'wc_esd_date', true );
		$wc_esd_date_enable  = get_post_meta( $item['product_id'], 'wc_esd_date_enable', true );
		$wc_esd_date_message = get_post_meta( $item['product_id'], 'wc_esd_date_message', true );

        if ( $wc_esd_date_enable !== 'yes' || empty( $wc_esd_date ) || empty( $wc_esd_date_message ) ) {
            return;
        }

		$date = date( wc_date_format(), strtotime( '+' . $wc_esd_date . 'days' ) );

		$massage = '<br>';
		$massage .= sprintf( wp_kses( __("<strong>%s %s</strong>", "wcesd" ), array( 'strong' => array() ) ), $wc_esd_date_message, $date );
		$massage .= '</br>';

		echo $massage;
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Woocommerce_Estimated_Shipping_Date;
		}

		return self::$instance;
	}

	public function activate() {
    	if ( ! function_exists( 'WC' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            deactivate_plugins( plugin_basename( __FILE__ ) );

            wp_die( '<div class="error"><p>' . sprintf( __( '<b>WC Minimum Maximum Order</b> requires %sWooCommerce%s to be installed & activated!', 'dokan-lite' ), '<a target="_blank" href="https://wordpress.org/plugins/woocommerce/">', '</a>' ) . '</p></div>' );
        }
	}

	public function deactivate() {
		//
	}

}

add_action( 'plugins_loaded', array( 'Woocommerce_estimated_shipping_date', 'init' ) );
