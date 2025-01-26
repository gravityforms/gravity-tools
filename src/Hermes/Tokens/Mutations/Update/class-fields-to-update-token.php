<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Update;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Token;

/**
 * Fields to Update Token
 *
 * A token holding the fields to update during this Mutation.
 */
class Fields_To_Update_Token extends Token {

	protected $type = 'Fields_To_Update';

	/**
	 * The fields to update.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Public accessor for $items.
	 *
	 * @return array
	 */
	public function items() {
		return $this->items;
	}

	/**
	 * Return $items as children.
	 *
	 * @return array
	 */
	public function children() {
		return $this->items();
	}

	/**
	 * Parse the string contents to values.
	 *
	 * @param string $contents
	 *
	 * @return void
	 */
	public function parse( $contents ) {
		preg_match_all( $this->get_parsing_regex(), $contents, $results );

		if ( count( $results ) < 4 ) {
			// Something has gone terrible awry, bail.
			return;
		}

		$fields = array();

		$keys   = $results[1];
		$values = $results[2];

		foreach ( $keys as $idx => $key ) {
			$value          = $values[ $idx ];
			$fields[ $key ] = trim( $value, '"\' ');
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
			'argument_pair' => '([a-zA-z0-9_-]*):([^,\)]+)',
		);
	}

}