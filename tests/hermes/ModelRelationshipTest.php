<?php

use PHPUnit\Framework\TestCase;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Query_Token;

function current_user_can( $cap ) {
	return true;
}

global $wpdb;

$wpdb = new fakeWPDB();

class ModelRelationshipTest extends TestCase {

	public function testRelationshipsParseToSQL() {
		$group_model = new FakeGroupModel();
		$contact_model = new FakeContactModel();
		$name_args = array(
			'name' => 'first_name',
			'alias' => 'first_name',
		);
		$email_args = array(
			'name' => 'email',
			'alias' => 'email',
		);
		$meta_args = array(
			'name' => 'secondary_phone',
			'alias' => 'phone_two',
		);

		$matches = array();
		$marks = array();

		$fields = array(
			new \Gravity_Forms\Gravity_Tools\Hermes\Tokens\Field_Token( $matches, $marks, $name_args ),
			new \Gravity_Forms\Gravity_Tools\Hermes\Tokens\Field_Token( $matches, $marks, $email_args ),
			new \Gravity_Forms\Gravity_Tools\Hermes\Tokens\Field_Token( $matches, $marks, $meta_args ),
		);

		$conditions = array(
			array(
				'key' => 'id',
				'value' => 1,
				'comparator' => '=',
			),
		);

		$query = $group_model->get_relationship_query( 1, new FakeContactModel(), $fields, $conditions );

		$this->assertEquals( 'SELECT mt.first_name AS first_name, mt.email AS email, meta_secondary_phone.meta_value AS phone_two FROM wp_gravitycrm_contact AS mt LEFT JOIN wp_gravitycrm_group_contact AS pt ON pt.contact_id = mt.id LEFT JOIN wp_gravitycrm_meta AS meta_secondary_phone ON meta_secondary_phone.object_id = mt.id AND meta_secondary_phone.object_type = contact AND meta_secondary_phone.meta_name = secondary_phone WHERE mt.id = 1;', $query );
	}

}

class FakeContactModel extends \Gravity_Forms\Gravity_Tools\Hermes\Models\Model {

	protected $type = 'contact';

	protected $fields = array(
		'first_name',
		'email',
		'website'
	);

	protected $meta_fields = array(
		'secondary_phone',
		'alternate_website'
	);

	protected $access_cap = 'manage_options';

	public function relationships() {
		return new \Gravity_Forms\Gravity_Tools\Hermes\Utils\Relationship_Collection(
			array(
				new \Gravity_Forms\Gravity_Tools\Hermes\Utils\Relationship( 'contact', 'group', 'manage_options' )
			)
		);
	}

}

class FakeGroupModel extends \Gravity_Forms\Gravity_Tools\Hermes\Models\Model {

	protected $type = 'group';

	protected $fields = array(
		'name',
	);

	protected $meta_fields = array(
		'custom_meta_field'
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

	public $prefix = 'wp_gravitycrm_';

	public function prepare( $string, ...$args ) {
		return sprintf( $string, ...$args );
	}

}