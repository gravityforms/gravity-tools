<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Insert;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Token;

/**
 * Insertion Object Token
 *
 * A token holding the values for an object to be inserted into the Database.
 */
class Insertion_Object_Token extends Token {

	protected $type = 'insertion_object';

	/**
	 * The fields to insert for this object.
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * Return $items as children.
	 *
	 * @return array
	 */
	public function children() {
		return $this->items;
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

		$matches = $parts[0];
		$marks   = $parts['MARK'];

		$fields  = array();
		$key     = false;

		while ( ! empty( $matches ) ) {
			$value     = array_shift( $matches );
			$mark_type = array_shift( $marks );

			switch ( $mark_type ) {
				case 'key':
					$key = trim( $value, ': ' );
					break;
				case 'value':
					if ( $key ) {
						$fields[ $key ] = trim( $value, ': "' );
						$key            = false;
					}
					break;
				default:
					break;
			}
		}

		$this->items = $fields;
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
			'value' => ':[^,\}]+',
			'key'   => '[a-zA-Z0-9_\-]+',
		);
	}

}