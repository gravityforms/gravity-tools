<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Runners;

use Gravity_Forms\Gravity_Tools\Hermes\Models\Model;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Insert\Insert_Mutation_Token;

/**
 * A concrete implementation of Runner which handles database operations required
 * to insert a set of objects into the appropriate tables.
 */
class Insert_Runner extends Runner {

	/**
	 * Using the data provided by the Mutation_Token, this determines the correct
	 * DB table for the insertion and adds the records one-by-one.
	 *
	 * This also checks permissions for the object type to ensure that the user has
	 * the appropriate capabilities for running this insert mutation. If any meta fields
	 * are defined in the mutation, those values are inserted into the meta table with
	 * the appropriate values.
	 *
	 * @param Insert_Mutation_Token $mutation
	 * @param Model                 $object_model
	 *
	 * @return void
	 */
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

	/**
	 * Helper method to handle an individual insertion action from the array of objects to insert.
	 *
	 * @param Model $object_model
	 * @param array $categorized_fields
	 *
	 * @return int
	 */
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