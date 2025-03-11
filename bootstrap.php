<?php

use Gravity_Forms\Gravity_Tools\Hermes\Enum\Field_Type_Validation_Enum;
use tad\FunctionMocker\FunctionMocker;

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

FunctionMocker::init();

/**
 * Bootstrap for WP Unit Tests
 */
require __DIR__ . '/wp-loader.php';
$core_loader = new WpLoader();
$core_loader->init();

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

function gravitytools_tests_reset_db() {
	echo "\r\n";
	echo '=========================================' . "\r\n";
	echo 'Cleaning up test database for next run...' . "\r\n";
	global $wpdb;

	$tables = array(
		'contact',
		'company',
		'group',
		'deal',
		'pipeline',
		'company_contact',
		'group_contact',
		'deal_company',
		'deal_contact',
		'pipeline_deal',
		'meta',
	);

	foreach( $tables as $table ) {
		$table_name = sprintf( '%s%s_%s', $wpdb->prefix, 'gravitycrm', $table );
		$sql = sprintf( 'TRUNCATE TABLE %s', $table_name );
		$wpdb->query( $sql );
	}

	echo 'Done cleaning test database!' . "\r\n";
	echo '=========================================' . "\r\n";
	echo "\r\n";
}