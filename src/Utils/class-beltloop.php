<?php

namespace Gravity_Forms\Gravity_Tools\Utils;

class Beltloop {

	public static function sort( $data, $id_key = 'id', $sort_key = 'prevId' ) {
		$indexed = array();

		foreach ( $data as $datum ) {
			$indexed[ $datum[ $sort_key ] ] = $datum;
		}

		$sorted = array();

		$current_index = null;

		if ( ! array_key_exists( null, $indexed ) ) {
			$current_index = array_key_first( $indexed );
		}

		while ( count( $indexed ) ) {
			if ( ! array_key_exists( $current_index, $indexed ) ) {
				$current_index = array_key_first( $indexed );
			}

			$current  = $indexed[ $current_index ];
			$sorted[] = $current;

			unset( $indexed[ $current_index ] );
			$current_index = $current[ $id_key ];
		}

		return $sorted;
	}

	public static function partial_sort( $full_list, $partial_list, $id_key = 'id' ) {
		$indexed = array();

		foreach ( $full_list as $item ) {
			$indexed[ $item[ $id_key ] ] = $item;
		}

		$sorted = array();

		foreach ( $partial_list as $part_item ) {
			$search         = $part_item[ $id_key ];
			$pos            = (int) array_search( $search, array_keys( $indexed ) );
			$sorted[ $pos ] = $part_item;
		}

		ksort( $sorted );

		return array_values( $sorted );
	}
}
