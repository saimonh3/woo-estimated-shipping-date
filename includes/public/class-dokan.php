<?php
/**
 * No cheating please
 */
if ( ! defined( 'WPINC' ) ) exit;

/**
 * WCESD Dokan Class
 */
class WCESD_Dokan {
    private static $instance;

    use helperMethods;

    /**
     * Constructor method
     */
    public function __construct() {
        if ( ! $this->enabled() ) {
            return;
        }

        if ( $this->wcesd_get_option( 'wcesd_enable_dokan', 'wcesd_settings' ) !== 'on' ) {
            return;
        }

        $this->init_hooks();
    }

    /**
     * Init all the hooks
     *
     * @return void
     */
    private function init_hooks() {
        add_action( 'dokan_product_edit_after_shipping', array( $this, 'add_wcesd_form' ) );
        add_action( 'dokan_product_updated', array( $this, 'save_product_post_data' ), 15 );
    }

    /**
     * Add wcesd form
     *
     * @param string
     */
    public function add_wcesd_form( $product_id ) {
        $wc_shipping_enabled = get_option( 'woocommerce_calc_shipping' ) == 'yes' ? true : false;

        if ( ! $wc_shipping_enabled ) {
            return;
        }

        $wc_esd_date         = get_post_meta( $product_id, 'wc_esd_date', true );
        $wc_esd_date_enable  = get_post_meta( $product_id, 'wc_esd_date_enable', true );
        $wc_esd_date_message = get_post_meta( $product_id, 'wc_esd_date_message', true );
        ?>

        <div class="dokan-product-shipping-tax hide_if_virtual hide_if_grouped dokan-edit-row dokan-clearfix dokan-border-top  woocommerce-no-tax">
            <div class="dokan-section-heading" data-togglehandler="dokan_product_shipping_tax">
                <h2><i class="fa fa-truck" aria-hidden="true"></i> <?php _e( 'WooCommerce Estimated Shipping Date', 'dokan' ); ?></h2>
                <p><?php _e( 'Manage estimated shipping date for this product', 'dokan' ); ?></p>
                <a href="#" class="dokan-section-toggle">
                    <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true"></i>
                </a>
                <div class="dokan-clearfix"></div>
            </div>

            <div class="dokan-section-content">
                <div class="dokan-clearfix dokan-shipping-container">
                    <input type="hidden" name="product_shipping_class" value="0">
                    <div class="dokan-form-group">
                        <label class="dokan-checkbox-inline" for="wc_esd_date_enable">
                            <input type="checkbox" id="wc_esd_date_enable" name="wc_esd_date_enable" value="yes" <?php checked( $wc_esd_date_enable, 'yes' ); ?>>
                            <?php _e( 'Enable WC Estimated Date', 'wcesd' ); ?>
                        </label>
                    </div>

                    <div class="dokan-form-group content-half-part">
                        <label for="upsell_ids" class="form-label">
                            <?php _e( 'Estimated Delivery Time in Days', 'wcesd' ); ?>
                            <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'Insert how many days it will take to deliver the product after purchase', 'wcesd' ); ?>" data-original-title="" title=""></i>
                        </label>
                        <input type="number" id="wc_esd_date" name="wc_esd_date" value="<?php echo esc_attr( $wc_esd_date ); ?>" >
                    </div>

                    <div class="dokan-form-group content-half-part">
                        <label for="upsell_ids" class="form-label">
                            <?php _e( 'Estimated Delivery Date Message', 'wcesd' ); ?>
                            <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'Estimated Delivery Date Message', 'wcesd' ); ?>" data-original-title="" title=""></i>
                        </label>
                        <input type="text" id="wc_esd_date_message" name="wc_esd_date_message" value="<?php echo esc_attr( $wc_esd_date_message ); ?>">
                    </div>
                    <div class="dokan-clearfix"></div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Save product post data
     *
     * @param  int $product_id
     *
     * @return void
     */
    public function save_product_post_data( $product_id ) {
        if ( ! $product_id ) {
            return;
        }

        $wc_esd_date         = isset( $_POST['wc_esd_date'] ) ? wc_clean( $_POST['wc_esd_date'] ) : '';
        $wc_esd_date_enable  = isset( $_POST['wc_esd_date_enable'] ) ? wc_clean( $_POST['wc_esd_date_enable'] ) : '';
        $wc_esd_date_message = isset( $_POST['wc_esd_date_message'] ) ? wc_clean( $_POST['wc_esd_date_message'] ) : '';

        update_post_meta( $product_id, 'wc_esd_date_enable', $wc_esd_date_enable );
        update_post_meta( $product_id, 'wc_esd_date', $wc_esd_date );
        update_post_meta( $product_id, 'wc_esd_date_message', $wc_esd_date_message );

        do_action( 'wc_esd_save_shipping_date', $product_id );
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

WCESD_Dokan::init();