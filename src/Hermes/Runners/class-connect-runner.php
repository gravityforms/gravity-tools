<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Runners;

use Gravity_Forms\Gravity_Tools\Hermes\Models\Model;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Connect\Connect_Mutation_Token;

/**
 * A concrete implementation of Runner which handles database operations connecting
 * two object types to one another via a lookup table.
 */
class Connect_Runner extends Runner {

	/**
	 * Using the data provided by the Mutation_Token, this determines the correct
	 * DB table for the connection and inserts the appropriate IDs into the table.
	 *
	 * This also checks permissions for both object types to ensure that the user has
	 * the appropriate capabilities for running this connect mutation.
	 *
	 *
	 * @param Connect_Mutation_Token $mutation
	 * @param Model                  $object_model
	 *
	 * @return void
	 */
	public function run( $mutation, $object_model ) {
		global $wpdb;

		$from_object = $mutation->from_object();
		$to_object   = $mutation->to_object();
		$from_id     = $mutation->from_id();
		$to_id       = $mutation->to_id();

		if ( ! $object_model->relationships()->has( $to_object ) ) {
			$error_message = sprintf( 'Relationship from %s to %s does not exist.', $from_object, $to_object );
			throw new \InvalidArgumentException( $error_message, 455 );
		}

		if ( ! $object_model->relationships()->get( $to_object )->has_access() ) {
			$error_message = sprintf( 'Attempting to access forbidden object type %s.', $to_object );
			throw new \InvalidArgumentException( $error_message, 403 );
		}

		$table_name = sprintf( '%s%s_%s_%s', $wpdb->prefix, $this->db_namespace, $from_object, $to_object );

		$check_sql = sprintf( 'SELECT * FROM %s WHERE %_id = "%s" AND %s_id = "%s"', $table_name, $from_object, $from_id, $to_object, $to_id );

		$existing = $wpdb->get_results( $check_sql );

		if ( ! empty( $existing ) ) {
			$response = sprintf( 'Connection from %s ID %s to %s ID %s already exists.', $from_object, $from_id, $to_object, $to_id );
			wp_send_json_success( $response );
		}

		$connect_sql = sprintf( 'INSERT INTO %s ( %s_id, %s_id ) VALUES( "%s", "%s" )', $table_name, $from_object, $to_object, $from_id, $to_id );

		$wpdb->query( $connect_sql );

		$response = sprintf( 'Connection from %s ID %s to %s ID %s created.', $from_object, $from_id, $to_object, $to_id );

		wp_send_json_success( $response );
	}

}