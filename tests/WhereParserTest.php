<?php

use PHPUnit\Framework\TestCase;
use Gravity_Forms\Gravity_Tools\Query\Where_Parser;

class WhereParserTest extends TestCase {

	protected $queryable = array(
		'id',
		'date_created',
		'date_updated',
		'service',
		'subject',
		'message',
		'status',
	);

	protected $wpdb;

	public function setUp() {
		parent::setUp();
		$this->wpdb = new WPDB_Stub();
	}

	/**
	 * Test that filters are parsed to the correct SQL string.
	 *
	 * @dataProvider filter_data_provider
	 *
	 * @param $filters
	 * @param $expected_sql
	 *
	 * @test
	 *
	 * @return void
	 */
	public function wheres_parse_correctly( $filters, $trailing_union, $expected_sql ) {
		$custom_callbacks = array(
			'attachments' => arraY( $this, 'attachments_column_callback' ),
		);
		$parser = new Where_Parser( $this->wpdb, $this->queryable, 'extra', $custom_callbacks );
		$sql = $parser->process( $filters, $trailing_union );

		$this->assertEquals( $expected_sql, $sql );
	}

	public function attachments_column_callback( $value, $parser ) {
		$inverter    = $value === 'no' ? null : 'NOT';
		return '`' . $parser->default_column . '` ' . $inverter . ' LIKE "%\"attachments\";a:0:%"';
	}

	public function filter_data_provider() {
		return array(
			// Single filter with single value and trailing union
			array(
				array(
					'status' => array( 'sent' ),
				),
				true,
				"((`status` = 'sent')) AND "
			),

			// Single filter with single value and NO trailing union
			array(
				array(
					'status' => array( 'sent' ),
				),
				false,
				"((`status` = 'sent'))"
			),

			// Multiple filters with single values and trailing union
			array(
				array(
					'status' => array( 'sent' ),
					'service' => array( 'google' ),
				),
				true,
				"((`status` = 'sent') AND (`service` = 'google')) AND "
			),

			// Multiple filters with single values and NO trailing union
			array(
				array(
					'status' => array( 'sent' ),
					'service' => array( 'google' ),
				),
				false,
				"((`status` = 'sent') AND (`service` = 'google'))"
			),

			// Multiple filters with multiple values and trailing union
			array(
				array(
					'status' => array( 'sent', 'failed' ),
					'service' => array( 'google', 'microsoft' ),
				),
				true,
				"((`status` = 'sent' OR `status` = 'failed') AND (`service` = 'google' OR `service` = 'microsoft')) AND "
			),

			// Multiple filters with multiple values and NO trailing union
			array(
				array(
					'status' => array( 'sent', 'failed' ),
					'service' => array( 'google', 'microsoft' ),
				),
				false,
				"((`status` = 'sent' OR `status` = 'failed') AND (`service` = 'google' OR `service` = 'microsoft'))"
			),

			// Multiple filters with multiple values and NO trailing union, include attachments
			array(
				array(
					'status' => array( 'sent', 'failed' ),
					'service' => array( 'google', 'microsoft' ),
					'attachments' => array( 'no' ),
				),
				false,
				"((`status` = 'sent' OR `status` = 'failed') AND (`service` = 'google' OR `service` = 'microsoft') AND (`extra`  LIKE \"%\\\"attachments\\\";a:0:%\"))"
			),

		);
	}

}

class WPDB_Stub {

	public function prepare( $string, $value ) {
		return sprintf( $string, sprintf( "'%s'", $value ) );
	}

}