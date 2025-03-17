<?php

namespace Gravity_Forms\Gravity_Tools\Hermes;

use Gravity_Forms\Gravity_Tools\Hermes\Models\Model;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Data_Object_From_Array_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Field_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Query_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Utils\Model_Collection;

/**
 * Query Handler
 *
 * The entry point for parsing queries made to Hermes. For Mutations, see Mutation_Handler.
 */
class Query_Handler {

	/**
	 * The namespace to use when querying DB tables. The namespace is used after the $wpdb->prefix
	 * value and before the actual table name.
	 *
	 * Example:
	 *
	 * Passing `gravitytools` would result in a meta table name of `wp_gravitytools_meta`..
	 *
	 * @var string
	 */
	protected $db_namespace;

	/**
	 * The collection of models supported for queries.
	 *
	 * @var Model_Collection
	 */
	protected $models;


	/**
	 * Constructor
	 *
	 * @param string           $db_namespace
	 * @param Model_Collection $models
	 */
	public function __construct( $db_namespace, Model_Collection $models ) {
		$this->db_namespace = $db_namespace;
		$this->models       = $models;
	}

	/**
	 * Parse the given query string text and perform the appropriate database queries to return
	 * the requested data structure.
	 *
	 * @param string $query_string
	 *
	 * @return array
	 */
	public function handle_query( $query_string ) {
		global $wpdb;

		// Parse to token array
		$query_token = new Query_Token( $query_string );
		$data        = array();

		// Use the Token to generate recursive SQL.
		foreach ( $query_token->children() as $object ) {
			$object_name          = ! empty( $object->alias() ) ? $object->alias() : $object->object_type();
			$sql                  = $this->recursively_generate_sql( $object );
			$data[ $object_name ] = sprintf( 'SELECT %s', $sql );
		}

		$results = array();

		// Decode the results and set them up for return.
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

		wp_send_json_success( $results );
	}

	/**
	 * Loops through the objects in the Query String and recursively generates the appropriate
	 * SQL for the related query. Supports an infinite level of nesting, but caution should be used when
	 * performing deeply-nested queries as performance may be impacted.
	 *
	 * @param Data_Object_From_Array_Token $data
	 * @param                              $idx_prefix
	 * @param                              $parent_table
	 * @param                              $parent_object_type
	 *
	 * @return string
	 */
	public function recursively_generate_sql( Data_Object_From_Array_Token $data, $idx_prefix = null, $parent_table = false, $parent_object_type = false ) {
		$object_type = $data->object_type();
		$object_name = ! empty( $data->alias() ) ? $data->alias() : $data->object_type();

		// Ensure the object type being queried exists as a Model.
		if ( ! $this->models->has( $object_type ) ) {
			$error_message = sprintf( 'Attempting to access invalid object type %s', $object_type );
			throw new \InvalidArgumentException( $error_message, 460 );
		}

		$object_model = $this->models->get( $object_type );

		// Ensure that the querying user has the appropriate permissions to access object.
		if ( ! $object_model->has_access() ) {
			$error_message = sprintf( 'Access not allowed for object type %s', $object_type );
			throw new \InvalidArgumentException( $error_message, 403 );
		}

		// Set up values for the table being queried.
		$table_name  = $this->compose_table_name( $object_type );
		$table_alias = $this->compose_table_alias( $object_name, $parent_table );

		// Categorized queried fields as either local or meta fields for future processing.
		$fields_to_process  = $data->fields();
		$categorized_fields = $this->categorize_fields( $object_model, $fields_to_process, $table_alias );

		$arguments = $data->arguments();

		// Set up data arrays for holding pieces of the SQL statement for later concatenation.
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

		// Arguments are present; parse them and add them to the appropriate SQL arrays.
		if ( ! empty( $arguments ) ) {
			$this->get_where_clauses_from_arguments( $where_clauses, $table_alias, $arguments );
			$limit_sql = $this->get_limit_from_arguments( $arguments );
		}

		// Loop through each local field and generate the appropriate SQL chunks for retrieving the data.
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

		// Loop through each meta field and compose the appropriate JOIN query for gathering its data.
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

		// A parent table exists, meaning this is a nested query and requires a JOIN statement relating it
		// to the parent table.
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

		// Concatenate each SQL array to generate the final SQL.
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

		// Return the resulting SQL
		return sprintf( 'JSON_OBJECT( %s ) %s %s %s %s %s %s', $field_sql, $separator_sql, $from_sql, $join_sql, $where_sql, $group_sql, $limit_sql );
	}

