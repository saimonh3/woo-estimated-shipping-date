<?php

namespace Saimon\WCESD;

use DateInterval;
use DatePeriod;
use DateTime;

defined( 'ABSPATH' ) || exit;

class Helper {
	const SETTINGS_KEY = 'wcesd_settings';
	public static $settings = null;

	public static function is_ready() {
		return 'on' === self::get_settings( 'wcesd_enable' );
	}

	public static function enabled_all_products() {
		return 'on' === self::get_settings( 'wcesd_enable_all_products' );
	}

	public static function is_weekend_excluded() {
		return 'on' === self::get_settings( 'wc_esd_exclude_weekend' );
	}

	public static function get_option( $key, $id = null, $return_single = true ) {
		$id = $id ?? get_the_ID();

		return get_post_meta( $id, $key, $return_single );
	}

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

	public static function get_admin_users() {
		return self::get_author_ids_by_capability();
	}

	public static function get_weekend_count( $from, $to ) {
		$period = new DatePeriod(
			new DateTime( $from ),
			new DateInterval( 'P1D' ),
			new DateTime( $to )
		);

		$weekends = 0;
		foreach ($period as $value) {
			if ($value->format( 'N' ) >= 6) {
				$weekends++;
			}
		}

		return $weekends;
	}
}