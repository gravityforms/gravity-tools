<?php

namespace utils;

use Exception;
use Gravity_Forms\Gravity_Tools\Utils\Bettarray;
use PHPUnit\Framework\TestCase;

class BettarrayTest extends TestCase {

	public function testArrayOps() {
		$init_data = array( 'foo' => 'bar', 'bing' => 'bash' );
		$data = new Bettarray( $init_data );

		unset( $data['foo'] );

		$this->assertEquals( array( 'bing' => 'bash' ), $data->data() );

		$data['bingo'] = 'bango';

		$this->assertEquals( array( 'bing' => 'bash', 'bingo' => 'bango' ), $data->data() );

		$data[] = 'huzzah';

		$this->assertEquals( array( 'bing' => 'bash', 'bingo' => 'bango', 0 => 'huzzah' ), $data->data() );
	}

	public function testSet() {
		$data = new Bettarray( array() );

		$data->set( 'foo', 'bar' );

		$this->assertEquals( array( 'foo' => 'bar' ), $data->data() );
	}

	public function testGet() {
		$data = new Bettarray( array( 'foo' => 'bar', 'bing' => 'bash' ) );

		$this->assertEquals( 'bar', $data->get( 'foo' ) );
		$this->assertEquals( 'bash', $data->get( 'bing' ) );

		$data->set( 'bingo', 'bango' );

		$this->assertEquals( 'bango', $data->get( 'bingo' ) );
	}

	public function testDelete() {
		$data = new Bettarray( array( 'foo' => 'bar' ) );
		$data->delete( 'foo' );

		$this->assertEquals( array(), $data->data() );
	}

	public function testDotSet() {
		$data = new Bettarray( array( 'foo' => 'bar' ) );

		$data->set( 'bing.bash.bazinga', array( 1, 2, 3 ) );

		$this->assertEquals( array( 'foo' => 'bar', 'bing' => array( 'bash' => array( 'bazinga' => array( 1, 2, 3 ) ) ) ), $data->data() );
	}

	public function testDotGet() {
		$data = new Bettarray( array( 'foo' => array( 'bar' => array( 'bash' => 'found' ) ) ) );

		$value = $data->get( 'foo.bar.bash' );

		$this->assertEquals( 'found', $value );
	}

	public function testDotDelete() {
		$data = new Bettarray( array( 'foo' => array( 'bar' => array( 'bash' => 'hey' ) ) ) );
		$data->delete( 'foo.bar.bash' );

		$this->assertEquals( array( 'foo' => array( 'bar' => array() ) ), $data->data() );
	}

	public function testAppend() {
		$data = new Bettarray( array( 'foo' => array( 1, 2, 3 ) ) );
		$data->append( 'foo', 4 );

		$this->assertEquals( array( 'foo' => array( 1, 2, 3, 4 ) ), $data->data() );
	}

	public function testDotAppend() {
		$data = new Bettarray( array( 'foo' => array( 'bar' => array( 1, 2, 3 ) ) ) );
		$data->append( 'foo.bar', 4 );

		$this->assertEquals( array( 'foo' => array( 'bar' => array( 1, 2, 3, 4, ) ) ), $data->data() );
	}

	public function testAmend() {
		$data = new Bettarray( array( 'foo' => array( 'bar' => 'bash' ) ) );
		$data->amend( 'foo', array( 'bingo' => 'bango' ) );

		$this->assertEquals( array( 'foo' => array( 'bar' => 'bash', 'bingo' => 'bango' ) ), $data->data() );
	}

	public function testDotAmend() {
		$data = new Bettarray( array( 'foo' => array( 'bar' => array( 'bingo' => 'bango' ) ) ) );
		$data->amend( 'foo.bar', array( 'bazinga' => 'bazanga' ) );

		$this->assertEquals( array( 'foo' => array( 'bar' => array( 'bingo' => 'bango', 'bazinga' => 'bazanga' ) ) ), $data->data() );
	}
}
