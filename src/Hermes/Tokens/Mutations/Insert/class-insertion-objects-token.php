<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Insert;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Token;

/**
 * Insertion Objects Token
 *
 * A token holding a series of Insertion Object Tokens containing the various items to insert during this mutation.
 */
class Insertion_Objects_Token extends Token {

	protected $type = 'insertion_objects';

	/**
	 * @var Insertion_Object_Token[]
	 */
	protected $objects;

	/**
	 * Return the array of Insertion Object Tokens as children.
	 *
	 * @return Insertion_Object_Token[]
	 */
	public function children() {
		return $this->objects;
	}

	/**
	 * Parse the string contents to values.
	 *
	 * @param string $contents
	 *
	 * @return void
	 */
	public function parse( $contents ) {
		preg_match_all( $this->get_parsing_regex(), $contents, $parts );

		$matches   = $parts[0];
		$marks     = $parts['MARK'];
		$objects      = array();

		while ( ! empty( $matches ) ) {
			$value     = array_shift( $matches );
			$mark_type = array_shift( $marks );

			switch( $mark_type ) {
				case 'object':
					$objects[] = new Insertion_Object_Token( $value );
					break;
				default:
					break;
			}
		}

		$this->objects = $objects;
	}

	/**
	 * The regex types to use while parsing.
	 *
	 * $key represents the MARK type, while the $value represents the REGEX string to use.
	 *
	 * @return string[]
	 */
	public function regex_types() {
		return array(
			'object' => '\{[^\}]+\}',
		);
	}

}