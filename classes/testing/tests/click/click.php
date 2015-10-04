<?php
/**
 * Handles tracking for a click test
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\tests\click;

use ingot\testing\crud\group;
use ingot\testing\crud\sequence;

class click {

	/**
	 * Increase total times a test ran inside a sequence
	 *
	 * @since 0.0.3
	 *
	 * @param int $test_id Test ID
	 * @param int $sequence_id Sequence ID
	 *
	 * @return array|bool
	 */
	public static function increase_total( $test_id, $sequence_id ) {
		$sequence = sequence::read( $sequence_id );
		$is_a = self::is_a( $test_id, $sequence );
		switch( $is_a  ) {
			case true === $is_a  :
				$sequence[ 'a_total' ] = $sequence[ 'a_total' ] + 1;
				break;
			case false === $is_a :
				$sequence[ 'b_total' ] = $sequence[ 'b_total' ] + 1;
				break;
		}

		$updated =  sequence::update( $sequence, $sequence_id, true );
		return $updated;

	}

	/**
	 * Increase the times a test "won"
	 *
	 * @since 0.0.3
	 *
	 * @param int $test_id Test ID
	 * @param int $sequence_id Sequence ID
	 *
	 * @return array|bool
	 */
	public static function increase_victory( $test_id, $sequence_id ) {
		$sequence = sequence::read( $sequence_id );
		$is_a = self::is_a( $test_id, $sequence );
		switch( $is_a  ) {
			case true === $is_a  :
				$sequence[ 'a_win' ] = $sequence[ 'a_win' ] + 1;
				break;
			case false === $is_a :
				$sequence[ 'b_win' ] = $sequence[ 'b_win' ] + 1;
				break;
		}

		$updated = sequence::update( $sequence, $sequence_id, true );
		return $updated;

	}

	/**
	 * Check if test is "A" or "B"
	 *
	 * @since 0.0.3
	 *
	 * @param int $test_id Test ID
	 * @param array $sequence Sequence config
	 *
	 * @return bool|\WP_Error True if is A, False is B, WP_Error if neither.
	 */
	public static function is_a( $test_id, $sequence ) {
		if ( $test_id == $sequence[ 'a_id' ] ){
			return true;

		}elseif ( $test_id == $sequence[ 'b_id' ] ) {
			return false;

		}else{
			return new \WP_Error( 'ingot-test-sequence-mismatch' );
		}

	}


	/**
	 * Choose A or B based on a given probability
	 *
	 * @since 0.0.3
	 *
	 * @param int $a_chance Chance (1-100) to
	 *
	 * @return bool True if A is selected, false if not.
	 */
	public static function choose_a( $a_chance ) {
		$val = rand( 1, 100 );
		if ( $val <= $a_chance ) {
			return true;

		}
		
	}

	/**
	 * Set up JS vars to localize in click-test js
	 *
	 * @since 0.0.6
	 *
	 * @return array
	 */
	public static function js_vars() {
		$vars = array(
			'api_url' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'nonce' => wp_create_nonce( 'ingot_nonce' )
		);

		return $vars;
	}

	/**
	 * Create initial sequence for group
	 *
	 * @since 0.0.6
	 *
	 * @param int $group_id The Group ID
	 *
	 * @return array|bool|\WP_Error Array (group config) if succesful, false if not, and WP_Error if input invalid.
	 */
	public static function make_initial_sequence( $group_id ){
		$group = group::read( $group_id );
		if( empty( $group[ 'order' ] ) || isset( $group[0]) || ! isset( $group[1] ) ) {
			return new \WP_Error( 'bad-order-for-sequence-creation', __( 'Can not make sequences for group', 'ingot' ) );
		}

		if ( ! empty( $group ) ){
			$order = $group[ 'order' ];
			$sequence_args = array(
				'type' => $group[ 'type' ],
				'a_id' => $order[0],
				'b_id' => $order[1],
				'initial' => $group[ 'initial' ],
				'threshold' => $group[ 'threshold' ],
				'group' => (int) $group[ 'ID' ]
			);
			$sequence = sequence::create( $sequence_args );
			if( $sequence ) {
				$group[ 'sequences' ][] = $sequence;
			}

			return group::update( $group, $group_id );

		}

	}

	public static function make_next_sequence( $group_id, $last_winner_id ) {

	}

	public static function update_sequences( $group_id ){

	}


}