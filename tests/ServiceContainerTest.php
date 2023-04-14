<?php

use PHPUnit\Framework\TestCase;
use Gravity_Forms\Gravity_Tools\Service_Container;

class ServiceContainerTest extends TestCase {

	public function testServiceIsAdded() {
		$container = new Service_Container();
		$service = array( 'foo', 'bar' );
		$service_name = 'test_service';

		$container->add( $service_name, $service );

		$this->assertSame( $container->get( $service_name ), $service );
	}

}