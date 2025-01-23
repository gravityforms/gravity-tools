<?php

namespace Gravity_Forms\Gravity_Tools\Hermes\Runners;

class Delete_Runner extends Runner {

	public function run( $mutation, $object_model ) {
		global $wpdb;

		$id_to_delete = $mutation->id_to_delete();
		$table_name   = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, $object_model->type() );
		$delete_sql   = sprintf( 'DELETE FROM %s WHERE id = "%s"', $table_name, $id_to_delete );

		$wpdb->query( $delete_sql );

		wp_send_json_success( array( 'deleted_id' => $id_to_delete ) );
	}

}