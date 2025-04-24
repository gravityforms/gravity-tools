<?php

namespace Gravity_Forms\Gravity_Tools\Data_Import;

class Record {

	/**
	 * @var array
	 */
	protected $data;

	public function __construct( $data ) {
		$this->data = $data;
	}

	public function data() {
		return $this->data;
	}

	public function value_for_key( $key ) {
		if ( ! $this->has_key( $key ) ) {
			throw new \InvalidArgumentException( sprintf( 'Key %s does not exist on current record.', $key ) );
		}

		return $this->data[ $key ];
	}

	public function keys() {
		return array_keys( $this->data );
	}

	public function has_key( $key ) {
		return array_key_exists( $key, $this->data );
	}
}
