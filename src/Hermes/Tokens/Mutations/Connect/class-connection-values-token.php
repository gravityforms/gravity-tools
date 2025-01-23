<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Connect;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Token;

class Connection_Values_Token extends Token {

	protected $type = 'Connection_Values';

	protected $from;

	protected $to;

	public function from() {
		return $this->from;
	}

	public function to() {
		return $this->to;
	}

	public function children() {
		return array(
			'from' => $this->from,
			'to'   => $this->to,
		);
	}

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

	public function regex_types() {
		return array(
			'argument_pair' => '([a-zA-z0-9_-]*):([^,\)]+)',
		);
	}

}