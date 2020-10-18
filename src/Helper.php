<?php

namespace Saimon\WCESD;

use DateTime;

defined( 'ABSPATH' ) || exit;

class Helper {
	const SETTINGS_KEY = 'wcesd_settings';
	public static $settings = null;

	/**
	 * @return bool
	 */
	public static function is_ready() {
		return 'on' === self::get_settings( 'wcesd_enable' );
	}

	/**
	 * @return bool
	 */
	public static function enabled_all_products() {
		return 'on' === self::get_settings( 'wcesd_enable_all_products' );
	}

	/**
	 * @return bool
	 */
	public static function is_weekend_excluded() {
		return 'on' === self::get_settings( 'wc_esd_exclude_weekend' );
	}

	/**
	 * @return bool
	 */
	public static function is_date_range_enabled() {
		return 'on' === self::get_settings( 'wc_esd_enable_date_range' );
	}

	/**
	 * @return int
	 */
	public static function get_date_range_gap() {
		return (int) self::get_settings( 'wc_esd_date_range_gap' );
	}

	/**
	 * @param $key
	 * @param null $id
	 * @param bool $return_single
	 *
	 * @return mixed
	 */
	public static function get_option( $key, $id = null, $return_single = true ) {
		$id = $id ?? get_the_ID();

		return get_post_meta( $id, $key, $return_single );
	}

	/**
	 * @param $option
	 * @param string $section
	 * @param string $default
	 *
	 * @return mixed|string
	 */
	public static function get_settings( $option, $section = self::SETTINGS_KEY, $default = '' ) {
		if ( is_null( self::$settings ) ) {
			self::$settings = get_option( $section );
		}

		if ( isset( self::$settings[ $option ] ) ) {
			return self::$settings[ $option ];
		}

		return $default;
	}

	/**
	 * Get author ids by capability
	 *
	 * @param  string $capability
	 *
	 * @return array ids
	 */
	public static function get_author_ids_by_capability( $capability = 'manage_woocommerce' ) {
		$users = get_users( [ 'fields' => 'ID' ] );

		$allowed_users = array_map( function( $user ) use ( $capability ) {
			if ( ! user_can( $user, $capability ) ) {
				return;
			}

			return $user;
		}, $users );

		return apply_filters( 'saimon_wcesd_get_author_ids_by_capability', $allowed_users );
	}

	/**
	 * @return array
	 */
	public static function get_admin_users() {
		return self::get_author_ids_by_capability();
	}

	/**
	 * @param null $format
	 *
	 * @return mixed|void
	 */
	public static function get_date_format( $format = null ) {
		$date_format = get_option( 'date_format', 'Y-m-d' );
		$time_fromat = get_option( 'time_format', 'H:i:s' );

		$date_time_format = '';

		if ( 'date' === $format ) {
			$date_time_format = $date_format;
		}

		if ( 'time' === $format ) {
			$date_time_format = $time_fromat;
		}

		if ( is_null( $format ) ) {
			$date_time_format = $date_format . ' ' . $time_fromat;
		}

		return apply_filters( 'wesd_get_date_format', $date_time_format );
	}

	/**
	 * @param $date
	 *
	 * @return string
	 */
	public static function display_date( $date ) {
		return date_i18n( Helper::get_date_format( 'date' ), strtotime( $date ) );
	}
}