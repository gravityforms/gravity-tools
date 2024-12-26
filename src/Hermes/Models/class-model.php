<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Models;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Arguments_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Base_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Data_Object_From_Array_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Field_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Utils\Relationship_Collection;

abstract class Model {

	protected $type = '';

	protected $fields = array();

	protected $meta_fields = array();

	protected $access_cap = '';

	/**
	 * @return Relationship_Collection
	 */
	abstract public function relationships();

	public function type() {
		return $this->type;
	}

	/**
	 * @return array
	 */
	public function fields() {
		return $this->fields;
	}

	/**
	 * @return array
	 */
	public function meta_fields() {
		return $this->meta_fields;
	}

	public function get_relationship_query( $this_id, Model $related_object, $fields, $conditions ) {
		if ( ! $this->relationships()->has( $related_object->type() ) ) {
			$error_string = sprintf( 'Invalid relationship requested from %s to %s.', $this->type, $related_object->type() );
			throw new \InvalidArgumentException( $error_string );
		}

		if ( ! $this->relationships()->get( $related_object->type() )->has_access() ) {
			$error_string = sprintf( 'Access to %s not allowed.', $related_object->type() );
		}

		global $wpdb;

		$meta_fields  = array();
		$local_fields = array();

		foreach ( $fields as $field ) {
			// Field is a relationship. Skip for now.
			if ( ! is_a( $field, Field_Token::class ) ) {
				continue;
			}

			$field_name = $field->name();

			if ( ! in_array( $field_name, $related_object->fields() ) && ! in_array( $field_name, $related_object->meta_fields() ) ) {
				$error_string = sprintf( 'Attempting to access invalid field %s on object type %s', $field_name, $related_object->type() );
				throw new \InvalidArgumentException( $error_string );
			}

			$alias = $field->alias();

			if ( in_array( $field_name, $related_object->fields() ) ) {
				$local_fields[ $field_name ] = $alias ? $alias : $field_name;
			}

			if ( in_array( $field_name, $related_object->meta_fields() ) ) {
				$meta_fields[ $field_name ] = $alias ? $alias : $field_name;
			}
		}

		$field_select_clauses = $this->build_local_fields_query( $local_fields );
		$field_join_clauses   = array();

		foreach ( $meta_fields as $field_name => $field_alias ) {
			$clauses                = $this->build_meta_query( $field_name, $field_alias, $related_object->type() );
			$field_select_clauses[] = $clauses['select_clause'];
			$field_join_clauses[]   = $clauses['join_clause'];
		}

		$where_clauses = false;

		if ( ! empty( $conditions ) ) {
			$where_clauses = $this->build_where_clauses( $conditions );
		}

		$select_string = implode( ', ', $field_select_clauses );
		$join_string   = implode( ' ', $field_join_clauses );

		$where_string = ! empty( $where_clauses ) ? 'WHERE ' . implode( ' AND ', $where_clauses ) : null;

		$relationship_id_string = sprintf( '%s_id', $related_object->type() );
		$pivot_table_name       = $wpdb->prefix . $this->type . '_' . $related_object->type();
		$related_table_name     = $wpdb->prefix . $related_object->type();

		return $wpdb->prepare( "SELECT %s FROM %s AS mt LEFT JOIN %s AS pt ON pt.%s = mt.id %s %s;", $select_string, $related_table_name, $pivot_table_name, $relationship_id_string, $join_string, $where_string );
	}

	protected function build_local_fields_query( $field_names ) {
		$clauses = array();

		foreach ( $field_names as $field_name => $alias ) {
			$clauses[] = sprintf( 'mt.%s AS %s', $field_name, $alias );
		}

		return $clauses;
	}

	protected function build_meta_query( $meta_name, $alias, $object_type, $is_pivot = false ) {
		global $wpdb;

		$meta_table_name = $wpdb->prefix . 'meta';
		$meta_alias      = sprintf( 'meta_%s', $meta_name );
		$main_table      = $is_pivot ? 'pt' : 'mt';

		$select_clause = $wpdb->prepare(
			'%s.meta_value AS %s',
			$meta_alias,
			$alias
		);

		$join_clause = $wpdb->prepare(
			'LEFT JOIN %s AS %s ON %s.object_id = %s.id AND %s.object_type = %s AND %s.meta_name = %s',
			$meta_table_name,
			$meta_alias,
			$meta_alias,
			$main_table,
			$meta_alias,
			$object_type,
			$meta_alias,
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

			$clauses[] = $wpdb->prepare( 'mt.%1$s %2$s %3$s', $column_name, $comparator, $column_value );
		}

		return $clauses;
	}

}