<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Utils;

class Relationship_Collection {

	/**
	 * @var Relationship[]
	 */
	protected $relationships;

	/**
	 * @param Relationship[] $relationships
	 */
	public function __construct( $relationships ) {
		$this->relationships = $relationships;
	}

	public function add( Relationship $relationship ) {
		$this->relationships[] = $relationship;
	}

	public function get( $related_object ) {
		if ( ! $this->has( $related_object ) ) {
			return null;
		}

		$relationship = array_filter( $this->relationships, function( $relationship ) use ( $related_object ) {
			return $relationship->to() === $related_object;
		} );

		return array_shift( $relationship );
	}

	public function has( $related_object ) {
		$relationship = array_filter( $this->relationships, function( $relationship ) use ( $related_object ) {
			return $relationship->to() === $related_object;
		} );

		return ! empty( $relationship );
	}

}