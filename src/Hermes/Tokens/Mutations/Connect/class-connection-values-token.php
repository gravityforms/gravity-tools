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
	 * The ID of the object to connect from.
	 *
	 * @var string
	 */
	protected $from;

	/**
	 * The ID of the object to connect to.
	 *
	 * @var string
	 */
	protected $to;

	/**
	 * Public accessor for $from.
	 *
	 * @return string
	 */
	public function from() {
		return $this->from;
	}

	/**
	 * Public accessor for $to.
	 *
	 * @return string
	 */
	public function to() {
		return $this->to;
	}

	/**
	 * Return the $to and $from values as children.
	 *
	 * @return array
	 */
	public function children() {
		return array(
			'from' => $this->from,
			'to'   => $this->to,
		);
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
			$fields[ $key ] = trim( $value, '"\' ' );
		}

		if ( ! array_key_exists( 'from', $fields ) || ! array_key_exists( 'to', $fields ) ) {
			throw new \InvalidArgumentException( 'Connect mutations must provide a from and to ID.' );
		}

		$this->from = $fields['from'];
		$this->to   = $fields['to'];
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