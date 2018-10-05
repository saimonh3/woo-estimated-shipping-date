<?php 
/**
 * No cheating please
 */
if ( ! defined( 'WPINC' ) ) exit;

/**
 * WCESD_Settings Class
 */
class WCESD_Settings {
	/**
	 * Hold the instance
	 * 
	 * @var string
	 */
	private static $instance;
    private $settings_api;

	use helperMethods;

    /**
     * Constructor method
     *
     * @return void
     */
	public function __construct() {
        $this->includes();
        $this->settings_api = new WeDevs_Settings_API;
		$this->init_hooks();
	}

    private function includes() {
        require_once WC_ESD_INC . '/libs/settings-api.php';
    }

    /**
     * Init all the hooks
     * 
     * @return void
     */
	protected function init_hooks() {
        add_action( 'admin_init', array( $this, 'admin_init') );
        add_action( 'admin_menu', array( $this, 'admin_menu') );

		// enqueue all the scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'wc_esd_enqueue_scripts' ) );

		// enable wcesd for all product
		add_action( 'wp_ajax_enabled_for_all_products', array( $this, 'enable_wcesd_for_all_product' ) );
	}

    public function admin_init() {
        //set the settings
        $this->settings_api->set_sections( $this->get_wcesd_settings_section() );
        $this->settings_api->set_fields( $this->get_wcesd_settings() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    public function admin_menu() {
        add_menu_page( __( 'Estimated Shipping Date', 'wcesd' ), __( 'Shipping Date', 'wcesd' ), 'manage_woocommerce', 'wcesd', array( $this, 'plugin_page' ), 'dashicons-calendar', 55 );
    }

    public function get_wcesd_settings_section() {
        $sections = array(
            array(
                'id'    => 'wcesd_settings',
                'title' => __( 'WooCommerce Estimated Shipping Date Settings', 'wcesd' )
            ),
        );
        return $sections;
    }

    public function plugin_page() {
        echo '<div class="wrap">';
        $this->settings_api->show_forms();
        $this->load_enable_for_all_products();
        echo '</div>';
    }

    private function load_enable_for_all_products() {
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
                    <input id="limit" type="hidden" name="limit" value="<?php echo apply_filters( 'enabled_for_all_products_limits', 100 ); ?>">
                    <input id="offset" type="hidden" name="offset" value="0">
                    <input id="enable-for-all-products" type="submit" class="button button-primary" value="<?php _e( 'Enable for All', 'wcesd' ); ?>" >
                    <span class="regen-sync-loader" style="display:none"></span>
                </div>
            </div>
        </div>  
        <?php 
    }


    /**
     * Enqueue all the scripts
     * 
     * @param  string $hook
     * 
     * @return void
     */
	function wc_esd_enqueue_scripts( $hook ) {
		if ( $hook !== 'toplevel_page_wcesd' ) {
			return;
		}

		wp_enqueue_style( 'wcesd-css', WC_ESD_ASSETS . 'css/admin-style.css', null, time(), 'all' );
		wp_enqueue_script( 'wcesd-js', WC_ESD_ASSETS . 'js/wcesd.js', array( 'jquery' ), false, true );
		wp_localize_script( 'wcesd-js', 'wcesd', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}


    /**
     * Get wcesd settings fields
     * 
     * @return string
     */
    public function get_wcesd_settings() {   
        $settings = array(
            'wcesd_settings' => array(
                array(
                    'name'      => 'wcesd_enable',
                    'label'     => __( 'Enable', 'wcesd' ),
                    'desc'      => __( 'Enable WooCommerce Estimated Shipping Date.', 'wcesd' ),
                    'type'      => 'checkbox',
                    'options'   => array( 'on' ),
                    'default'   => 'on'
                ),
                array(
                    'label'    => __( 'Enable Shipping Date for All Existing Product', 'wcesd' ),
                    'desc'     => __( 'Enable Shipping for all existing product by default. If this option is not checked you will have to enable estimated shipping date for each individual product manually', 'wcesd' ),
                    'name'     => 'wcesd_enable_all_products',
                    'type'     => 'checkbox',
                    'options'  => array( 'on', 'off' ),
                    'default'  => 'on',
                ),
                array(
                    'name'          => 'wc_esd_date_default',
                    'label'         => __( 'Estimated Delivery Time in Days', 'wcesd' ),
                    'desc'   => __( 'Insert how many days it will take to deliver the product after purchase', 'wcesd' ),
                    'type'          => 'number',
                    'default'       => 5
                ),
                array(
                    'name'            => 'wc_esd_date_message_default',
                    'label'         => __( 'Estimated Delivery Date Message', 'wcesd' ),
                    'desc'   => __( 'Insert your message', 'wcesd' ),
                    'type'          => 'text',
                    'default'       => 'Estimated Delivery Date',
                ),
            )
        );

        return apply_filters( 'woo_estimated_shipping_date', $settings );
    }

    /**
     * Enable wcesd for all product
     * 
     * @return void
     */
    public function enable_wcesd_for_all_product() {
        if ( ! isset( $_POST['nonce'], $_POST['limit'], $_POST['offset'] ) ) {
        	return;
        }

        if ( ! wp_verify_nonce( $_POST['nonce'], 'enabled_for_all_products' ) ) {
            wp_send_json_error();
        }

        $limit          = $_POST['limit'];
        $offset         = $_POST['offset'];
        $total_products = isset( $_POST['total_products'] ) ? $_POST['total_products'] : 0;
  
    	if ( ! is_admin() ) {
    		return;
    	}

    	if ( ! $this->enabled() ) {
    		return;
    	}

    	if ( ! $this->enabled_for_all_products() ) {
    		return;
    	}

    	if ( ! current_user_can( 'manage_woocommerce' ) ) {
    		return;
    	}

    	$users = $this->get_author_ids_by_capability( 'manage_woocommerce' );

    	$args = array(
    		'post_type'   => 'product',
    		'post_status' => 'publish',
    		'numberposts' => $limit,
    		'offset'      => $offset,
    		'author__in'  => $users,
    		'fields'      => 'ids'
    	);

		$products       = get_posts( $args );
		$total_products = get_posts( array(
    		'post_type'   => 'product',
    		'post_status' => 'publish',
    		'numberposts' => -1,
    		'author__in'  => $users,
    		'fields'      => 'ids'
		) );

		if ( $products ) {
			$wc_esd_date_default         = $this->wcesd_get_option( 'wc_esd_date_default', 'wcesd_settings' );
			$wc_esd_date_message_default = $this->wcesd_get_option( 'wc_esd_date_message_default', 'wcesd_settings' );

			foreach ( $products as $product ) {
	    		update_post_meta( $product, 'wc_esd_date_enable', 'yes' );
	    		update_post_meta( $product, 'wc_esd_date', $wc_esd_date_default );
	    		update_post_meta( $product, 'wc_esd_date_message', $wc_esd_date_message_default );
	    	}
	    	
	    	$done           = count( $products );
	    	$total_products = count( $total_products );

	        wp_send_json_success( array(
	            'offset'         => $offset + $limit,
	            'total_products' => $total_products,
	            'done'           => $done,
	            'message'        => sprintf( __( '%d products are completed out of %d', 'wcesd' ), $done, $total_products )
	        ) );
		} else {
            wp_send_json_success( array(
                'offset'  => 0,
                'done'    => 'All',
                'message' => sprintf( __( 'WooCommerce estimated shipping date has been applied to all products.', 'wcesd' ) )
            ) );	
		}
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

WCESD_Settings::init();