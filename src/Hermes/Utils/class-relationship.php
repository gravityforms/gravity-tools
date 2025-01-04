<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Utils;

class Relationship {

	protected $from;
	protected $to;
	protected $cap;
	protected $is_reverse;

	public function __construct( $from, $to, $cap, $is_reverse = false ) {
		$this->from       = $from;
		$this->to         = $to;
		$this->cap        = $cap;
		$this->is_reverse = $is_reverse;
	}

	public function from() {
		return $this->from;
	}

	public function to() {
		return $this->to;
	}

	public function cap() {
		return $this->cap;
	}

	public function has_access() {
		return current_user_can( $this->cap );
	}

	public function get_table_suffix() {
		if ( $this->is_reverse ) {
			return sprintf( '%s_%s', $this->to, $this->from );
		}

		return sprintf( '%s_%s', $this->from, $this->to );
	}

}