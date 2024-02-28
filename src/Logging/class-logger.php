<?php

namespace Gravity_Forms\Gravity_Tools\Logging;

/**
 * Logger
 *
 * Provides an abstract for dealing with logging. Takes a $provider class (to handle actually writing to a log/db/etc),
 * and must define its own methods for whether to log, and how to delete said log.
 */
abstract class Logger {

	/**
	 * @var Logging_Provider
	 */
	protected $provider;

	abstract protected function should_log();

	abstract protected function delete_log();

	public function __construct( Logging_Provider $provider ) {
		$this->provider = $provider;
	}

	public function log( $message, $priority ) {
		return $this->provider->log( $message, $priority );
	}

	public function log_info( $message ) {
		return $this->provider->log_info( $message );
	}

	public function log_debug( $message ) {
		return $this->provider->log_debug( $message );
	}

	public function log_warning( $message ) {
		return $this->provider->log_warning( $message );
	}

	public function log_error( $message ) {
		return $this->provider->log_error( $message );
	}

	public function log_fatal( $message ) {
		return $this->provider->log_fatal( $message );
	}

}