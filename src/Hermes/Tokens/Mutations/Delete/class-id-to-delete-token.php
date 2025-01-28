<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Delete;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Token;

/**
 * ID To Delete Token
 *
 * Stores the ID to delete for a Delete mutation.
 */
class ID_To_Delete_Token extends Token {

	protected $type = 'ID_To_Update';

	/**
	 * The ID to delete.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Public accessor for $id.
	 *
	 * @return string
	 */
	public function id() {
		return $this->id;
	}

	/**
	 * Return the ID as an array as children.
	 *
	 * @return array
	 */
	public function children() {
		return array( $this->id );
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

		if ( ! array_key_exists( 'id', $fields ) ) {
			throw new \InvalidArgumentException( 'Delete operations must provide a valid ID for deletion.', 495 );
		}

		$this->id = $fields['id'];
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