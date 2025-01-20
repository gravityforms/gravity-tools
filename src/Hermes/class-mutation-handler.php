<?php

namespace Gravity_Forms\Gravity_Tools\Hermes;

use Gravity_Forms\Gravity_Tools\Hermes\Enum\Field_Type_Validation_Enum;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Data_Object_From_Array_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Field_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Generic_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Insert_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Query_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Utils\Model_Collection;

class Mutation_Handler {

	/**
	 * @var string
	 */
	protected $db_namespace;

	/**
	 * @var Model_Collection
	 */
	protected $models;

	/**
	 * @var Query_Handler
	 */
	protected $query_handler;

	public function __construct( $db_namespace, $models, $query_handler ) {
		$this->db_namespace  = $db_namespace;
		$this->models        = $models;
		$this->query_handler = $query_handler;
	}

	public function handle_mutation( $mutation_string ) {
		global $wpdb;

		$generic_mutation = new Generic_Mutation_Token( $mutation_string );

		/**
		 * Mutation_Token $mutation
		 */
		$mutation = $generic_mutation->mutation();

		$object_model = $this->models->get( $mutation->object_type() );

		if ( ! $object_model->has_access() ) {
			$error_message = sprintf( 'Access not allowed for object type %s', $object_type );
			throw new \InvalidArgumentException( $error_message );
		}

		switch ( $mutation->operation() ) {
			case 'insert':
				$this->handle_insert_mutation( $mutation, $object_model );
				break;
			case 'update':
			case 'delete':
			default:
				break;
		}
	}

	/**
	 * @param Insert_Mutation_Token $mutation
	 * @param                       $object_model
	 *
	 * @return void
	 */
	public function handle_insert_mutation( $mutation, $object_model ) {
		$insertion_objects = $mutation->children();
		$inserted_ids      = array();

		foreach ( $insertion_objects->children() as $object ) {
			$fields             = $object->children();
			$categorized_fields = $this->categorize_fields( $object_model, $fields );
			$inserted_id        = $this->handle_single_insert( $object_model, $categorized_fields );
			$inserted_ids[]     = $inserted_id;
		}

		$objects_gql = sprintf( '{ contact: contact(id_in: %s){ %s }', implode( '|', $inserted_ids ), implode( ', ', $mutation->return_fields() ) );

		$data = $this->query_handler->handle_query( $objects_gql );

//		wp_send_json_success( $data );
	}

	public function handle_single_insert( $object_model, $categorized_fields ) {
		global $wpdb;

		$table_name  = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $object_model->type() );
		$field_list  = $this->get_field_name_list_from_fields( $categorized_fields['local'] );
		$values_list = $this->get_field_values_list_from_fields( $categorized_fields['local'] );
		$sql         = sprintf( 'INSERT INTO %s (%s) VALUES (%s)', $table_name, $field_list, $values_list );

		$wpdb->query( $sql );
		$object_id = $wpdb->insert_id;

		if ( ! empty( $categorized_fields['meta'] ) ) {
			foreach ( $categorized_fields['meta'] as $key => $value ) {
				$meta_table_name      = sprintf( '%s%s_meta', $wpdb->prefix, $this->db_namespace );
				$insert_fields_string = 'object_type, object_id, meta_name, meta_value';
				$insert_values_string = sprintf( '"%s", "%s", "%s", "%s"', $object_model->type(), $object_id, $key, $value );
				$meta_sql             = sprintf( 'INSERT INTO %s (%s) VALUES (%s)', $meta_table_name, $insert_fields_string, $insert_values_string );

				$wpdb->query( $meta_sql );
			}
		}

		return $object_id;
	}

	private function get_field_name_list_from_fields( $fields ) {
		return implode( ', ', array_keys( $fields ) );
	}

	private function get_field_values_list_from_fields( $fields ) {
		$values = array_values( $fields );
		foreach ( $values as $key => $value ) {
			$values[ $key ] = sprintf( '"%s"', $value );
		}

		return implode( ', ', $values );
	}

	private function categorize_fields( $object_model, $fields_to_process ) {
		$categorized = array(
			'meta'  => array(),
			'local' => array(),
		);

		foreach ( $fields_to_process as $field_name => $value ) {
			if ( ! array_key_exists( $field_name, $object_model->fields() ) && ! array_key_exists( $field_name, $object_model->meta_fields() ) ) {
				$error_string = sprintf( 'Attempting to access invalid field %s on object type %s', $field_name, $object_model->type() );
				throw new \InvalidArgumentException( $error_string );
			}

			if ( array_key_exists( $field_name, $object_model->fields() ) ) {
				$field_validation_type = $object_model->fields()[ $field_name ];
				$validated = Field_Type_Validation_Enum::validate( $field_validation_type, $value );

				if ( ! is_null( $value ) && is_null( $validated ) ) {
					$field_type_string = is_string( $field_validation_type ) ? $field_validation_type : 'callback';
					$error_string = sprintf( 'Invalid field value %s sent to field %s with a type of %s.', $value, $field_name, $field_type_string );
					throw new \InvalidArgumentException( $error_string );
				}

				$categorized['local'][ $field_name ] = $validated;
			}

			if ( array_key_exists( $field_name, $object_model->meta_fields() ) ) {
				$field_validation_type = $object_model->meta_fields()[ $field_name ];

				$validated = Field_Type_Validation_Enum::validate( $field_validation_type, $value );

				if ( ! is_null( $value ) && is_null( $validated ) ) {
					$error_string = sprintf( 'Invalid field value %s sent to field %s with a type of %s.', $value, $field_name, (string) $field_validation_type );
					throw new \InvalidArgumentException( $error_string );
				}

				$categorized['meta'][ $field_name ] = $validated;
			}
		}

		return $categorized;
	}

}