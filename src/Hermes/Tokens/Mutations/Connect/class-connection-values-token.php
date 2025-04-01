<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Connect;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Token;

/**
 * Connection Values Token
 *
 * Used to hold the IDs to use for a given Connect mutation.
 */
class Connection_Values_Token extends Token {

	protected $type = 'Connection_Values';

	/**
	* An array of sub-arrays, each consisting of a `to` and `from` index.
	*
	* @var array[]
	*/ 
	protected $pairs = array();

	public function pairs() {
		return $this->pairs;
	}

	/**
	 * Return the $to and $from values as children.
	 *
	 * @return array
	 */
	public function children() {
		return $this->pairs;
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

		$pairs  = array();
		$fields = array();

		$keys        = $results[1];
		$values      = $results[2];
		$pairs_index = 0;

		foreach ( $keys as $idx => $key ) {
			$value          = $values[ $idx ];
			$fields[ $key ] = trim( $value, '"\' ' );

			if ( $key === 'to' ) {
				$pairs[ $pairs_index ] = $fields;
				$fields                = array();
				$pairs_index          += 1;
			}
		}

		foreach ( $pairs as $fields ) {
			if ( ! array_key_exists( 'from', $fields ) || ! array_key_exists( 'to', $fields ) ) {
				throw new \InvalidArgumentException( 'Connect mutations must provide a from and to ID.', 485 );
			}
		}

		$this->pairs = $pairs;
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
			'argument_pair' => '([a-zA-z0-9_-]*):([^,\}\)]+)',
		);
	}
}

