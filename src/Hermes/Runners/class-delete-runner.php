<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Runners;

use Gravity_Forms\Gravity_Tools\Hermes\Models\Model;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Delete\Delete_Mutation_Token;

/**
 * A concrete implementation of Runner which handles database operations for
 * deleting an object from the appropriate tables.
 */
class Delete_Runner extends Runner {

	/**
	 * Using the data provided by the Mutation_Token, this determines the correct
	 * DB table for the deletion action and removes the record.
	 *
	 *
	 * @param Delete_Mutation_Token $mutation
	 * @param Model                  $object_model
	 *
	 * @return void
	 */
	public function run( $mutation, $object_model ) {
		global $wpdb;

		$id_to_delete = $mutation->id_to_delete();
		$table_name   = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $object_model->type() );
		$delete_sql   = sprintf( 'DELETE FROM %s WHERE id = "%s"', $table_name, $id_to_delete );

		$wpdb->query( $delete_sql );

		wp_send_json_success( array( 'deleted_id' => $id_to_delete ) );
	}

}