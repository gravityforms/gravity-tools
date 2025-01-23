<?php

namespace hermes;

use Gravity_Forms\Gravity_Tools\Hermes\Enum\Field_Type_Validation_Enum;
use Gravity_Forms\Gravity_Tools\Hermes\Mutation_Handler;
use Gravity_Forms\Gravity_Tools\Hermes\Query_Handler;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Connect_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Delete_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Insert_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Update_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Query_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Utils\Model_Collection;
use PHPUnit\Framework\TestCase;
use tad\FunctionMocker\FunctionMocker;

global $wpdb;

class HandlerTest extends TestCase {

	protected $model_collection;
	protected $contact_model;
	protected $group_model;
	protected $query_handler;
	protected $mutation_handler;
	protected $db_namespace;

	public function setUp() {
		$this->model_collection = new Model_Collection();
		$this->contact_model    = new \FakeContactModel();
		$this->group_model      = new \FakeGroupModel();

		$this->model_collection->add( 'contact', $this->contact_model );
		$this->model_collection->add( 'group', $this->group_model );

		$this->db_namespace = 'gravitycrm';

		$this->query_handler = new Query_Handler( $this->db_namespace, $this->model_collection );

		$runners = array(
			'insert'  => new Insert_Runner( $this->db_namespace, $this->query_handler ),
			'delete'  => new Delete_Runner( $this->db_namespace, $this->query_handler ),
			'connect' => new Connect_Runner( $this->db_namespace, $this->query_handler ),
			'update'  => new Update_Runner( $this->db_namespace, $this->query_handler ),
		);

		$this->mutation_handler = new Mutation_Handler( $this->db_namespace, $this->model_collection, $this->query_handler, $runners );
	}

	/**
	 * @dataProvider mutationHandlerProvider
	 *
	 * @param $text
	 * @param $expected
	 *
	 * @return void
	 */
	public function testMutationHandler( $text, $expected ) {

		try {
			$data = $this->mutation_handler->handle_mutation( $text );
		} catch ( \Exception $e ) {
			$this->assertEquals( $expected, 'failure' );

			return;
		}

		$this->assertEquals( $expected, 'success' );
	}

	public function mutationHandlerProvider() {
		return array(
			// Valid
			array(
				'{
  insert_contact(objects: [{email: "foo@bar.com", first_name: "Foo", last_name: "Bar"}, {first_name: "Bing", last_name: "Bash", secondary_phone: "4445554848" }]) {
    returning {
      id,
      first_name,
      last_name,
      secondary_phone,
    }
  }
}',
				'success',
			),

			// Invalid email field
			array(
				'{
  insert_contact(objects: [{email: "foo@bar", first_name: "Foo", last_name: "Bar"}, {first_name: "Bing", last_name: "Bash", secondary_phone: "4445554848" }]) {
    returning {
      id,
      first_name,
      last_name,
      secondary_phone,
    }
  }
}',
				'failure',
			),

