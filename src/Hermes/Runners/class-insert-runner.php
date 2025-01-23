<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Runners;

class Insert_Runner extends Runner {

	public function run( $mutation, $object_model ) {
		$insertion_objects = $mutation->children();
		$inserted_ids      = array();

		foreach ( $insertion_objects->children() as $object ) {
			$fields             = $object->children();
			$categorized_fields = $this->categorize_fields( $object_model, $fields );
			$inserted_id        = $this->handle_single_insert( $object_model, $categorized_fields );
			$inserted_ids[]     = $inserted_id;
		}

		$objects_gql = sprintf( '{ %s: %s(id_in: %s){ %s }', $object_model->type(), $object_model->type(), implode( '|', $inserted_ids ), implode( ', ', $mutation->return_fields() ) );

		$data = $this->query_handler->handle_query( $objects_gql );

		wp_send_json_success( $data );
	}

	private function handle_single_insert( $object_model, $categorized_fields ) {
		global $wpdb;

		$table_name  = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $object_model->type() );
		$field_list  = $this->get_field_name_list_from_fields( $categorized_fields['local'] );
		$values_list = $this->get_field_values_list_from_fields( $categorized_fields['local'] );
		$sql         = sprintf( 'INSERT INTO %s (%s) VALUES (%s)', $table_name, $field_list, $values_list );

		$wpdb->query( $sql );
		$object_id = $wpdb->insert_id;

		if ( ! empty( $categorized_fields['meta'] ) ) {
			foreach ( $categorized_fields['meta'] as $key => $value ) {
				$meta_table_name      = sprintf( '%s%s_meta', $wpdb->prefix, $this->db_namespace );
				$insert_fields_string = 'object_type, object_id, meta_name, meta_value';
				$insert_values_string = sprintf( '"%s", "%s", "%s", "%s"', $object_model->type(), $object_id, $key, $value );
				$meta_sql             = sprintf( 'INSERT INTO %s (%s) VALUES (%s)', $meta_table_name, $insert_fields_string, $insert_values_string );

				$wpdb->query( $meta_sql );
			}
		}

		return $object_id;
	}
}