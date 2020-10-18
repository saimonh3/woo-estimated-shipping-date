<?php

namespace Saimon\WCESD;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Class Date_Calculator
 * @package Saimon\WCESD
 */
class Date_Calculator {
	private $from;
	private $number_of_days;
	private $exclude_weekends;

	/**
	 * Date_Calculator constructor.
	 *
	 * @param $from
	 * @param $number_of_days
	 * @param $exclude_weekends
	 */
	public function __construct( $from, $number_of_days, $exclude_weekends = true ) {
		$this->from             = $from;
		$this->number_of_days   = $number_of_days;
		$this->exclude_weekends = $exclude_weekends;
	}

	/**
	 * @return bool
	 */
	private function is_valid_timestamp() {
		try {
			new DateTime( '@' . $this->from );
		} catch( Exception $e ) {
			return false;
		}

		return true;
	}

	/**
	 * @return DateTimeImmutable|false
	 */
	private function calculate() {
		if ( ! $this->is_valid_timestamp() ) {
			return false;
		}

		$from = $this->get_next_business_day( $this->from );
		$date = ( new DateTimeImmutable() )->setTimestamp( $from );

		while ( $this->number_of_days ) {
			$date    = $date->add( new DateInterval( 'P1D' ) );
			$weekend = (int) $date->format( 'N' );

			if ( $this->exclude_weekends && ( $weekend === 6 || $weekend === 7 ) ) {
				continue;
			}

			$this->number_of_days --;
		}

		return $date->format( 'Y-m-d H:i:s' );
	}


	/**
	 * @param $date
	 *
	 * @return mixed
	 */
	private function get_next_business_day( $timestamp ) {
		$date = ( new DateTime() )->setTimestamp( $timestamp );

		if ( $date->format('N') < 6 ) {
			return $date->getTimestamp();
		}

		return $this->get_next_business_day( $date->modify( '+1 day' )->getTimestamp() );
	}

	/**
	 * @return DateTimeImmutable|false
	 */
	public function get_date() {
		return $this->calculate();
	}
}
