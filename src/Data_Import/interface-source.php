<?php

namespace Gravity_Forms\Gravity_Tools\Data_Import;

interface Source {

	public function set_data( $data );

	public function keys( $args );

	public function has_key( $key );

	/**
	 * Get the records for this source.
	 *
	 * @param array $args
	 *
	 * @return Record[]
	 */
	public function records( $args );

	public function count( $args );

	/**
	 * Get a specific slice of records from this source.
	 *
	 * @param int   $count
	 * @param int   $offset
	 * @param array $additional_args
	 *
	 * @return Record[]
	 */
	public function slice( $count, $offset, $additional_args );
}
