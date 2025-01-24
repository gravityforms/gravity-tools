<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Models;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Arguments_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Base_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Data_Object_From_Array_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Field_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Utils\Relationship_Collection;

/**
 * Model
 *
 * This provides the base abstract contract for Models in Hermes. A Model is responsible
 * for defining all of the fields, relationships, and permissions surrounding a given
 * object/data type.
 */
abstract class Model {

	/**
	 * The type of object this model represents.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * The minimum capability required for a given user to be able to access
	 * this object's data.
	 *
	 * @var string
	 */
	protected $access_cap = '';

	/**
	 * Concrete Models must implement the public ::relationships() method
	 * in order to define any relationships this object type may have.
	 *
	 * @return Relationship_Collection
	 */
	abstract public function relationships();

	/**
	 * Accessor for the type property
	 *
	 * @return string
	 */
	public function type() {
		return $this->type;
	}

	/**
	 * Concrete Models must define the specific Fields and Field Types this
	 * object type has. Any request regarding this object type will use this
	 * array to determine if a given field should be evaluated, and how to
	 * validate it.
	 *
	 * In the Database design, tables for this object type should have columns
	 * that strictly match the field names outlined here. For instance, if a field
	 * called "first_name" is defined here, a column of "first_name" must exist in
	 * the DB table for this object.
	 *
	 * @return array
	 */
	public function fields() {
		return array();
	}

	/**
	 * Concrete Models must define the specific Fields and Field Types this object
	 * type supports for *meta* fields. Meta fields differ from typical fields in that
	 * they do not require specific columns in the base Database table but are rather stored
	 * in a meta table. This allows for flexibility in adding new fields to a given object type
	 * after code has been deployed to production while avoiding having to modify DB tables.
	 *
	 * @return array
	 */
	public function meta_fields() {
		return array();
	}

	/**
	 * Checks the current user's access for this object type.Typically this should
	 * not be overwritten in the concrete Model, except for cases in which custom
	 * access functionality is required.
	 *
	 * @return bool
	 */
	public function has_access() {
		return current_user_can( $this->access_cap );
	}

}