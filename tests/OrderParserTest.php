<?php

use PHPUnit\Framework\TestCase;
use Gravity_Forms\Gravity_Tools\Query\Order_Parser;

class OrderParserTest extends TestCase {

	protected $wpdb;

	public function setUp() {
		parent::setUp();
		$this->wpdb = new Order_WPDB_Stub();
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
	public function search_parses_correctly( $filters, $expected_sql ) {
		$parser = new Order_Parser( $this->wpdb );
		$sql = $parser->process( $filters );

		$this->assertEquals( $expected_sql, $sql );
	}

	public function filter_data_provider() {
		return array(

			// Single column
			array(
				array(
					'date_modified' => 'ASC'
				),
				'ORDER BY `date_modified` ASC',
			),

			// Multiple columns
			array(
				array(
					'date_modified' => 'ASC',
					'id' => 'DESC',
				),
				'ORDER BY `date_modified` ASC, `id` DESC',
			)
		);
	}

}

class Order_WPDB_Stub {

	public function prepare( $string, ...$values ) {
		return sprintf( $string, ...$values );
	}

}