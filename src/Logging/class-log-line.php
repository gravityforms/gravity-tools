<?php

namespace Gravity_Forms\Gravity_Tools\Logging;

class Log_Line {

	protected $timestamp;

	protected $priority;

	protected $message;

	public function __construct( $timestamp, $priority, $message ) {
		$this->timestamp = $timestamp;
		$this->priority  = $priority;
		$this->message   = $message;
	}

	public function timestamp() {
		return $this->timestamp;
	}

	public function priority() {
		return $this->priority;
	}

	public function message() {
		return $this->message;
	}

}