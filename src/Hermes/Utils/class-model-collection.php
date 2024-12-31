<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Utils;

use Gravity_Forms\Gravity_Tools\Hermes\Models\Model;

class Model_Collection {

	/**
	 * @var Model[]
	 */
	protected $models = array();

	public function add( $type, Model $model ) {
		$this->models[ $type ] = $model;
	}

	public function remove( $type ) {
		unset( $this->models[ $type ] );
	}

	public function has( $type ) {
		return array_key_exists( $type, $this->models );
	}

	public function get( $type ) {
		if ( ! $this->has( $type ) ) {
			return null;
		}

		return $this->models[ $type ];
	}

}