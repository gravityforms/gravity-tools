<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Tokens;

abstract class Token extends Base_Token {

	public function __construct( $contents ) {
		$this->parse( $contents );
	}

	protected function regex_types() {
		return array();
	}

	protected function get_parsing_regex() {
		$clauses = array();

		foreach ( $this->regex_types() as $type => $pattern ) {
			$clauses[] = sprintf( '(*MARK:%s)%s', $type, $pattern );
		}

		$clauses_concat = implode( '|', $clauses );

		return sprintf( '/(?|%s)/m', $clauses_concat );
	}

	abstract public function parse( $contents );
}