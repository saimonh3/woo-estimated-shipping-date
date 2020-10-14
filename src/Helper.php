<?php

namespace Saimon\WCESD;

use DateInterval;
use DatePeriod;
use DateTime;
use Exception;

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
		$begin    = new DateTime( gmdate( 'Y-m-d H:i:s', $from ) );
		$interval = new DateInterval( 'P1D' );
		$end      = new DateTime( gmdate( 'Y-m-d H:i:s', $to ) );
		error_log( print_r( $end, true ) );
		$end->modify( '+1 day' );

		try {
			$period = new DatePeriod( $begin, $interval, $end );
		} catch ( Exception $e ) {
			return 0;
		}

		$weekends = 0;
		foreach ($period as $value) {
			error_log( print_r( $value->format('l'), true ) );
			if ($value->format( 'N' ) >= 6 ) {
				$weekends++;
			}
		}

//		error_log( print_r( $period, true ) );
		error_log( print_r( $weekends, true ) );

		return $weekends;
	}

	public static function get_next_business_day( $date ) {
		$date = new DateTime( gmdate( 'Y-m-d H:i:s', strtotime( $date ) ) );
		static $count = 0;
		if ( $date->format('N') < 6 ) {
			error_log( print_r( ['count', $count], true ) );
			return $date->format( self::get_date_format( 'date' ) );
		}
		$count ++;
		return self::get_next_business_day( $date->modify( '+1 day' )->format( 'Y-m-d H:i:s' ) );
	}

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
}