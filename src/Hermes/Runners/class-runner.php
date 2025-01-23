<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Runners;

use Gravity_Forms\Gravity_Tools\Hermes\Enum\Field_Type_Validation_Enum;
use Gravity_Forms\Gravity_Tools\Hermes\Models\Model;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutation_Token;

abstract class Runner {

	protected $db_namespace;

	protected $query_handler;

	public function __construct( $db_namespace, $query_handler ) {
		$this->db_namespace  = $db_namespace;
		$this->query_handler = $query_handler;
	}

	/**
	 * @param Mutation_Token $mutation
	 * @param Model          $object_model
	 *
	 * @return mixed
	 */
	abstract public function run( $mutation, $object_model );

	protected function get_field_name_list_from_fields( $fields ) {
		return implode( ', ', array_keys( $fields ) );
	}

	protected function get_field_values_list_from_fields( $fields ) {
		$values = array_values( $fields );
		foreach ( $values as $key => $value ) {
			$values[ $key ] = sprintf( '"%s"', $value );
		}

		return implode( ', ', $values );
	}

	protected function get_update_field_list( $fields ) {
		$pairs = array();

		foreach ( $fields as $key => $value ) {
			if ( $key === 'id' ) {
				continue;
			}
			$pairs[] = sprintf( '%s = "%s"', $key, $value );
		}

		return implode( ', ', $pairs );
	}

	protected function categorize_fields( $object_model, $fields_to_process ) {
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