<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Enum;

class Field_Type_Validation_Enum {

	const STRING = 'string';
	const INT    = 'int';
	const FLOAT  = 'float';
	const DATE   = 'date';
	const OBJECT = 'object';
	const ARR    = 'array';
	const EMAIL  = 'email';


	private static function validation_map() {
		return array(
			self::STRING => array( self, 'validate_string' ),
			self::INT    => array( self, 'validate_int' ),
			self::FLOAT  => array( self, 'validate_float' ),
			self::DATE   => array( self, 'validate_date' ),
			self::OBJECT => array( self, 'validate_object' ),
			self::ARR    => array( self, 'validate_array' ),
			self::EMAIL  => array( self, 'validate_email' ),
		);
	}

	public static function validate( $type, $value ) {
		if ( ! is_string( $type ) && is_callable( $type ) ) {
			return call_user_func( $type, $value );
		}

		if ( ! array_key_exists( $type, self::validation_map() ) ) {
			$error_string = sprintf( 'Field type %s is not valid.', $type );
			throw new \InvalidArgumentException( $error_string );
		}

		$validated_value = call_user_func( self::validation_map()[ $type ], $value );

		return $validated_value;
	}

	public static function validate_string( $value ) {
		if ( ! is_string( $value ) ) {
			return null;
		}

		return addslashes( $value );
	}

	public static function validate_int( $value ) {
		if ( ! is_numeric( $value ) ) {
			return null;
		}

		return (integer) $value;
	}

	public static function validate_float( $value ) {
		if ( ! is_numeric( $value ) ) {
			return null;
		}

		return (float) $value;
	}

	public static function validate_date( $value ) {
		if ( ! is_string( $value ) ) {
			return null;
		}

		$check = strtotime( $value );

		if ( ! $check ) {
			return null;
		}

		return $value;
	}

	public static function validate_object( $value ) {
		return $value;
	}

	public static function validate_array( $value ) {
		return $value;
	}

	public static function validate_email( $value ) {
		return filter_var( $value, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE );
	}

}