<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutation_Token;

class Delete_Mutation_Token extends Mutation_Token {

	protected $operation = 'delete';

	/**
	 * @var ID_To_Delete_Token
	 */
	protected $id_to_delete;

	public function id_to_delete() {
		return $this->id_to_delete->id();
	}

	public function children() {
		return $this->id_to_delete->children();
	}

	public function parse( $contents ) {
		preg_match_all( $this->get_parsing_regex(), $contents, $results );
		$this->tokenize( $results );

		return;
	}

	public function tokenize( $parts ) {
		$matches = $parts[0];
		$marks   = $parts['MARK'];
		$data    = array();

		while ( ! empty( $matches ) ) {
			$value     = array_shift( $matches );
			$mark_type = array_shift( $marks );

			switch ( $mark_type ) {
				case 'operation_alias':
					$data['object_type'] = str_replace( 'delete_', '', $value );
					$data['alias']       = $value;
					break;
				case 'arg_group':
					$data['id_to_delete'] = new ID_To_Delete_Token( $value );
					break;
				case 'alias':
					$has_alias = $value;
					break;
			}
		}

		if ( empty( $data['id_to_delete'] ) || empty( $data['object_type'] ) ) {
			throw new \InvalidArgumentException( 'Delete payload malformed. Check values and try again.' );
		}

		$this->set_properties( $data );
	}

	protected function set_properties( $data ) {
		$this->alias        = $data['alias'];
		$this->object_type  = $data['object_type'];
		$this->id_to_delete = $data['id_to_delete'];
	}

	protected function regex_types() {
		return array(
			'operation_alias' => 'delete_[^\(]*',
			'arg_group'       => '\([^\)]+\)',
			'alias'           => '[_A-Za-z][_0-9A-Za-z]*:',
			'open_bracket'    => '{',
			'close_bracket'   => '}',
		);
	}

}