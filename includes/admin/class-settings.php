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
        echo '</div>';
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
                    'name'    => 'wcesd_enable',
                    'label'   => __( 'Enable', 'wcesd' ),
                    'desc'    => __( 'Enable WooCommerce Estimated Shipping Date.', 'wcesd' ),
                    'type'    => 'checkbox',
                    'options' => array( 'on' ),
                    'default' => 'on'
                )
            )
        );

        return apply_filters( 'woo_estimated_shipping_date', $settings );
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