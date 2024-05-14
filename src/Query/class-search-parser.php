<?php

namespace Gravity_Forms\Gravity_Tools\Query;

class Search_Parser {

	protected $wpdb;

	protected $column_map;

	public function __construct( $wpdb, $column_map ) {
		$this->wpdb       = $wpdb;
		$this->column_map = $column_map;
	}

	public function process( $filters, $trailing_union = false, $union = 'AND', $passed_key = null ) {
		$sql_array = array();

		foreach ( $filters as $key => $value ) {
			if ( $passed_key !== null ) {
				$key = $passed_key;
			}

			if ( is_array( $key ) ) {
				foreach( $key as $subkey ) {
					$sql_array[] = $this->process( array( $value ), false, 'OR', $subkey );
				}
				continue;
			}

			$column_name = isset( $this->column_map[ $key ] ) ? $this->column_map[ $key ] : $key;

			if ( is_array( $value ) ) {
				$sql_array[] = $this->process( $value, false, 'OR', $column_name );
				continue;
			}

			$sql_array[] = $this->wpdb->prepare(
				"`" . $column_name . "` LIKE '%%%s%%'",
				$value
			);
		}

		$sql = implode( ' ' . $union . ' ', $sql_array );

		$sql = "($sql)";

		if ( $trailing_union ) {
			$sql .= ' ' . $union . ' ';
		}

		return $sql;
	}

}