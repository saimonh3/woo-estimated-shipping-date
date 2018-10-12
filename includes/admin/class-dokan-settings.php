<?php
/**
 * No cheating please
 */
if ( ! defined( 'WPINC' ) ) exit;

/**
 * WCESD Dokan Class
 */
class WCESD_Dokan_Settings {
    /**
     * Hold the instance
     *
     * @var string
     */
    private static $instance;

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
        add_filter( 'woo_estimated_shipping_date', array( $this, 'register_dokan_settings' ) );
    }

    /**
     * Register dokan settings
     *
     * @param  array $settings
     *
     * @return array
     */
    public function register_dokan_settings( $settings ) {
        $dokan_settings = array(
            'label'    => __( 'Enable Shipping Date for Dokan Multivendor Plugin', 'wcesd' ),
            'desc'    => __( 'Enable Shipping for Dokan Multivendor Plugin', 'wcesd' ),
            'name'       => 'wcesd_enable_dokan',
            'type'     => 'checkbox',
            'options'  => array( 'on', 'off' ),
            'default'  => 'on',
        );

        array_push( $settings['wcesd_settings'], $dokan_settings );

        return $settings;
    }

    /**
     * Get class instance
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

WCESD_Dokan_Settings::init();