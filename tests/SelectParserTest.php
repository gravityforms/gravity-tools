<?php

use PHPUnit\Framework\TestCase;
use Gravity_Forms\Gravity_Tools\Query\Select_Parser;

class SelectParserTest extends TestCase {

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
	public function search_parses_correctly( $filters, $expected_sql ) {
		$parser = new Select_Parser( $this->wpdb );
		$sql = $parser->process( $filters );

		$this->assertEquals( $expected_sql, $sql );
	}

	public function filter_data_provider() {
		return array(

			// Single column
			array(
				array(
					'id',
				),
				'SELECT id',
			),

			// Multiple columns
			array(
				array(
					'id',
					'date_modified',
					'message'
				),
				'SELECT id, date_modified, message',
			),

			// Multiple columns with db identifiers
			array(
				array(
					'id',
					'date_modified',
					array(
						'second',
						'message'
					)
				),
				'SELECT id, date_modified, second.message',
			),

			// Empty
			array(
				array(),
				'SELECT *',
			),
		);
	}

}

class WPDB_Stub {

	public function prepare( $string, ...$values ) {
		return sprintf( $string, ...$values );
	}

}