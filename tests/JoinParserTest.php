<?php

use PHPUnit\Framework\TestCase;
use Gravity_Forms\Gravity_Tools\Query\Where_Parser;
use Gravity_Forms\Gravity_Tools\Query\Join_Parser;

class JoinParserTest extends TestCase {

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
		$this->wpdb = new Join_WPDB_Stub();
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
	public function wheres_parse_correctly( $filters, $expected_sql ) {
		$custom_callbacks = array();
		$where_parser = new Where_Parser( $this->wpdb, $this->queryable, 'extra', $custom_callbacks );

		$join_parser = new Join_Parser( $this->wpdb, $where_parser );
		$sql = $join_parser->process( $filters );

		$this->assertEquals( $expected_sql, $sql );
	}

	public function filter_data_provider() {
		return array(
			// Single filter with single value
			array(
				array(
					'wp_gravitysmtp_event_logs' => array(
						'wp_gravitysmtp_event_logs.event_id' => array( 'wp_gravitysmtp_events.id' ),
					),
				),
				"LEFT JOIN wp_gravitysmtp_event_logs ON ((wp_gravitysmtp_event_logs.event_id = wp_gravitysmtp_events.id))"
			),

			// Multiple filters with single value
			array(
				array(
					'wp_gravitysmtp_event_logs' => array(
						'wp_gravitysmtp_event_logs.event_id' => array( 'wp_gravitysmtp_events.id' ),
						'wp_gravitysmtp_event_logs.foo_id' => array( 'wp_gravitysmtp_events.action_id' ),
					),
				),
				"LEFT JOIN wp_gravitysmtp_event_logs ON ((wp_gravitysmtp_event_logs.event_id = wp_gravitysmtp_events.id) AND (wp_gravitysmtp_event_logs.foo_id = wp_gravitysmtp_events.action_id))"
			),

			// Single filter with multiple values
			array(
				array(
					'wp_gravitysmtp_event_logs' => array(
						'wp_gravitysmtp_event_logs.event_id' => array( 'wp_gravitysmtp_events.id', 'wp_gravitysmtp_events.foobar' ),
					),
				),
				"LEFT JOIN wp_gravitysmtp_event_logs ON ((wp_gravitysmtp_event_logs.event_id = wp_gravitysmtp_events.id OR wp_gravitysmtp_event_logs.event_id = wp_gravitysmtp_events.foobar))"
			),
		);
	}

}

class Join_WPDB_Stub {

	public function prepare( $string, ...$values ) {
		return sprintf( $string, ...$values );
	}

}