<?php

namespace system_report;

use Gravity_Forms\Gravity_Tools\System_Report\System_Report_Group;
use Gravity_Forms\Gravity_Tools\System_Report\System_Report_Item;
use Gravity_Forms\Gravity_Tools\System_Report\System_Report_Repository;
use PHPUnit\Framework\TestCase;

class SystemReportTest extends TestCase {

	public function testAddGroup() {
		$repo  = System_Report_Repository::instance();
		$group = new System_Report_Group();

		$repo->add( 'foobar', $group );

		$retrieve = $repo->get( 'foobar' );

		$this->assertEquals( $group, $retrieve );
	}

	public function testHasGroup() {
		$repo  = System_Report_Repository::instance();
		$group = new System_Report_Group();
		$repo->add( 'foobar', $group );
		$this->assertTrue( $repo->has( 'foobar' ) );
	}

	public function testDeleteGroup() {
		$repo  = System_Report_Repository::instance();
		$group = new System_Report_Group();
		$repo->add( 'foobar', $group );
		$this->assertTrue( $repo->has( 'foobar' ) );

		$repo->delete( 'foobar' );

		$this->assertFalse( $repo->has( 'foobar' ) );
	}

	public function testAddItem() {
		$group = new System_Report_Group();
		$item = new System_Report_Item( 'foo', 'bar' );

		$group->add( 'foobar', $item );

		$retrieved = $group->get( 'foobar' );

		$this->assertEquals( $retrieved, $item );
	}

	public function testHasItem() {
		$group = new System_Report_Group();
		$item = new System_Report_Item( 'foo', 'bar' );

		$group->add( 'foobar', $item );

		$this->assertTrue( $group->has( 'foobar' ) );
	}

	public function testDeleteItem() {
		$group = new System_Report_Group();
		$item = new System_Report_Item( 'foo', 'bar' );

		$group->add( 'foobar', $item );

		$this->assertTrue( $group->has( 'foobar' ) );

		$group->delete( 'foobar' );

		$this->assertFalse( $group->has( 'foobar' ) );
	}

	public function testItemEscape() {
		$item = new System_Report_Item( '<script>hey<script><a>foo</a>', '<a>bar</a>' );

		$this->assertEquals( 'hey<a>foo</a>', $item->key() );
		$this->assertEquals( '<a>bar</a>', $item->value() );
	}

	public function testItemSensitive() {
		$item = new System_Report_Item( 'foo', 'bar', true );

		$this->assertEquals( 'foo', $item->key() );
		$this->assertEquals( '**********', $item->value() );
	}
}
