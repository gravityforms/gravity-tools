<?php

namespace Gravity_Forms\Gravity_Tools\Hermes;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Data_Object_From_Array_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Field_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Query_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Utils\Model_Collection;

class Query_Handler {

	/**
	 * @var string
	 */
	protected $db_namespace;

	/**
	 * @var Model_Collection
	 */
	protected $models;

	public function __construct( $db_namespace, Model_Collection $models ) {
		$this->db_namespace = $db_namespace;
		$this->models       = $models;
	}

	public function handle_query( $query_string ) {
		global $wpdb;

		// Parse to token array
		$query_token = new Query_Token( $query_string );
		$data = array();

		foreach( $query_token->children() as $object ) {
			$object_name = $object->alias();
			$sql = $this->recursively_generate_sql( $object );
			$data[ $object_name ] = $sql;
		}

		array_walk( $data, function( &$data_item, $key, $wpdb ) {
			$data_item = $wpdb->get_results( $data_item, ARRAY_A );
		}, $wpdb );

		return $data;
	}

	public function recursively_generate_sql( Data_Object_From_Array_Token $data, $idx_prefix = null, $parent_table = false, $parent_object_type = false ) {
		global $wpdb;

		$meta_fields  = array();
		$local_fields = array();

		$object_type      = $data->object_type();
		$object_model     = $this->models->get( $object_type );
		$main_table_alias = sprintf( 'table_%s%s', $object_type, is_null( $idx_prefix ) ? null : '_' . $idx_prefix );
		$main_table_name  = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $object_type );

		$fields_to_process = $data->fields();
		$conditions        = $data->arguments();
		$field_join_clauses   = array();

		foreach ( $fields_to_process as $idx => $field ) {
			// Field is a relationship. Skip for now.
			if ( is_a( $field, Data_Object_From_Array_Token::class ) ) {
				$local_fields[ $field->object_type() ] = $field;
				$lookup_table_name = sprintf( '%s%s_%s_%s', $wpdb->prefix, $this->db_namespace, $object_type, $field->object_type() );
				$lookup_table_alias = 'table_' . $lookup_table_name;
				$parent_id_string = sprintf( '%s_id', $object_type );
				$field_join_clauses[] = $wpdb->prepare( 'LEFT JOIN %s AS %s ON %s.%s = %s.%s', $lookup_table_name, $lookup_table_alias, $main_table_alias, 'id', $lookup_table_alias, $parent_id_string );

				continue;
			}

			$field_name = $field->name();

			if ( ! in_array( $field_name, $object_model->fields() ) && ! in_array( $field_name, $object_model->meta_fields() ) ) {
				$error_string = sprintf( 'Attempting to access invalid field %s on object type %s', $field_name, $object_model->type() );
				throw new \InvalidArgumentException( $error_string );
			}

			$alias = $field->alias();

			if ( in_array( $field_name, $object_model->fields() ) ) {
				$local_fields[ $field_name ] = $alias ? $alias : $field_name;
			}

			if ( in_array( $field_name, $object_model->meta_fields() ) ) {
				$meta_fields[ $field_name ] = $alias ? $alias : $field_name;
			}
		}

		$field_select_clauses = $this->build_local_field_select_clauses( $local_fields, $main_table_alias, $object_type );

		foreach ( $meta_fields as $field_name => $field_alias ) {
			$clauses                = $this->build_meta_query( $field_name, $field_alias, $object_model->type(), $main_table_alias, $idx_prefix );
			$field_select_clauses[] = $clauses['select_clause'];
			$field_join_clauses[]   = $clauses['join_clause'];
		}

		$select_concat = implode( ', ', $field_select_clauses );

		$sql = $wpdb->prepare( 'SELECT JSON_ARRAYAGG( JSON_OBJECT( %s ) ) FROM %s AS %s ', $select_concat, $main_table_name, $main_table_alias );

		if ( ! empty( $parent_table ) ) {
			$lookup_table_name = sprintf( '%s%s_%s_%s', $wpdb->prefix, $this->db_namespace, $parent_object_type, $object_type );
			$lookup_table_alias = 'table_' . $lookup_table_name;
			$parent_id_string = sprintf( '%s_id', $parent_object_type );
			$this_id_string = sprintf( '%s_id', $object_type );

			$field_join_clauses[] = $lookup_table_join_clause;

			$conditions[] = array(
				'key'        => sprintf( '%s.id', $main_table_alias ),
				'value'      => sprintf( '%s.%s', $lookup_table_alias, $this_id_string ),
				'comparator' => '=',
			);
		}

		if ( ! empty( $field_join_clauses ) ) {
			$join_string = implode( ' ', $field_join_clauses );
			$sql         .= $join_string;
		}

		$where_clauses = array();
		$where_string  = null;

		if ( ! empty( $conditions ) ) {
			$where_clauses = $this->build_where_clauses( $conditions );
			$where_string  = ' WHERE ' . implode( ' AND ', $where_clauses );
		}

		$sql .= $where_string;

		return $sql;
	}

	protected function build_local_field_select_clauses( $field_names, $table_alias, $object_type ) {
		$pairs = array();

		foreach ( $field_names as $field_name => $alias ) {
			if ( is_a( $alias, Data_Object_From_Array_Token::class ) ) {
				$value   = '(' . $this->recursively_generate_sql( $alias, $field_name, $table_alias, $object_type ) . ')';
				$pairs[] = sprintf( '"%s", %s', $field_name, $value );
				continue;
			}

			$pairs[] = sprintf( '"%s", %s.%s', $alias, $table_alias, $field_name );
		}

		return $pairs;
	}

	protected function build_meta_query( $meta_name, $alias, $object_type, $parent_table_alias, $idx_prefix ) {
		global $wpdb;

		$meta_table_name  = $wpdb->prefix . $this->db_namespace . '_' . 'meta';
		$meta_table_alias = sprintf( 'meta_%s%s', $meta_name, is_null( $idx_prefix ) ? null : '_' . $idx_prefix );

		$select_clause = $wpdb->prepare(
			'"%s", %s.meta_value',
			$alias,
			$meta_table_alias
		);

		$join_clause = $wpdb->prepare(
			'LEFT JOIN %s AS %s ON %s.object_id = %s.id AND %s.object_type = %s AND %s.meta_name = %s',
			$meta_table_name,
			$meta_table_alias,
			$meta_table_alias,
			$parent_table_alias,
			$meta_table_alias,
			$object_type,
			$meta_table_alias,
			$meta_name
		);

		return array(
			'select_clause' => $select_clause,
			'join_clause'   => $join_clause,
		);
	}

	/**
	 * @param array $conditions
	 *
	 * @return void
	 */
	protected function build_where_clauses( $conditions ) {
		global $wpdb;

		$clauses = array();

		foreach ( $conditions as $condition ) {
			$column_name  = $condition['key'];
			$column_value = $condition['value'];
			$comparator   = $condition['comparator'];

			$clauses[] = $wpdb->prepare( '%1$s %2$s %3$s', $column_name, $comparator, $column_value );
		}

		return $clauses;
	}

}