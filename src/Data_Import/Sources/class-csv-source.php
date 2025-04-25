<?php

namespace Gravity_Forms\Gravity_Tools\Data_Import\Sources;

use Gravity_Forms\Gravity_Tools\Data_Import\Record;
use Gravity_Forms\Gravity_Tools\Data_Import\Source;

class CSV_Source implements Source {

	private $raw_data;

	private $data;

	/**
	 * Parse and store the passed CSV data.
	 *
	 * @param string $data - The raw CSV string data.
	 *
	 * @return void
	 */
	public function set_data( $data ) {
		$this->raw_data = $data;
		$this->data     = str_getcsv( $data );
	}

	/**
	 * Get all of the available keys from the CSV headers.
	 *
	 * @param array $args - An array of additional args, info, or data needed.
	 *
	 * @return array
	 */
	public function keys( $args ) {
	}

	/**
	 * Determine if this CSV has a given key.
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function has_key( $key ) {
	}

	/**
	 * Get the rows for this CSV.
	 *
	 * @param array $args
	 *
	 * @return Record[]
	 */
	public function records( $args ) {
		$records = array();
		
		foreach( $this->data as $row ) {
			$records[] = new Record( $row );
		}

		return $records;
	}

	/**
	 * Get the row count for this CSV.
	 *
	 * @param array $args
	 *
	 * @return int
	 */
	public function count( $args ) {
		return count( $this->data );
	}

	/**
	 * Get a specific slice of rows from this CSV.
	 *
	 * @param int   $count
	 * @param int   $offset
	 * @param array $additional_args
	 *
	 * @return Record[]
	 */
	public function slice( $count, $offset, $additional_args ) {
	}
}
