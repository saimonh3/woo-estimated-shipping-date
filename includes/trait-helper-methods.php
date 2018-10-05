<?php
/**
 * No cheating please
 */

if ( ! defined( 'WPINC' ) ) exit;

/**
 * helperMethods trait
 */
trait helperMethods {
	/**
	 * Check if WCESD is enabled
	 * 
	 * @return boolean
	 */
	public function enabled() {
		$settings = $this->wcesd_get_option( 'wcesd_enable', 'wcesd_settings' );

		return $settings === 'on' ? true : false;
	}

	/**
	 * Get the value of a settings field
	 *
	 * @param string $option settings field name
	 * @param string $section the section name this field belongs to
	 * @param string $default default text if it's not found
	 * @return mixed
	 */
	public function wcesd_get_option( $option, $section, $default = '' ) {
	    $options = get_option( $section );

	    if ( isset( $options[$option] ) ) {
	        return $options[$option];
	    }

	    return $default;
	}

	/**
	 * Check if wcesd is enabled for all products
	 * 
	 * @return boolean
	 */
	public function enabled_for_all_products() {
		$settings = $this->wcesd_get_option( 'wcesd_enable_all_products', 'wcesd_settings' );

		return $settings === 'on' ? true : false;
	}

	/**
	 * Get author ids by capability
	 * 
	 * @param  string $capability
	 * 
	 * @return array ids
	 */
	public function get_author_ids_by_capability( $capability ) {

		if ( ! $capability ) {
			$capability = 'manage_woocommerce';
		}

		$users = get_users( array( 'fields' => 'ID' ) );

		$allowed_users = array_map( function( $user ) use ( $capability ) {
			if ( ! user_can( $user, $capability ) ) {
				return;
			}

			return $user;
		}, $users );

        return $allowed_users;
	}
}