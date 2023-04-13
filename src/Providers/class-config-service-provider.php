<?php

namespace Gravity_Forms\Gravity_Tools\Providers;

use Gravity_Forms\Gravity_Tools\Service_Container;
use Gravity_Forms\Gravity_Tools\Service_Provider;

/**
 * Class Config_Service_Provider
 *
 * Service provider for the Config Service.
 *
 * @package Gravity_Forms\Gravity_Tools\Providers
 */
abstract class Config_Service_Provider extends GF_Service_Provider {

	// Organizational services
	const CONFIG_COLLECTION = 'config_collection';
	const DATA_PARSER       = 'data_parser';

	/**
	 * Array mapping config class names to their container ID.
	 *
	 * @since 1.0
	 *
	 * @var string[]
	 */
	protected $configs = array();

	/**
	 * Register services to the container.
	 *
	 * @since 1.0
	 *
	 * @param Service_Container $container
	 */
	public function register( Service_Container $container ) {

		// Add to container
		$container->add( self::CONFIG_COLLECTION, function () {
			return new Config_Collection();
		} );

		$container->add( self::DATA_PARSER, function () {
			return new Config_Data_Parser();
		} );

		$container->add( self::GLOBAL_CONFIG, function () {
			return new Config_Global();
		} );

		// Add configs to container.
		$this->register_config_items( $container );
		$this->register_configs_to_collection( $container );
	}

	/**
	 * Initiailize any actions or hooks.
	 *
	 * @since 1.0
	 *
	 * @param Service_Container $container
	 *
	 * @return void
	 */
	public function init( Service_Container $container ) {

		// Need to pass $this to callbacks; save as variable.
		$self = $this;

		add_action( 'wp_enqueue_scripts', function () use ( $container ) {
			$container->get( self::CONFIG_COLLECTION )->handle();
		}, 9999 );

		add_action( 'admin_enqueue_scripts', function () use ( $container ) {
			$container->get( self::CONFIG_COLLECTION )->handle();
		}, 9999 );

		add_action( 'gform_preview_init', function () use ( $container ) {
			$container->get( self::CONFIG_COLLECTION )->handle();
		}, 0 );

		add_action( 'rest_api_init', function () use ( $container, $self ) {
			register_rest_route( 'gravityforms/v2', '/tests/mock-data', array(
				'methods'             => 'GET',
				'callback'            => array( $self, 'config_mocks_endpoint' ),
				'permission_callback' => function () {
					return true;
				},
			) );
		} );
	}

	/**
	 * For each config defined in $configs, instantiate and add to container.
	 *
	 * @since 1.0
	 *
	 * @param Service_Container $container
	 *
	 * @return void
	 */
	private function register_config_items( Service_Container $container ) {
		$parser = $container->get( self::DATA_PARSER );

		foreach ( $this->configs as $name => $class ) {
			$container->add( $name, function () use ( $class, $parser ) {
				return new $class( $parser );
			} );
		}
	}

	/**
	 * Register each config defined in $configs to the GF_Config_Collection.
	 *
	 * @since 1.0
	 *
	 * @param Service_Container $container
	 *
	 * @return void
	 */
	public function register_configs_to_collection( Service_Container $container ) {
		$collection = $container->get( self::CONFIG_COLLECTION );

		foreach ( $this->configs as $name => $config ) {
			$config_class = $container->get( $name );
			$collection->add_config( $config_class );
		}
	}

	/**
	 * Callback for the Config Mocks REST endpoint.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function config_mocks_endpoint() {
		define( 'GFORMS_DOING_MOCK', true );
		$container = \GFForms::get_service_container();
		$data      = $container->get( self::CONFIG_COLLECTION )->handle( false );

		return $data;
	}
}