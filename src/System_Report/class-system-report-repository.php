<?php

namespace Gravity_Forms\Gravity_Tools\System_Report;

class System_Report_Repository {

	/**
	 * @var System_Report_Group[]
	 */
	protected $groups = array();

	private static $instance;

	public static function instance() {
		if ( ! is_null( self::$instance ) ) {
			return self::$instance;
		}

		return new self();
	}

	public function all() {
		return $this->groups;
	}

	public function get( $name ) {
		if ( ! isset( $this->groups[ $name ] ) ) {
			return null;
		}

		return $this->groups[ $name ];
	}

	public function delete( $name ) {
		unset( $this->groups[ $name ] );
	}

	public function has( $name ) {
		if ( ! isset( $this->groups[ $name ] ) ) {
			return false;
		}

		return true;
	}

	public function add( $name, System_Report_Group $group ) {
		$this->groups[ $name ] = $group;
	}

	public function as_array() {
		$response = array();

		foreach( $this->groups as $name => $group ) {
			$response[ $name ] = $group->as_array();
		}

		return $response;
	}
}
