<?php

namespace Gravity_Forms\Gravity_Tools\Query;

class Order_Parser {

	protected $wpdb;

	public function __construct( $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function process( $orders ) {
		$sql_array = array();

		foreach( $orders as $key => $dir ) {
			$sql_array[] = $this->wpdb->prepare( '`%s` %s', $key, $dir );
		}

		$sql_string = implode( ', ', $sql_array );

		return sprintf( 'ORDER BY %s', $sql_string );
	}

}