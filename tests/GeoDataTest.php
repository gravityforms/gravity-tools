<?php

use PHPUnit\Framework\TestCase;
use Gravity_Forms\Gravity_Tools\Utils\GeoData;

function __( $string, $namespace ) {
	return $string;
}

class GeoDataTest extends TestCase {

	public function testCountriesData() {
		$countries_data = GeoData::countries();
		$this->assertTrue( is_array( $countries_data ) );
		$this->assertTrue( array_key_exists( 'US', $countries_data ) );
		$this->assertTrue( array_key_exists( 'CA', $countries_data ) );
	}

}
