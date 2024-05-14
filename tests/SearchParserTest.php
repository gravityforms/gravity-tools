<?php

use PHPUnit\Framework\TestCase;
use Gravity_Forms\Gravity_Tools\Query\Search_Parser;

class SearchParserTest extends TestCase {

	protected $column_map = array(
		'email_and_headers' => 'extra',
		'content' => 'message',
		'subject' => 'subject',
		'all' => array(
			'subject',
			'message',
			'extra',
		)
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
	public function search_parses_correctly( $filters, $trailing_union, $expected_sql ) {
		$parser = new Search_Parser( $this->wpdb, $this->column_map );
		$sql = $parser->process( $filters, $trailing_union );

		$this->assertEquals( $expected_sql, $sql );
	}

	public function filter_data_provider() {
		return array(
			// Single filter with single value and trailing union
			array(
				array(
					'email_and_headers' => array( 'foobar' )
				),
				true,
				"((`extra` LIKE '%foobar%')) AND "
			),

			// Multiple filter with single value and trailing union
			array(
				array(
					'email_and_headers' => array( 'foobar' ),
					'content' => array( 'foobash' )
				),
				true,
				"((`extra` LIKE '%foobar%') AND (`message` LIKE '%foobash%')) AND "
			),

			// Single filter with multiple values and trailing union
			array(
				array(
					'email_and_headers' => array( 'foobar', 'foobash' )
				),
				true,
				"((`extra` LIKE '%foobar%' OR `extra` LIKE '%foobash%')) AND "
			),

			// Single filter with value that maps to multiple.
			array(
				array(
					'all' => array( 'foobar' )
				),
				true,
				"(((`subject` LIKE '%foobar%') OR (`message` LIKE '%foobar%') OR (`extra` LIKE '%foobar%'))) AND "
			),

		);
	}

}

class WPDB_Stub {

	public function prepare( $string, $value ) {
		return sprintf( $string, sprintf( "%s", $value ) );
	}

}