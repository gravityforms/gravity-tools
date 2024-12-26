<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Utils;

class Relationship {

	protected $from;
	protected $to;
	protected $cap;

	public function __construct( $from, $to, $cap ) {
		$this->from = $from;
		$this->to = $to;
		$this->cap = $cap;
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

}