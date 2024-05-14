<?php

namespace Gravity_Forms\Gravity_Tools\Query;

class Where_Parser {

	protected $wpdb;

	protected $queryable;

	protected $custom_handlers;

	public $default_column;

	public function __construct( $wpdb, $queryable_columns, $default_column, $custom_handlers = array() ) {
		$this->wpdb            = $wpdb;
		$this->queryable       = $queryable_columns;
		$this->default_column  = $default_column;
		$this->custom_handlers = $custom_handlers;
	}

	public function process( $filters, $trailing_union = false, $union = 'AND', $passed_key = null, $raw_columns = false ) {
		$sql_array = array();

		foreach ( $filters as $key => $value ) {

			if ( is_array( $value ) ) {
				$sql_array[] = $this->process( $value, false, 'OR', $key, $raw_columns );
				continue;
			}

			if ( $passed_key !== null ) {
				$key = $passed_key;
			}

			if ( array_key_exists( $key, $this->custom_handlers ) ) {
				$sql_array[] = call_user_func( $this->custom_handlers[ $key ], $value, $this );
				continue;
			}

			if ( in_array( $key, $this->queryable ) || $raw_columns ) {
				$wrapper = $raw_columns ? null : '`';
				$sql_array[] = $this->wpdb->prepare( "%s" . $key . "%s = %s", $wrapper, $wrapper, $value );
				continue;
			}

			$sql_array[] = sprintf( 'REGEXP_LIKE( `%s`, \'"%s"[^"]+"%s"\' )', $this->default_column, $key, $value );
		}

		$sql = implode( ' ' . $union . ' ', $sql_array );

		$sql = "($sql)";

		if ( $trailing_union ) {
			$sql .= ' ' . $union . ' ';
		}

		return $sql;
	}

}