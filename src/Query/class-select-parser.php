<?php

namespace Gravity_Forms\Gravity_Tools\Query;

class Select_Parser {

	protected $wpdb;

	public function __construct( $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function process( $columns ) {
		$sql_array = array();

		foreach( $columns as $column ) {
			if ( is_array( $column ) ) {
				$column = implode( '.', $column );
			}

			$sql_array[] = $this->wpdb->prepare( '%s', $column );
		}

		$sql_string = implode( ', ', $sql_array );

		return sprintf( 'SELECT %s', $sql_string );
	}

}