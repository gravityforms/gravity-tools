<?php

namespace Gravity_Forms\Gravity_Tools\Query;

class Join_Parser {

	protected $wpdb;

	/**
	 * @var Where_Parser
	 */
	protected $where_parser;

	public function __construct( $wpdb, $where_parser ) {
		$this->wpdb = $wpdb;
		$this->where_parser = $where_parser;
	}

	public function process( $columns, $join_type = 'LEFT' ) {
		$sql_array = array();
		foreach( $columns as $column => $conditions ) {
			$conditions_sql = $this->where_parser->process( $conditions, false, 'AND', null, true );

			$sql_array[] = $this->wpdb->prepare( '%s ON %s', $column, $conditions_sql );
		}

		$sql_string = implode( ', ', $sql_array );

		return sprintf( '%s JOIN %s', $join_type, $sql_string );
	}

}