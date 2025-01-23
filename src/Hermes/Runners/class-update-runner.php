<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Runners;

class Update_Runner extends Runner {

	public function run( $mutation, $object_model ) {
		$fields_to_update = $mutation->children()->children();

		if ( ! array_key_exists( 'id', $fields_to_update ) ) {
			$error_string = sprintf( 'Update mutations must contain an id in the fields list. Fields provided: %s', json_encode( array_keys( $fields_to_update ) ) );
			throw new \InvalidArgumentException( $error_string );
		}

		$object_id = $fields_to_update['id'];

		$categorized_fields = $this->categorize_fields( $object_model, $fields_to_update );

		$this->handle_single_update( $object_model, $categorized_fields, $object_id );

		$objects_gql = sprintf( '{ %s: %s(id: %s){ %s }', $object_model->type(), $object_model->type(), $object_id, implode( ', ', $mutation->return_fields() ) );

		$data = $this->query_handler->handle_query( $objects_gql );

		wp_send_json_success( $data );
	}

	private function handle_single_update( $object_model, $categorized_fields, $object_id ) {
		global $wpdb;

		$table_name = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $object_model->type() );
		$field_list = $this->get_update_field_list( $categorized_fields['local'] );
		$sql        = sprintf( 'UPDATE %s SET %s WHERE id = "%s"', $table_name, $field_list, $object_id );

		$wpdb->query( $sql );

		if ( ! empty( $categorized_fields['meta'] ) ) {
			foreach ( $categorized_fields['meta'] as $key => $value ) {
				$meta_table_name = sprintf( '%s%s_meta', $wpdb->prefix, $this->db_namespace );

				$delete_sql = sprintf( 'DELETE FROM %s WHERE meta_name = "%s" AND object_id = "%s"', $meta_table_name, $key, $object_id );
				$wpdb->query( $delete_sql );

				$insert_fields_string = 'object_type, object_id, meta_name, meta_value';
				$insert_values_string = sprintf( '"%s", "%s", "%s", "%s"', $object_model->type(), $object_id, $key, $value );
				$meta_sql             = sprintf( 'INSERT INTO %s (%s) VALUES (%s)', $meta_table_name, $insert_fields_string, $insert_values_string );

				$wpdb->query( $meta_sql );
			}
		}

		return $object_id;
	}

}