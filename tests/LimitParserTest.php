<?php

use PHPUnit\Framework\TestCase;
use Gravity_Forms\Gravity_Tools\Query\Limit_Parser;

class LimitParserTest extends TestCase {

	/**
	 * Test that filters are parsed to the correct SQL string.
	 *
	 * @dataProvider value_data_provider
	 *
	 * @param $page
	 * @param $per_page
	 * @param $expected_sql
	 *
	 * @test
	 *
	 * @return void
	 */
	public function wheres_parse_correctly( $page, $per_page, $expected_sql ) {
		$limit_parser = new Limit_Parser();
		$sql          = $limit_parser->process( $page, $per_page );

		$this->assertEquals( $expected_sql, $sql );
	}

	public function value_data_provider() {
		return array(
			array(
				1,
				10,
				'LIMIT 10 OFFSET 0',
			),

			array(
				3,
				25,
				'LIMIT 25 OFFSET 50',
			),

			array(
				10,
				100,
				'LIMIT 100 OFFSET 900',
			),

			array(
				5,
				6,
				'LIMIT 6 OFFSET 24',
			),

			array(
				"10",
				"10",
				'LIMIT 10 OFFSET 90',
			),

			array(
				"string",
				"string",
				'LIMIT 0 OFFSET 0'
			),

			array(
				0,
				10,
				'LIMIT 10 OFFSET 0'
			),

			array(
				-1,
				10,
				'LIMIT 10 OFFSET 0'
			)
		);
	}

}