			// Invalid custom callback
			array(
				'{
  insert_contact(objects: [{foobar: "bar", first_name: true, last_name: "Bar"}, {first_name: "Bing", last_name: "Bash", secondary_phone: "4445554848" }]) {
    returning {
      id,
      first_name,
      last_name,
      secondary_phone,
    }
  }
}',
				'failure',
			),

			// Valid update
			array(
				'{
  update_contact(id: 1, email: "foo@bar.com", first_name: "Foo", last_name: "Bar", secondary_phone: "4445554848") {
    returning {
      id,
      first_name,
      last_name,
      secondary_phone,
    }
  }
}',
				'success',
			),

			// Update missing ID
			array(
				'{
  update_contact( email: "foo@bar.com", first_name: "Foo", last_name: "Bar", secondary_phone: "4445554848") {
    returning {
      id,
      first_name,
      last_name,
      secondary_phone,
    }
  }
}',
				'failure',
			),

			// Delete
			array(
				'{
  delete_contact(id: 1) {
  }
}',
				'success',
			),

			// Delete missing ID
			array(
				'{
  delete_contact() {
  }
}',
				'failure',
			),

			// Delete missing valid object type
			array(
				'{
  delete_invalid_object(id: 1) {
  }
}',
				'failure',
			),

			// Connect
			array(
				'{
  connect_group_contact(from: 1, to: 2) {
  }
}',
				'success',
			),
		);
	}

	public function testInsertMutation() {
		global $wpdb;
		\gravitytools_tests_reset_db();

		$text = '{
  insert_contact(objects: [{email: "foo@bar.com", first_name: "Foo", last_name: "Bar"}, {first_name: "Bing", last_name: "Bash", secondary_phone: "4445554848" }]) {
    returning {
      id,
      first_name,
      last_name,
      secondary_phone,
    }
  }
}';

		$this->mutation_handler->handle_mutation( $text );
		$table_name      = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, 'contact' );
		$meta_table_name = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, 'meta' );

		$check_query = sprintf( 'SELECT * FROM %s', $table_name );
		$results     = $wpdb->get_results( $check_query, ARRAY_A );

		$this->assertEquals( 2, count( $results ) );

		$record = $results[1];

		$this->assertEquals( 'Bing', $record['first_name'] );

		$meta_check_query = sprintf( 'SELECT meta_value FROM %s WHERE object_id = "%s" AND meta_name = "%s"', $meta_table_name, $record['id'], 'secondary_phone' );
		$meta_results     = $wpdb->get_results( $meta_check_query, ARRAY_A );

		$this->assertEquals( '4445554848', $meta_results[0]['meta_value'] );
	}

	public function testUpdateMutation() {
		global $wpdb;
		\gravitytools_tests_reset_db();

		$table_name      = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, 'contact' );
		$meta_table_name = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, 'meta' );

		$insert_query = sprintf( 'INSERT INTO %s (first_name, last_name, email, phone) VALUES ("Test", "User", "test@gravity.local", "5556665656" )', $table_name );
		$wpdb->query( $insert_query );

		$insert_meta_query = sprintf( 'INSERT INTO %s (meta_name, meta_value, object_type, object_id) VALUES ("secondary_phone", "4445554545", "contact", "1" )', $meta_table_name );
		$wpdb->query( $insert_meta_query );

		$text = '{
  update_contact(id: 1, email: "foo@bar.com", first_name: "Foo", last_name: "Bar", secondary_phone: "4445554848") {
    returning {
      id,
      first_name,
      last_name,
      secondary_phone,
    }
  }
}';

		$this->mutation_handler->handle_mutation( $text );

		$check_query = sprintf( 'SELECT * FROM %s', $table_name );
		$results     = $wpdb->get_results( $check_query, ARRAY_A );

		$this->assertEquals( 1, count( $results ) );

		$check_query = sprintf( 'SELECT * FROM %s WHERE id = "%s"', $table_name, 1 );
		$results     = $wpdb->get_results( $check_query, ARRAY_A );

		$record = $results[0];

		$this->assertEquals( 'Foo', $record['first_name'] );
		$this->assertEquals( 'Bar', $record['last_name'] );
		$this->assertEquals( 'foo@bar.com', $record['email'] );

		$meta_check_query = sprintf( 'SELECT meta_value FROM %s WHERE object_id = "%s" AND meta_name = "%s"', $meta_table_name, $record['id'], 'secondary_phone' );
		$meta_results     = $wpdb->get_results( $meta_check_query, ARRAY_A );

		$this->assertEquals( '4445554848', $meta_results[0]['meta_value'] );

	}

	public function testDeleteMutation() {
		global $wpdb;
		\gravitytools_tests_reset_db();

		$table_name      = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, 'contact' );
		$meta_table_name = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, 'meta' );

		$insert_query = sprintf( 'INSERT INTO %s (first_name, last_name, email, phone) VALUES ("Test", "User", "test@gravity.local", "5556665656" )', $table_name );
		$wpdb->query( $insert_query );

		$text = '{
  delete_contact(id: 1) {}
}';

		$check_query = sprintf( 'SELECT * FROM %s', $table_name );
		$results     = $wpdb->get_results( $check_query, ARRAY_A );

		$this->assertEquals( 1, count( $results ) );

		$this->mutation_handler->handle_mutation( $text );

		$check_query = sprintf( 'SELECT * FROM %s', $table_name );
		$results     = $wpdb->get_results( $check_query, ARRAY_A );

		$this->assertEquals( 0, count( $results ) );
	}

	public function testConnectMutation() {
		global $wpdb;
		\gravitytools_tests_reset_db();

		$contact_table_name = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, 'contact' );
		$group_table_name   = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, 'group' );
		$connect_table_name = sprintf( '%s%s_%s', $wpdb->prefix, $this->db_namespace, 'group_contact' );

		$insert_query = sprintf( 'INSERT INTO %s (first_name, last_name, email, phone) VALUES ("Test", "User", "test@gravity.local", "5556665656" )', $contact_table_name );
		$wpdb->query( $insert_query );

		$insert_query = sprintf( 'INSERT INTO %s (label) VALUES ("Test Group")', $group_table_name );
		$wpdb->query( $insert_query );

		$text = '{
  connect_group_contact(from: 1, to: 1) {}
}';

		$check_query = sprintf( 'SELECT * FROM %s', $connect_table_name );
		$results     = $wpdb->get_results( $check_query, ARRAY_A );

		$this->assertEquals( 0, count( $results ) );

		$this->mutation_handler->handle_mutation( $text );

		$check_query = sprintf( 'SELECT * FROM %s', $connect_table_name );
		$results     = $wpdb->get_results( $check_query, ARRAY_A );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $results[0]['group_id'] );
		$this->assertEquals( 1, $results[0]['contact_id'] );
	}

}
