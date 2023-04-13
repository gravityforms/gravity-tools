<?php

namespace Gravity_Forms\Gravity_Tools\Apps;

trait Registers_Apps {

	//----------------------------------------
	//---------- App Registration ------------
	//----------------------------------------

	/**
	 * Register a JS app with the given arguments.
	 *
	 * @since 1.0
	 *
	 * @param array $args
	 */
	public function register_app( $args ) {
		$config = new GF_App_Config( $this->container->get( GF_Config_Service_Provider::DATA_PARSER ) );
		$config->set_data( $args );

		$this->container->get( GF_Config_Service_Provider::CONFIG_COLLECTION )->add_config( $config );

		$should_display = is_callable( $args['enqueue'] ) ? call_user_func( $args['enqueue'] ) : $args['enqueue'];

		if ( ! $should_display ) {
			return;
		}

		if ( ! empty( $args['css'] ) ) {
			$this->enqueue_app_css( $args );
		}

		if ( ! empty( $args['root_element'] ) ) {
			$this->add_root_element( $args['root_element'] );
		}
	}

	/**
	 * Enqueue the CSS assets for the app.
	 *
	 * @since 1.0
	 *
	 * @param $args
	 */
	protected function enqueue_app_css( $args ) {
		$css_asset = $args['css'];

		add_action( 'wp_enqueue_scripts', function () use ( $css_asset ) {
			call_user_func_array( 'wp_enqueue_style', $css_asset );
		} );

		add_action( 'admin_enqueue_scripts', function () use ( $css_asset ) {
			call_user_func_array( 'wp_enqueue_style', $css_asset );
		} );
	}

	/**
	 * Add the root element to the footer output for bootstrapping.
	 *
	 * @since 1.0
	 *
	 * @param string $root
	 */
	protected function add_root_element( $root ) {
		add_action( 'admin_footer', function() use ( $root ) {
			echo '<div data-js="' . $root . '"></div>';
		}, 10, 0 );
	}

}