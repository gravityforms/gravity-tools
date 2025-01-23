<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Delete;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Token;

class ID_To_Delete_Token extends Token {

	protected $type = 'ID_To_Update';

	protected $id;

	public function id() {
		return $this->id;
	}

	public function children() {
		return array( $this->id );
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

		if ( ! array_key_exists( 'id', $fields ) ) {
			throw new \InvalidArgumentException( 'Delete operations must provide a valid ID for deletion.' );
		}

		$this->id = $fields['id'];
	}

	public function regex_types() {
		return array(
			'argument_pair' => '([a-zA-z0-9_-]*):([^,\)]+)',
		);
	}

}