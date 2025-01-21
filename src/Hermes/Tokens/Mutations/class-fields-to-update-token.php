<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Token;

class Fields_To_Update_Token extends Token {

	protected $type = 'Fields_To_Update';

	protected $items = array();

	public function items() {
		return $this->items;
	}

	public function children() {
		return $this->items();
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
			$fields[ $key ] = trim( $value, '"\' ');
		}

		$this->items = $fields;
	}

	public function regex_types() {
		return array(
			'argument_pair' => '([a-zA-z0-9_-]*):([^,\)]+)',
		);
	}

}