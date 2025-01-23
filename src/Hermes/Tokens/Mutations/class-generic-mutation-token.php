<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Insert\Insert_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Update\Update_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Delete\Delete_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Connect\Connect_Mutation_Token;

class Generic_Mutation_Token extends Mutation_Token {

	protected $type = 'generic_mutation';

	protected $mutation_type;

	protected $typed_token;

	public function mutation_type() {
		return $this->mutation_type;
	}

	public function mutation() {
		return $this->typed_token;
	}

	public function parse( $contents ) {
		preg_match_all( $this->get_parsing_regex(), $contents, $parts );
		$matches     = $parts[0];
		$marks       = $parts['MARK'];
		$typed_token = false;

		while ( ! empty( $matches ) ) {
			$value     = array_shift( $matches );
			$mark_type = array_shift( $marks );

			if ( $mark_type !== 'operation' ) {
				continue;
			}

			$cleaned = trim( $value, '{ (' );

			if ( strpos( $cleaned, 'insert_' ) !== false ) {
				$typed_token         = new Insert_Mutation_Token( $contents );
				$this->mutation_type = 'insert';
			}

			if ( strpos( $cleaned, 'update_' ) !== false ) {
				$typed_token         = new Update_Mutation_Token( $contents );
				$this->mutation_type = 'update';
			}

			if ( strpos( $cleaned, 'delete_' ) !== false ) {
				$typed_token         = new Delete_Mutation_Token( $contents );
				$this->mutation_type = 'delete';
			}

			if ( strpos( $cleaned, 'connect_' ) !== false ) {
				$typed_token         = new Connect_Mutation_Token( $contents );
				$this->mutation_type = 'connect';
			}

		}

		if ( empty( $typed_token ) ) {
			throw new \InvalidArgumentException( 'Invalid operation type passed to mutation.' );
		}

		$this->typed_token = $typed_token;
	}

	public function regex_types() {
		return array(
			'operation' => '\{[^\(]+\(',
		);
	}

	public function children() {
		return $this->typed_token;
	}

}