	/**
	 * Categorizes fields as either local (i.e., existing as columns within the table for the object) or meta
	 * (i.e., existing as custom values in the meta table).
	 *
	 * @param Model  $object_model
	 * @param array  $fields_to_process
	 * @param string $table_alias
	 *
	 * @return array|array[]
	 */
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
					throw new \InvalidArgumentException( $error_string, 455 );
				}

				$categorized['local'][ $field->alias() ] = $field;
				continue;
			}

			$field_name = $field->name();

			if ( ! array_key_exists( $field_name, $object_model->fields() ) && ! array_key_exists( $field_name, $object_model->meta_fields() ) ) {
				$error_string = sprintf( 'Attempting to access invalid field %s on object type %s', $field_name, $object_model->type() );
				throw new \InvalidArgumentException( $error_string, 450 );
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

	/**
	 * From the given array of $field_names, build the properly-structured SQL for selecting them.
	 *
	 * If a field is detected as a related object, we treat it as a top-level query and recursively
	 * begin the SQL generation process for it.
	 *
	 * @param array  $field_names
	 * @param string $table_alias
	 * @param string $object_type
	 *
	 * @return array
	 */
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

	/**
	 * Generates the appropriate SQL clause(s) for selecting a Meta field. Meta fields exist in a lookup table, and thus require both a SELECT statement to properly
	 * query the fields as well as a JOIN clause to join the meta table with a specific alias.
	 *
	 * @param string $meta_name          The name of the field being selected.
	 * @param string $alias              The alias to use when returning the selected field.
	 * @param string $object_type        The object type to grab the values from.
	 * @param string $parent_table_alias If present, the parent table this nested query belongs to.
	 * @param string $idx_prefix         If present, the previous IDX prefix used for the parent table.
	 *                                   (to be prenended to this table's alias)
	 *
	 * @return array
	 */
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
	 * Build a SQL WHERE clause from the given set of conditions.
	 *
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

	/**
	 * Compose the proper table name for a given object type.
	 *
	 * @param string $object_type
	 *
	 * @return string
	 */
	private function compose_table_name( $object_type ) {
		global $wpdb;

		if ( ! $this->models->has( $object_type ) || ! $this->models->get( $object_type )->forced_table_name() ) {
			return sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $object_type );
		}

		$object_model = $this->models->get( $object_type );

		return sprintf( '%s_%s', $wpdb->prefix, $object_model->forced_table_name() );
	}

	/**
	 * Compose the appropriate join table name for a given suffix.
	 *
	 * @param string $suffix
	 *
	 * @return string
	 */
	private function compose_join_table_name( $suffix ) {
		global $wpdb;

		return sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $suffix );
	}

	/**
	 * Composes a table alias to ensure every table has a unique name in the query.
	 *
	 * @param string $object_name
	 * @param string $parent_table_name
	 *
	 * @return string
	 */
	private function compose_table_alias( $object_name, $parent_table_name = false ) {
		if ( ! empty( $parent_table_name ) ) {
			return sprintf( '%s_%s', $parent_table_name, $object_name );
		}

		return sprintf( 'table_%s', $object_name );
	}

	/**
	 * Search an array of arguments and return the appropriate SQL LIMIT clause from
	 * the values.
	 *
	 * @param array $arguments
	 *
	 * @return string
	 */
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

	/**
	 * Search an array of arguments and compose the appropriate WHERE clause for the values provided.
	 *
	 * @param array  $where_clauses
	 * @param string $table_alias
	 * @param array  $arguments
	 *
	 * @return void
	 */
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
