<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Runners;

class Connect_Runner extends Runner {

	public function run( $mutation, $object_model ) {
		global $wpdb;

		$from_object = $mutation->from_object();
		$to_object   = $mutation->to_object();
		$from_id     = $mutation->from_id();
		$to_id       = $mutation->to_id();

		if ( ! $object_model->relationships()->has( $to_object ) ) {
			$error_message = sprintf( 'Relationship from %s to %s does not exist.', $from_object, $to_object );
			throw new \InvalidArgumentException( $error_message );
		}

		if ( ! $object_model->relationships()->get( $to_object )->has_access() ) {
			$error_message = sprintf( 'Attempting to access forbidden object type %s.', $to_object );
			throw new \InvalidArgumentException( $error_message );
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