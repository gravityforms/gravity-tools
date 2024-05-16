<?php

use PHPUnit\Framework\TestCase;
use Gravity_Forms\Gravity_Tools\Query\Query_Builder;

class QueryBuilderTest extends TestCase {


	protected $queryable = array(
		'id',
		'date_created',
		'date_updated',
		'service',
		'subject',
		'message',
		'status',
	);

	protected $column_map = array(
		'email_and_headers' => 'extra',
		'content'           => 'message',
		'subject'           => 'subject',
		'all'               => array(
			'subject',
			'message',
			'extra',
		)
	);

	/**
	 * @var \Gravity_Forms\Gravity_Tools\Query\SQL_Parsers
	 */
	protected $parsers;

	public function setUp() {
		parent::setUp();
		$wpdb_multi  = new Query_WPDB_Multi_Stub();
		$wpdb_single = new Query_WPDB_Single_Stub();

		$select_parser = new \Gravity_Forms\Gravity_Tools\Query\Select_Parser( $wpdb_multi );
		$where_parser  = new \Gravity_Forms\Gravity_Tools\Query\Where_Parser( $wpdb_multi, $this->queryable, 'extra', array() );
		$join_parser   = new \Gravity_Forms\Gravity_Tools\Query\Join_Parser( $wpdb_multi, $where_parser );
		$search_parser = new \Gravity_Forms\Gravity_Tools\Query\Search_Parser( $wpdb_single, $this->column_map );
		$order_parser  = new \Gravity_Forms\Gravity_Tools\Query\Order_Parser( $wpdb_multi );
		$limit_parser  = new \Gravity_Forms\Gravity_Tools\Query\Limit_Parser();

		$this->parsers = new \Gravity_Forms\Gravity_Tools\Query\SQL_Parsers( $select_parser, $search_parser, $where_parser, $join_parser, $order_parser, $limit_parser );
	}

	/**
	 * Test that filters are parsed to the correct SQL string.
	 *
	 * @dataProvider value_data_provider
	 *
	 *
	 * @test
	 *
	 * @return void
	 */
	public function queries_build_correctly( $select, $join, $where, $search, $order, $pagination, $expected_sql ) {
		$builder = new Query_Builder( $this->parsers );
		$query   = $builder->query( 'foo_table' );

		$query->select( $select );
		$query->join( $join );
		$query->where( $where );
		$query->search( $search );
		$query->order( $order );
		$query->paginate( $pagination['page'], $pagination['per_page'] );

		$this->assertEquals( $expected_sql, $query->as_sql() );
	}

	public function value_data_provider() {
		return array(

			array(

				// Select
				array(
					'id',
				),

				// Join
				array(
					'wp_gravitysmtp_event_logs' => array(
						'wp_gravitysmtp_event_logs.event_id' => array( 'wp_gravitysmtp_events.id' ),
					),
				),

				// Where
				array(
					'status' => array( 'sent' ),
				),

				// Search
				array(
					'email_and_headers' => array( 'foobar' )
				),

				// Order
				array(
					'date_modified' => 'ASC'
				),

				// Paginate
				array(
					'page' => 2,
					'per_page' => 10,
				),

				// Expected SQL
				"SELECT id FROM foo_table LEFT JOIN wp_gravitysmtp_event_logs ON ((wp_gravitysmtp_event_logs.event_id = wp_gravitysmtp_events.id)) WHERE ((`status` = sent)) AND ((`extra` LIKE '%foobar%')) ORDER BY `date_modified` ASC LIMIT 10 OFFSET 10",
			),


		);
	}

}

class Query_WPDB_Multi_Stub {

	public function prepare( $string, ...$values ) {
		return sprintf( $string, ...$values );
	}

}

class Query_WPDB_Single_Stub {

	public function prepare( $string, $value ) {
		return sprintf( $string, sprintf( "%s", $value ) );
	}

}