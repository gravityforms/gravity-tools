<?php

namespace Gravity_Forms\Gravity_Tools\Hermes;

use Gravity_Forms\Gravity_Tools\Hermes\Enum\Field_Type_Validation_Enum;
use Gravity_Forms\Gravity_Tools\Hermes\Models\Model;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Data_Object_From_Array_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Field_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Connect_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Delete_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Generic_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Insert_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Update_Mutation_Token;
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

		if ( ! $this->models->has( $mutation->object_type() ) ) {
			$error_message = sprintf( 'Mutation attempted with invalid object type: %s', $mutation->object_type() );
			throw new \InvalidArgumentException( $error_message );
		}

		$object_model = $this->models->get( $mutation->object_type() );

		if ( ! $object_model->has_access() ) {
			$error_message = sprintf( 'Access not allowed for object type %s', $mutation->object_type() );
			throw new \InvalidArgumentException( $error_message );
		}

		switch ( $mutation->operation() ) {
			case 'insert':
				$this->handle_insert_mutation( $mutation, $object_model );
				break;
			case 'update':
				$this->handle_update_mutation( $mutation, $object_model );
				break;
			case 'delete':
				$this->handle_delete_mutation( $mutation, $object_model );
				break;
			case 'connect':
				$this->handle_connect_mutation( $mutation, $object_model );
				break;
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

		$objects_gql = sprintf( '{ %s: %s(id_in: %s){ %s }', $object_model->type(), $object_model->type(), implode( '|', $inserted_ids ), implode( ', ', $mutation->return_fields() ) );

		$data = $this->query_handler->handle_query( $objects_gql );

		wp_send_json_success( $data );
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

	/**
	 * @param Update_Mutation_Token $mutation
	 * @param Model                 $object_model
	 *
	 * @return void
	 */
	public function handle_update_mutation( $mutation, $object_model ) {
		$fields_to_update = $mutation->children()->children();

		if ( ! array_key_exists( 'id', $fields_to_update ) ) {
			$error_string = sprintf( 'Update mutations must contain an id in the fields list. Fields provided: %s', json_encode( array_keys( $fields_to_update ) ) );
			throw new \InvalidArgumentException( $error_string );
		}

		$object_id = $fields_to_update['id'];

		$categorized_fields = $this->categorize_fields( $object_model, $fields_to_update );

		$this->handle_single_update( $object_model, $categorized_fields, $object_id );

		$objects_gql = sprintf( '{ %s: %s(id: %s){ %s }', $object_model->type(), $object_model->type(), $object_id, implode( ', ', $mutation->return_fields() ) );

		$data = $this->query_handler->handle_query( $objects_gql );

		wp_send_json_success( $data );
	}

	private function handle_single_update( $object_model, $categorized_fields, $object_id ) {
		global $wpdb;

		$table_name = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $object_model->type() );
		$field_list = $this->get_update_field_list( $categorized_fields['local'] );
		$sql        = sprintf( 'UPDATE %s SET %s WHERE id = "%s"', $table_name, $field_list, $object_id );

		$wpdb->query( $sql );

		if ( ! empty( $categorized_fields['meta'] ) ) {
			foreach ( $categorized_fields['meta'] as $key => $value ) {
				$meta_table_name = sprintf( '%s%s_meta', $wpdb->prefix, $this->db_namespace );

				$delete_sql = sprintf( 'DELETE FROM %s WHERE meta_name = "%s" AND object_id = "%s"', $meta_table_name, $key, $object_id );
				$wpdb->query( $delete_sql );

				$insert_fields_string = 'object_type, object_id, meta_name, meta_value';
				$insert_values_string = sprintf( '"%s", "%s", "%s", "%s"', $object_model->type(), $object_id, $key, $value );
				$meta_sql             = sprintf( 'INSERT INTO %s (%s) VALUES (%s)', $meta_table_name, $insert_fields_string, $insert_values_string );

				$wpdb->query( $meta_sql );
			}
		}

		return $object_id;
	}

	/**
	 * @param Delete_Mutation_Token $mutation
	 * @param                       $object_model
	 *
	 * @return void
	 */
	private function handle_delete_mutation( $mutation, $object_model ) {
		global $wpdb;

		$id_to_delete = $mutation->id_to_delete();
		$table_name   = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $object_model->type() );
		$delete_sql   = sprintf( 'DELETE FROM %s WHERE id = "%s"', $table_name, $id_to_delete );

		$wpdb->query( $delete_sql );

		wp_send_json_success( array( 'deleted_id' => $id_to_delete ) );
	}

	/**
	 * @param Connect_Mutation_Token $mutation
	 * @param Model                  $object_model
	 *
	 * @return void
	 */
	private function handle_connect_mutation( $mutation, $object_model ) {
		global $wpdb;

		$from_object = $mutation->from_object();
		$to_object   = $mutation->to_object();
		$from_id     = $mutation->from_id();
		$to_id       = $mutation->to_id();

		if ( ! $object_model->relationships()->has( $to_object ) ) {
			$error_message = sprintf( 'Relationship from %s to %s does not exist.', $from_object, $to_object );
			throw new \InvalidArgumentException( $error_message );
		}

		if ( ! $object_model->relationships()->get( $to_object )->has_access() ) {
			$error_message = sprintf( 'Attempting to access forbidden object type %s.', $to_object );
			throw new \InvalidArgumentException( $error_message );
		}

		$table_name = sprintf( '%s%s_%s_%s', $wpdb->prefix, $this->db_namespace, $from_object, $to_object );

		$check_sql = sprintf( 'SELECT * FROM %s WHERE %_id = "%s" AND %s_id = "%s"', $table_name, $from_object, $from_id, $to_object, $to_id );

		$existing = $wpdb->get_results( $check_sql );

		if ( ! empty( $existing ) ) {
			$response = sprintf( 'Connection from %s ID %s to %s ID %s already exists.', $from_object, $from_id, $to_object, $to_id );
			wp_send_json_success( $response );
		}

		$connect_sql = sprintf( 'INSERT INTO %s ( %s_id, %s_id ) VALUES( "%s", "%s" )', $table_name, $from_object, $to_object, $from_id, $to_id );

		$wpdb->query( $connect_sql );

		$response = sprintf( 'Connection from %s ID %s to %s ID %s created.', $from_object, $from_id, $to_object, $to_id );

		wp_send_json_success( $response );
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

	private function get_update_field_list( $fields ) {
		$pairs = array();

		foreach ( $fields as $key => $value ) {
			if ( $key === 'id' ) {
				continue;
			}
			$pairs[] = sprintf( '%s = "%s"', $key, $value );
		}

		return implode( ', ', $pairs );
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
				$validated             = Field_Type_Validation_Enum::validate( $field_validation_type, $value );

				if ( ! is_null( $value ) && is_null( $validated ) ) {
					$field_type_string = is_string( $field_validation_type ) ? $field_validation_type : 'callback';
					$error_string      = sprintf( 'Invalid field value %s sent to field %s with a type of %s.', $value, $field_name, $field_type_string );
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