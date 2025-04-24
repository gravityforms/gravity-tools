<?php 

namespace Gravity_Forms\Gravity_Tools\Data_Import;

interface Destination {

	public function keys( $args );

	public function has_key( $key );

	public function store( $records );

	public function check_for_duplicates( $record, $check_key );
}
