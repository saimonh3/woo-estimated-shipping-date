<?php
/**
* Plugin Name: Woocommerce Estimated Shipping Date
* Description: A simple woocommerce based plugin to show the estimated shipping date on the cart and product page
* Author: Mohammed Saimon
* Author URI: http://saimon.info
* Version: 1.0
* Tested up to: 4.9.4
* Requires PHP: 5.6
* Text Domain: wcesd
* License: GPLv2 or later
**/

if ( ! defined( 'ABSPATH' ) ) exit;

final class Woocommerce_estimated_shipping_date {
	protected $version = 1.0;
	private static $instance;

	public function __construct() {
		$this->define_constants();

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_activation_hook( __FILE__, array( $this, 'deactivate' ) );

        $this->init_hooks();
	}

	public function define_constants() {
		define( 'WC_ESD', plugin_dir_path( __FILE__ ) );
        define( 'WC_ESD_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/' );
	}

    public function init_hooks() {
        add_action( 'admin_enqueue_scripts', array( $this, 'wc_esd_enqueue_scripts' ) );
        add_action( 'woocommerce_product_options_shipping', array( $this, 'wc_esd_add_estimated_shipping_date' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'wc_esd_save_shipping_date') );
        add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'wc_esd_show_date' ) );
    }

    function wc_esd_enqueue_scripts() {
        if ( ! is_admin() || get_post_type() !== 'product' ) {
            return;
        }

        wp_enqueue_script( 'wcesd-js', WC_ESD_ASSETS . 'js/wcesd.js', array( 'jquery', 'jquery-ui-datepicker' ), time(), true );
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
            'description'   => __( 'Insert how many days it will take to delivar the product after purchase', 'wcesd' ),
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
    }

    public function wc_esd_save_shipping_date( $product_id ) {
        if ( ! is_admin() || get_post_type() !== 'product' ) {
            return;
        }

        $wc_esd_date_enable     = isset( $_POST['wc_esd_date_enable'] ) ? $_POST['wc_esd_date_enable'] : '';
        $wc_esd_date            = isset( $_POST['wc_esd_date'] ) ? $_POST['wc_esd_date'] : '';
        $wc_esd_date_message    = isset( $_POST['wc_esd_date_message'] ) ? $_POST['wc_esd_date_message'] : '';

        update_post_meta( $product_id, 'wc_esd_date_enable', esc_attr( $wc_esd_date_enable ) );
        update_post_meta( $product_id, 'wc_esd_date', esc_attr( $wc_esd_date ) );
        update_post_meta( $product_id, 'wc_esd_date_message', esc_attr( $wc_esd_date_message ) );
    }

    public function wc_esd_show_date() {
        $wc_esd_date_enable     = get_post_meta( get_the_ID(), 'wc_esd_date_enable', true );

        if ( $wc_esd_date_enable !== 'yes' ) {
            return;
        }

        $date                   = get_post_meta( get_the_ID(), 'wc_esd_date', true );
        $wc_esd_date            = date( wc_date_format(), strtotime( '+' . $date . 'days' ) );
        $wc_esd_date_message    = get_post_meta( get_the_ID(), 'wc_esd_date_message', true );

        printf( esc_attr__( "%s: %s", "wcesd" ), $wc_esd_date_message, $wc_esd_date );
    }

	public function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();

			case 'ajax':
				return defined( 'DOING_AJAX' );

			case 'public':
				return ! is_admin() || ! defined( 'DOING_AJAX' );
		}
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Woocommerce_estimated_shipping_date;
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
