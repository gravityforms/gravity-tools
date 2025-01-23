<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Insert;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Token;

class Insertion_Object_Token extends Token {

	protected $type = 'insertion_object';

	protected $items;

	public function children() {
		return $this->items;
	}

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

	public function regex_types() {
		return array(
			'value' => ':[^,\}]+',
			'key'   => '[a-zA-Z0-9_\-]+',
		);
	}

}