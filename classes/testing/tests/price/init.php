<?php
/**
 * Setup price tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\tests\price;


use ingot\testing\cookies\cache;
use ingot\testing\crud\price_test;
use ingot\testing\tests\flow;
use ingot\testing\utility\helpers;

class init {

	/**
	 * Constructor
	 *
	 * @since 0.0.9
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $tests;

	/**
	 * Holds the data to put into cookie cache[ 'products' ]
	 *
	 * Used to create an array of data to track in purchases
	 *
	 * @since 0.0.9
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $product_map;

	public function  __construct( $tests ) {

		$this->setup_tests( $tests );
		cache::instance()->update( 'products', $this->product_map );

	}

	/**
	 * Setup tests if possible
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param array $tests
	 */
	protected function setup_tests( $tests ) {
		if( ! empty( $tests ) ){
			foreach( $tests as $test_detail ){

				$plugin = helpers::v( 'plugin', $test_detail, 0 );
				if( is_string( $plugin ) && ingot_acceptable_plugin_for_price_test( $plugin ) ){
					$class_name = $this->class_name( $plugin );
					if( class_exists( $class_name ) ){
						$this->tests[ $plugin ] = helpers::v( 'plugin', 'test_ID', 0 );
						$this->increase_total( $test_detail  );
						$this->add_to_product_map( $test_detail );
						new $class_name( $test_detail[ 'test_ID' ], helpers::v( 'a_or_b', $test_detail, 'a' ) );
					}
				}

			}

		}

	}

	/**
	 * Add to our product map
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param $data
	 */
	protected function add_to_product_map( $data ) {
		$this->product_map[ $data['plugin'] ][ $data['product_ID'] ] = array(
			'sequence' => $data[ 'sequence_ID' ],
			'test_ID'  => $data[ 'test_ID' ]
		);
	}

	/**
	 * Get callback class name
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param string $plugin
	 *
	 * @return string
	 */
	protected function class_name( $plugin ){
		return "\\ingot\\testing\\tests\\price\\plugins\\" . $plugin;
	}

	/**
	 * Track that test ran
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param array $data
	 */
	protected function increase_total( $data ){
		flow::increase_total( helpers::v( 'test_ID', $data, 0 ), helpers::v( 'sequence_ID', $data, 0 ) );
	}


}
