<?php

namespace hermes;

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
		$contact_model = new FakeContactModel();
		$group_model = new FakeGroupModel();

		$model_collection->add( 'contact', $contact_model );
		$model_collection->add( 'group', $group_model );

		$db_namespace = 'gravitycrm';

		$handler = new Query_Handler( $db_namespace, $model_collection );

		$text = file_get_contents( dirname( __FILE__ ) . '/../_data/group_to_contact.graphql' );
		$data = new Query_Token( $text );

		foreach( $data->items() as $object ) {
			$sql = $handler->recursively_generate_sql( $object );
			var_dump( $sql );
		}
	}

	public function testMutationHandler() {
		$model_collection = new Model_Collection();
		$contact_model = new FakeContactModel();
		$group_model = new FakeGroupModel();

		$model_collection->add( 'contact', $contact_model );
		$model_collection->add( 'group', $group_model );

		$db_namespace = 'gravitycrm';

		$query_handler = new Query_Handler( $db_namespace, $model_collection );

		$handler = new Mutation_Handler( $db_namespace, $model_collection, $query_handler );

		$text = '{
  insert_contact(objects: [{first_name: "Foo", last_name: "Bar"}, {first_name: "Bing", last_name: "Bash", secondary_phone: "4445554848" }]) {
    returning {
      id,
      first_name,
      last_name,
      secondary_phone,
    }
  }
}';
		$data = $handler->handle_mutation( $text );


	}

}

class FakeContactModel extends \Gravity_Forms\Gravity_Tools\Hermes\Models\Model {

	protected $type = 'contact';

	protected $fields = array(
		'id',
		'first_name',
		'last_name',
		'email',
		'phone'
	);

	protected $meta_fields = array(
		'secondary_phone',
		'alternate_website'
	);

	protected $access_cap = 'manage_options';

	public function relationships() {
		return new \Gravity_Forms\Gravity_Tools\Hermes\Utils\Relationship_Collection();
	}

}

class FakeGroupModel extends \Gravity_Forms\Gravity_Tools\Hermes\Models\Model {

	protected $type = 'group';

	protected $fields = array(
		'label',
	);

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

}