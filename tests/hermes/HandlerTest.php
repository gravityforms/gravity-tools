<?php

namespace hermes;

use Gravity_Forms\Gravity_Tools\Hermes\Enum\Field_Type_Validation_Enum;
use Gravity_Forms\Gravity_Tools\Hermes\Mutation_Handler;
use Gravity_Forms\Gravity_Tools\Hermes\Query_Handler;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Query_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Utils\Model_Collection;
use PHPUnit\Framework\TestCase;

global $wpdb;

$wpdb = new fakeWPDB();

class HandlerTest extends TestCase {

	public function testDataObjectParsesToSQL() {
		$model_collection = new Model_Collection();
		$contact_model    = new FakeContactModel();
		$group_model      = new FakeGroupModel();

		$model_collection->add( 'contact', $contact_model );
		$model_collection->add( 'group', $group_model );

		$db_namespace = 'gravitycrm';

		$handler = new Query_Handler( $db_namespace, $model_collection );

		$text = file_get_contents( dirname( __FILE__ ) . '/../_data/group_to_contact.graphql' );
		$data = new Query_Token( $text );

		foreach ( $data->items() as $object ) {
			$sql = $handler->recursively_generate_sql( $object );
			var_dump( $sql );
		}
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
		$model_collection = new Model_Collection();
		$contact_model    = new FakeContactModel();
		$group_model      = new FakeGroupModel();

		$model_collection->add( 'contact', $contact_model );
		$model_collection->add( 'group', $group_model );

		$db_namespace = 'gravitycrm';

		$query_handler = new Query_Handler( $db_namespace, $model_collection );

		$handler = new Mutation_Handler( $db_namespace, $model_collection, $query_handler );

		try {
			$data = $handler->handle_mutation( $text );
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

			// Valid custom callback
			array(
				'{
  insert_contact(objects: [{foobar: "foo", first_name: true, last_name: "Bar"}, {first_name: "Bing", last_name: "Bash", secondary_phone: "4445554848" }]) {
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
		);
	}

}

class FakeContactModel extends \Gravity_Forms\Gravity_Tools\Hermes\Models\Model {

	protected $type = 'contact';

	protected $access_cap = 'manage_options';

	public function fields() {
		return array(
			'id'         => Field_Type_Validation_Enum::INT,
			'first_name' => Field_Type_Validation_Enum::STRING,
			'last_name'  => Field_Type_Validation_Enum::STRING,
			'email'      => Field_Type_Validation_Enum::EMAIL,
			'phone'      => Field_Type_Validation_Enum::STRING,
			'foobar'     => function ( $value ) {
				if ( $value === 'foo' ) {
					return 'foo';
				}

				return null;
			},
		);
	}

	public function meta_fields() {
		return array(
			'secondary_phone'   => Field_Type_Validation_Enum::STRING,
			'alternate_website' => Field_Type_Validation_Enum::STRING,
		);
	}

	public function relationships() {
		return new \Gravity_Forms\Gravity_Tools\Hermes\Utils\Relationship_Collection();
	}

}

class FakeGroupModel extends \Gravity_Forms\Gravity_Tools\Hermes\Models\Model {

	protected $type = 'group';

	protected $fields = array(
		'label',
	);

	public function fields() {
		return array(
			'label' => Field_Type_Validation_Enum::STRING,
		);
	}

	protected $access_cap = 'manage_options';

	public function relationships() {
		return new \Gravity_Forms\Gravity_Tools\Hermes\Utils\Relationship_Collection(
			array(
				new \Gravity_Forms\Gravity_Tools\Hermes\Utils\Relationship( 'group', 'contact', 'manage_options' )
			)
		);
	}

}

class fakeWPDB {

	public $prefix = 'wp_';

	public $insert_id = 1;

	public function prepare( $string, ...$args ) {
		return sprintf( $string, ...$args );
	}

	public function query( $query ) {
		return;
	}

	public function get_results( $query ) {
		return array();
	}

}