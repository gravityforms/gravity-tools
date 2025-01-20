<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Models;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Arguments_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Base_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Data_Object_From_Array_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Field_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Utils\Relationship_Collection;

abstract class Model {

	protected $type = '';

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
		return array();
	}

	/**
	 * @return array
	 */
	public function meta_fields() {
		return array();
	}

	public function has_access() {
		return current_user_can( $this->access_cap );
	}

}