<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutation_Token;

class Insert_Mutation_Token extends Mutation_Token {

	protected $operation = 'insert';

	protected $return_fields = array();

	/**
	 * @var Insertion_Objects_Token
	 */
	protected $objects_to_insert;

	public function return_fields() {
		return $this->return_fields;
	}

	public function parse( $contents ) {
		preg_match_all( $this->get_parsing_regex(), $contents, $results );
		$this->tokenize( $results );

		return;
	}

	public function tokenize( $parts ) {
		$matches = $parts[0];
		$marks   = $parts['MARK'];
		$data    = array(
			'return_fields' => array(),
		);

		$next_is_return = false;

		while ( ! empty( $matches ) ) {
			$value     = array_shift( $matches );
			$mark_type = array_shift( $marks );

			switch ( $mark_type ) {
				case 'returning_def':
					$next_is_return = true;
					break;
				case 'operation_alias':
					$data['object_type'] = str_replace( 'insert_', '', $value );
					$data['alias']       = $value;
					break;
				case 'arg_group':
					$data['objects_to_insert'] = new Insertion_Objects_Token( $value );
					break;
				case 'alias':
					$has_alias = $value;
					break;
				case 'identifier':
					if ( ! $next_is_return ) {
						break;
					}

					$data['return_fields'][] = $value;
					break;
				case 'close_bracket':
					if ( $next_is_return ) {
						$next_is_return = false;
						break;
					} else {
						$this->set_properties( $data );

						return;
					}
			}
		}

		$this->set_properties( $data );
	}

	public function children() {
		return $this->objects_to_insert;
	}

	protected function set_properties( $data ) {
		$this->alias             = $data['alias'];
		$this->object_type       = $data['object_type'];
		$this->objects_to_insert = $data['objects_to_insert'];
		$this->return_fields     = $data['return_fields'];
	}

	protected function regex_types() {
		return array(
			'returning_def'   => 'returning',
			'operation_alias' => 'insert_[^\(]*',
			'arg_group'       => '\([^\)]+\)',
			'alias'           => '[_A-Za-z][_0-9A-Za-z]*:',
			'identifier'      => '[_A-Za-z][_0-9A-Za-z]*',
			'open_bracket'    => '{',
			'close_bracket'   => '}',
		);
	}

}