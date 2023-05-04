<?php

namespace Gravity_Forms\Gravity_Tools\Endpoints;

abstract class Endpoint {

	protected $required_params = array();

	abstract public function handle();

	protected function get_nonce_name() {
		return -1;
	}

	protected function validate() {
		check_ajax_referer( $this->get_nonce_name(), 'security' );

		foreach( $this->required_params as $param ) {
			if ( ! isset( $_REQUEST[ $param ] ) ) {
				return false;
			}
		}

		return true;
	}

}