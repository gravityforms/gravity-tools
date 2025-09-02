<?php

namespace utils;

use Gravity_Forms\Gravity_Tools\Utils\Beltloop;
use PHPUnit\Framework\TestCase;

class BeltloopTest extends TestCase {

	public function testOrdering() {
		$data = array(
			array(
				'id'               => 'seven',
				'prevId' => 'six',
			),
			array(
				'id'               => 'one',
				'prevId' => null,
			),
			array(
				'id'               => 'three',
				'prevId' => 'two',
			),
			array(
				'id'               => 'five',
				'prevId' => 'four',
			),
			array(
				'id'               => 'six',
				'prevId' => 'five',
			),
			array(
				'id'               => 'two',
				'prevId' => 'one',
			),
			array(
				'id'               => 'four',
				'prevId' => 'three',
			),
		);

		$expected = array(
			array(
				'id' => 'one',
				'prevId' => null,
			),
			array(
				'id' => 'two',
				'prevId' => 'one',
			),
			array(
				'id' => 'three',
				'prevId' => 'two',
			),
			array(
				'id' => 'four',
				'prevId' => 'three',
			),
			array(
				'id' => 'five',
				'prevId' => 'four',
			),
			array(
				'id' => 'six',
				'prevId' => 'five',
			),
			array(
				'id' => 'seven',
				'prevId' => 'six',
			),
		);

		$sorted = Beltloop::sort( $data );

		$this->assertEquals( $expected, $sorted );
	}

	public function testSortWithMissingItems() {
		$data = array(
			array(
				'id'               => 'eight',
				'prevId' => 'seven',
			),
			array(
				'id' => 'nine',
				'prevId' => 'eight',
			),
			array(
				'id'               => 'three',
				'prevId' => 'two',
			),
			array(
				'id'               => 'five',
				'prevId' => 'four',
			),
			array(
				'id'               => 'six',
				'prevId' => 'five',
			),
			array(
				'id'               => 'two',
				'prevId' => 'one',
			),
			array(
				'id'               => 'one',
				'prevId' => null,
			),
			array(
				'id'               => 'four',
				'prevId' => 'three',
			),
		);

		$expected = array(
			array(
				'id' => 'one',
				'prevId' => null,
			),
			array(
				'id' => 'two',
				'prevId' => 'one',
			),
			array(
				'id' => 'three',
				'prevId' => 'two',
			),
			array(
				'id' => 'four',
				'prevId' => 'three',
			),
			array(
				'id' => 'five',
				'prevId' => 'four',
			),
			array(
				'id' => 'six',
				'prevId' => 'five',
			),
			array(
				'id' => 'eight',
				'prevId' => 'seven',
			),
			array(
				'id' => 'nine',
				'prevId' => 'eight',
			),
		);

		$sorted = Beltloop::sort( $data );
		$this->assertEquals( $expected, $sorted );
	}

	public function testPartialSort() {
		$full_list = array(
			array(
				'id' => 'one',
				'prevId' => null,
			),
			array(
				'id' => 'two',
				'prevId' => 'one',
			),
			array(
				'id' => 'three',
				'prevId' => 'two',
			),
			array(
				'id' => 'four',
				'prevId' => 'three',
			),
			array(
				'id' => 'five',
				'prevId' => 'four',
			),
			array(
				'id' => 'six',
				'prevId' => 'five',
			),
			array(
				'id' => 'seven',
				'prevId' => 'six',
			),
		);

		$partial_list = array(
			array(
				'id' => 'seven',
				'prevId' => 'six',
			),
			array(
				'id' => 'four',
				'prevId' => 'three',
			),
			array(
				'id' => 'one',
				'prevId' => null,
			),
			array(
				'id' => 'five',
				'prevId' => 'four',
			),
		);

		$expected = array(
			array(
				'id' => 'one',
				'prevId' => null,
			),
			array(
				'id' => 'four',
				'prevId' => 'three',
			),
			array(
				'id' => 'five',
				'prevId' => 'four',
			),
			array(
				'id' => 'seven',
				'prevId' => 'six',
			),
		);

		$result = Beltloop::partial_sort( $full_list, $partial_list );

		$this->assertEquals( $expected, $result );
	}
}
