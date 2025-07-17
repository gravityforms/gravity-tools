<?php

namespace Gravity_Forms\Gravity_Tools\Utils;

use ArrayAccess;

class Bettarray implements ArrayAccess {

	protected $array;

	public function __construct( array $array ) {
		$this->array = $array;
	}

	public function data() {
		return $this->array;
	}

	public function offsetExists( mixed $offset ): bool {
		return isset( $this->array[ $offset ] );
	}

	public function offsetGet( mixed $offset ): mixed {
		return $this->get_nested_value( $offset );
	}

	public function offsetSet( mixed $offset, mixed $value ): void {
		if ( is_null( $offset ) ) {
			$this->array[] = $value;
			return;
		}

		$this->update_nested_value( $offset, $value );
	}

	public function offsetUnset( mixed $offset ): void {
		$this->delete_nested_value( $offset );
	}

	public function get( $key ) {
		return $this->get_nested_value( $key );
	}

	public function set( $key, $value ) {
		return $this->update_nested_value( $key, $value );
	}

	public function delete( $key ) {
		return $this->delete_nested_value( $key );
	}

	public function append( $key, $value ) {
		$existing = $this->get_nested_value( $key );

		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		$existing[] = $value;

		$this->update_nested_value( $key, $existing );
	}

	public function amend( $key, $value ) {
		$existing = $this->get_nested_value( $key );

		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		$existing = array_merge( $existing, $value );

		$this->update_nested_value( $key, $existing );
	}

	private function get_nested_value( $key ) {
		$current = $this->array;
		$key_arr = explode( '.', $key );

		while ( count( $key_arr ) ) {
			$new_key = array_shift( $key_arr );
			$current = isset( $current[ $new_key ] ) ? $current[ $new_key ] : array();
		}

		return $current;
	}

	private function update_nested_value( $key, $value ) {
		$current = &$this->array;
		$key_arr = explode( '.', $key );

		while ( count( $key_arr ) ) {
			$new_key = array_shift( $key_arr );

			if ( ! isset( $current[ $new_key ] ) ) {
				$current[ $new_key ] = array();
			}

			$current = &$current[ $new_key ];
		}

		$current = $value;
	}

	private function delete_nested_value( $key ) {
		$current = &$this->array;
		$key_arr = explode( '.', $key );

		while ( count( $key_arr ) > 1 ) {
			$new_key = array_shift( $key_arr );

			if ( ! isset( $current[ $new_key ] ) ) {
				$current[ $new_key ] = array();
			}

			$current = &$current[ $new_key ];
		}

		$new_key = array_shift( $key_arr );

		unset( $current[ $new_key ] );
	}
}
