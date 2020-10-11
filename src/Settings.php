<?php

namespace Saimon\WCESD;

use WeDevs_Settings_API;

defined( 'ABSPATH' ) || exit;

class Settings {
	const SLUG          = 'wcesd';
	const CAPABILITY    = 'manage_woocommerce';
	const ICON          = 'dashicons-calendar';
	const MENU_POSITION = 55;

	private $settings_api = null;

	public function __construct() {
		$this->settings_api = new WeDevs_Settings_API();

		$this->hooks();
	}

	private function hooks() {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'saimon_wcesd_wrap_end', [ $this, 'enable_for_all_products' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts( $hook ) {
		if ( $hook !== 'toplevel_page_wcesd' ) {
			return;
		}

		wp_enqueue_style( 'wcesd-admin-style', WC_ESD_ASSETS . 'css/admin-style.css', null, time(), 'all' );
		wp_enqueue_script( 'wcesd-admin-script', WC_ESD_ASSETS . 'js/admin-script.js', [ 'jquery' ], time(), true );
		wp_localize_script( 'wcesd-admin-script', 'wcesd_pro', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );
	}

	public function admin_init() {
		$this->settings_api->set_sections( $this->get_settings_section() );
		$this->settings_api->set_fields( $this->get_settings() );
		$this->settings_api->admin_init();
	}

	public function admin_menu() {
		add_menu_page(
			__( 'Estimated Shipping Date', 'wcesd' ),
			__( 'Shipping Date', 'wcesd' ),
			self::CAPABILITY,
			self::SLUG,
			[ $this, 'plugin_page' ],
			self::ICON,
			self::MENU_POSITION
		);
	}

	public function get_settings_section() {
		return apply_filters( 'saimon_wcesd_get_section', [
			[
				'id'    => 'wcesd_settings',
				'title' => __( 'WooCommerce Estimated Shipping Date Settings', 'wcesd' )
			],
		] );
	}

	public function plugin_page() {
		echo '<div class="wrap">';
		do_action( 'saimon_wcesd_wrap_start' );
		$this->settings_api->show_forms();
		do_action( 'saimon_wcesd_wrap_end' );
		echo '</div>';
	}

	/**
	 * Get wcesd settings fields
	 *
	 * @return string
	 */
	public function get_settings() {
		return apply_filters( 'saimon_wcesd_get_settings', [
			'wcesd_settings' => [
				[
					'name'    => 'wcesd_enable',
					'label'   => __( 'Enable', 'wcesd' ),
					'desc'    => __( 'Enable WooCommerce Estimated Shipping Date.', 'wcesd' ),
					'type'    => 'checkbox',
					'options' => [ 'on' ],
					'default' => 'on'
				],
				[
					'label'   => __( 'Enable Shipping Date for All Existing Product', 'wcesd' ),
					'desc'    => __( 'Enable Shipping for all existing product by default. If this option is not checked you will have to enable estimated shipping date for each individual product manually.', 'wcesd' ),
					'name'    => 'wcesd_enable_all_products',
					'type'    => 'checkbox',
					'options' => [ 'on', 'off' ],
					'default' => 'on',
				],
				[
					'name'    => 'wc_esd_date_default',
					'label'   => __( 'Estimated Delivery Time in Days', 'wcesd' ),
					'desc'    => __( 'Insert how many days it will take to deliver the product after purchase.', 'wcesd' ),
					'type'    => 'number',
					'default' => 5
				],
				[
					'name'    => 'wc_esd_exclude_weekend',
					'label'   => __( 'Exclude Weekend', 'wcesd' ),
					'desc'    => __( 'Exclude weekend from the shipping date calculation.', 'wcesd' ),
					'type'    => 'checkbox',
					'options' => [ 'on', 'off' ],
					'default' => 'on'
				],
				[
					'name'    => 'wc_esd_date_message_default',
					'label'   => __( 'Estimated Delivery Date Message', 'wcesd' ),
					'desc'    => __( 'Insert estimated delivery date message.', 'wcesd' ),
					'type'    => 'text',
					'default' => 'Estimated Delivery Date',
				],
			]
		] );
	}

	public function enable_for_all_products() {
		?>
		<div class="wcesd postbox">
			<h3><?php _e( 'Enable Shipping Date for All Existing Products', 'wcesd' ); ?></h3>
			<div class="inside">
				<p><?php _e( 'This tool will enable WooCommerce Estimated Shipping Date for all the existing products. It\'s helpful if you have lots of products, that you don\'t want to insert estimated shipping date for each and every product manually. Which is very time consuming.', 'wcesd' ); ?></p>
				<div class="regen-sync-response"></div>
				<div id="progressbar" style="display: none">
					<div id="regen-pro" >0</div>
				</div>
				<div id="enable-for-all-products-div">
					<?php wp_nonce_field( 'enabled_for_all_products', 'my_nonce' ); ?>
					<input id="limit" type="hidden" name="limit" value="<?php echo apply_filters( 'saimon_wcesd_enabled_for_all_products_limits', 25 ); ?>">
					<input id="offset" type="hidden" name="offset" value="0">
					<input id="enable-for-all-products" type="submit" class="button button-primary" value="<?php _e( 'Enable for All', 'wcesd' ); ?>" >
					<span class="regen-sync-loader" style="display:none"></span>
				</div>
			</div>
		</div>
		<?php
	}
}