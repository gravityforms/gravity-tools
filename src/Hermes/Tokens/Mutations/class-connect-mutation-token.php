<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutation_Token;

class Connect_Mutation_Token extends Mutation_Token {

	protected $operation = 'connect';

	/**
	 * @var Connection_Values_Token
	 */
	protected $connection_ids;

	protected $from_object;

	protected $to_object;

	public function from_object() {
		return $this->from_object;
	}

	public function to_object() {
		return $this->to_object;
	}

	public function from_id() {
		return $this->connection_ids->from();
	}

	public function to_id() {
		return $this->connection_ids->to();
	}

	public function children() {
		return array(
			'from_object' => $this->from_object(),
			'from_id'     => $this->from_id(),
			'to_object'   => $this->to_object(),
			'to_id'       => $this->to_id(),
		);
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
					$objects      = str_replace( 'connect_', '', $value );
					$object_parts = explode( '_', $objects );

					if ( count( $object_parts ) !== 2 ) {
						throw new \InvalidArgumentException( 'Error parsing connection object types.' );
					}

					$data['from_object'] = $object_parts[0];
					$data['to_object']   = $object_parts[1];
					$data['alias']       = $value;
					break;
				case 'arg_group':
					$data['connection_ids'] = new Connection_Values_Token( $value );
					break;
				case 'alias':
					$has_alias = $value;
					break;
			}
		}

		if ( empty( $data['connection_ids'] ) || empty( $data['from_object'] ) ) {
			throw new \InvalidArgumentException( 'Connect payload malformed. Check values and try again.' );
		}

		$this->set_properties( $data );
	}

	protected function set_properties( $data ) {
		$this->alias          = $data['alias'];
		$this->connection_ids = $data['connection_ids'];
		$this->object_type    = $data['from_object'];
		$this->from_object    = $data['from_object'];
		$this->to_object      = $data['to_object'];
	}

	protected function regex_types() {
		return array(
			'operation_alias' => 'connect_[^\(]*',
			'arg_group'       => '\([^\)]+\)',
			'alias'           => '[_A-Za-z][_0-9A-Za-z]*:',
			'open_bracket'    => '{',
			'close_bracket'   => '}',
		);
	}

}