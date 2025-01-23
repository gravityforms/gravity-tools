<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Insert;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Token;

class Insertion_Objects_Token extends Token {

	protected $type = 'insertion_objects';

	/**
	 * @var Insertion_Object_Token[]
	 */
	protected $objects;

	public function children() {
		return $this->objects;
	}

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


	public function regex_types() {
		return array(
			'object' => '\{[^\}]+\}',
		);
	}

}