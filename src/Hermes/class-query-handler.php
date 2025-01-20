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
		$data        = array();

		foreach ( $query_token->children() as $object ) {
			$object_name          = ! empty( $object->alias() ) ? $object->alias() : $object->object_type();
			$sql                  = $this->recursively_generate_sql( $object );
			$data[ $object_name ] = sprintf( 'SELECT %s', $sql );
		}

		$results = array();

		foreach ( $data as $data_group_name => $query_to_execute ) {
			$query_results = $wpdb->get_results( $query_to_execute, ARRAY_A );
			$rows          = array();
			foreach ( $query_results as $query_result ) {
				$json_data    = array_shift( $query_result );
				$decoded_data = json_decode( $json_data, true );
				$rows[]       = $decoded_data;
			}

			$results[ $data_group_name ] = $rows;
		}

		return $results;
	}

	public function recursively_generate_sql( Data_Object_From_Array_Token $data, $idx_prefix = null, $parent_table = false, $parent_object_type = false ) {
		global $wpdb;

		$sql = '';

		$meta_fields  = array();
		$local_fields = array();

		$object_type = $data->object_type();
		$object_name = ! empty( $data->alias() ) ? $data->alias() : $data->object_type();

		if ( ! $this->models->has( $object_type ) ) {
			$error_message = sprintf( 'Attempting to access invalid object type %s', $object_type );
			throw new \InvalidArgumentException( $error_message );
		}

		$object_model = $this->models->get( $object_type );

		if ( ! $object_model->has_access() ) {
			$error_message = sprintf( 'Access not allowed for object type %s', $object_type );
			throw new \InvalidArgumentException( $error_message );
		}

		$table_name  = $this->compose_table_name( $object_type );
		$table_alias = $this->compose_table_alias( $object_name, $parent_table );

		$fields_to_process  = $data->fields();
		$categorized_fields = $this->categorize_fields( $object_model, $fields_to_process, $table_alias );

		$arguments = $data->arguments();

		$field_pairs   = array();
		$where_clauses = array();
		$join_clauses  = array();

		$field_sql     = null;
		$from_sql      = null;
		$join_sql      = null;
		$where_sql     = null;
		$group_sql     = null;
		$limit_sql     = null;
		$separator_sql = null;

		if ( ! empty( $arguments ) ) {
			$this->get_where_clauses_from_arguments( $where_clauses, $table_alias, $arguments );
			$limit_sql = $this->get_limit_from_arguments( $arguments );
		}

		foreach ( $categorized_fields['local'] as $field_name => $field_alias ) {
			if ( is_a( $field_alias, Data_Object_From_Array_Token::class ) ) {
				$this_alias    = empty( $field_alias->alias() ) ? $field_alias->object_type() : $field_alias->alias();
				$sub_sql       = $this->recursively_generate_sql( $field_alias, null, $table_alias, $object_type );
				$sub_sql_parts = explode( '|gsmtpfieldsseparator|', $sub_sql );
				$sub_sql       = sprintf( '( SELECT JSON_ARRAYAGG( %s ) %s )', $sub_sql_parts[0], $sub_sql_parts[1] );
				$field_pairs[] = sprintf( '"%s", %s', $this_alias, $sub_sql );
				continue;
			}

			$field_pairs[] = sprintf( '"%s", %s.%s', $field_alias, $table_alias, $field_name );
		}

		$meta_table_name = $this->compose_table_name( 'meta' );

		foreach ( $categorized_fields['meta'] as $field_name => $field_data ) {
			$value_clause   = $parent_table ? sprintf( '%s.meta_value', $field_data['lookup_table_alias'] ) : sprintf( 'MIN(%s.meta_value)', $field_data['lookup_table_alias'] );
			$field_pairs[]  = sprintf( '"%s", %s', $field_data['alias'], $value_clause, $field_name );
			$join_clauses[] = sprintf( 'LEFT JOIN %s AS %s ON %s.object_type = "%s" AND %s.meta_name = "%s" AND %s.object_id = %s.id',
				$meta_table_name,
				$field_data['lookup_table_alias'],
				$field_data['lookup_table_alias'],
				$object_type,
				$field_data['lookup_table_alias'],
				$field_name,
				$field_data['lookup_table_alias'],
				$table_alias
			);
		}

		if ( $parent_table ) {
			$parent_model       = $this->models->get( $parent_object_type );
			$relationship       = $parent_model->relationships()->get( $object_type );
			$lookup_table_name  = $this->compose_join_table_name( $relationship->get_table_suffix() );
			$lookup_table_alias = sprintf( 'join_%s', $table_alias );
			$id_string          = sprintf( '%s_id', $object_type );
			$parent_id_string   = sprintf( '%s_id', $parent_object_type );
			$join_clauses[]     = sprintf( 'LEFT JOIN %s AS %s ON %s.id = %s.%s', $lookup_table_name, $lookup_table_alias, $table_alias, $lookup_table_alias, $id_string );
			$where_clauses[]    = sprintf( '%s.%s = %s.id', $lookup_table_alias, $parent_id_string, $parent_table );
		}

		$field_sql = implode( ', ', $field_pairs );

		$from_sql = sprintf( 'FROM %s AS %s', $table_name, $table_alias );

		$join_sql = implode( ' ', $join_clauses );

		if ( ! empty( $where_clauses ) ) {
			$where_sql = sprintf( 'WHERE %s', implode( ' AND ', $where_clauses ) );
		}

		$group_sql = null;

		if ( ! $parent_table ) {
			$group_sql = sprintf( 'GROUP BY %s.id', $table_alias );
		}

		if ( $parent_table ) {
			$separator_sql = '|gsmtpfieldsseparator|';
		}

		return sprintf( 'JSON_OBJECT( %s ) %s %s %s %s %s %s', $field_sql, $separator_sql, $from_sql, $join_sql, $where_sql, $group_sql, $limit_sql );
	}

	protected function categorize_fields( $object_model, $fields_to_process, $table_alias ) {
		$categorized = array(
			'meta'  => array(),
			'local' => array(),
		);

		foreach ( $fields_to_process as $field ) {
			if ( is_a( $field, Data_Object_From_Array_Token::class ) ) {
				$child_type = $field->object_type();
				if ( ! $object_model->relationships()->has( $child_type ) ) {
					$error_string = sprintf( 'Attempting to access invalid related object %s for object type %s', $child_type, $object_model->type() );
					throw new \InvalidArgumentException( $error_string );
				}

				$categorized['local'][ $field->alias() ] = $field;
				continue;
			}

			$field_name = $field->name();

			if ( ! array_key_exists( $field_name, $object_model->fields() ) && ! array_key_exists( $field_name, $object_model->meta_fields() ) ) {
				$error_string = sprintf( 'Attempting to access invalid field %s on object type %s', $field_name, $object_model->type() );
				throw new \InvalidArgumentException( $error_string );
			}

			$alias      = $field->alias();
			$identifier = $alias ? $alias : $field_name;

			if ( array_key_exists( $field_name, $object_model->fields() ) ) {
				$categorized['local'][ $field_name ] = $identifier;
			}

			if ( array_key_exists( $field_name, $object_model->meta_fields() ) ) {
				$categorized['meta'][ $field_name ] = array(
					'alias'              => $identifier,
					'lookup_table_alias' => sprintf( 'meta_%s_%s', $table_alias, $identifier ),
				);
			}
		}

		return $categorized;
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

		$select_clause = sprintf(
			'"%1$s", %2$s.meta_value',
			$alias,
			$meta_table_alias
		);

		$join_clause = sprintf(
			'LEFT JOIN %1$s AS %2$s ON %3$s.object_id = %4$s.id AND %5$s.object_type = "%6$s" AND %7$s.meta_name = "%8$s"',
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

			$clauses[] = sprintf( '%1$s %2$s %3$s', $column_name, $comparator, $column_value );
		}

		return $clauses;
	}

	private function compose_table_name( $object_type ) {
		global $wpdb;

		return sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $object_type );
	}

	private function compose_join_table_name( $suffix ) {
		global $wpdb;

		return sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $suffix );
	}

	private function compose_table_alias( $object_name, $parent_table_name = false ) {
		if ( ! empty( $parent_table_name ) ) {
			return sprintf( '%s_%s', $parent_table_name, $object_name );
		}

		return sprintf( 'table_%s', $object_name );
	}

	private function get_limit_from_arguments( $arguments ) {
		$response = '';

		$limit = array_values( array_filter( $arguments, function ( $item ) {
			return $item['key'] === 'limit';
		} ) );

		$offset = array_values( array_filter( $arguments, function ( $item ) {
			return $item['key'] === 'offset';
		} ) );

		if ( ! empty( $limit ) ) {
			$response .= sprintf( 'LIMIT %s', $limit[0]['value'] );
		}

		if ( ! empty( $offset ) ) {
			$response .= sprintf( ' OFFSET %s', $offset[0]['value'] );
		}

		return $response;
	}

	private function get_where_clauses_from_arguments( &$where_clauses, $table_alias, $arguments ) {
		foreach ( $arguments as $argument ) {
			if ( $argument['key'] === 'limit' || $argument['key'] === 'offset' ) {
				continue;
			}

			if ( $argument['comparator'] === 'in' ) {
				$in_vals = explode( '|', $argument['value'] );
				foreach ( $in_vals as $key => $value ) {
					$in_vals[ $key ] = sprintf( '"%s"', $value );
				}
				$in_string       = implode( ', ', $in_vals );
				$clause          = sprintf( '%s.%s IN (%s)', $table_alias, $argument['key'], $in_string );
				$where_clauses[] = $clause;
				continue;
			}

			$clause          = sprintf( '%s.%s %s "%s"', $table_alias, $argument['key'], $argument['comparator'], $argument['value'] );
			$where_clauses[] = $clause;
		}
	}

}