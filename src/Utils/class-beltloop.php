<?php

namespace Gravity_Forms\Gravity_Tools\Utils;

class Beltloop {

	public static function sort( $data, $id_key = 'id', $sort_key = 'prevId' ) {
		$indexed = array();

		foreach ( $data as $datum ) {
			$indexed[] = array(
				'sort_val' => $datum[ $sort_key ],
				'data' => $datum,
			);
		}

		$sorted = array();

		$current_index = 0;

		while ( count( $indexed ) ) {
			$item_by_idx = self::get_matching_item_by_sort_key( $current_index, $indexed );
			$current  = $item_by_idx['data'];
			$sorted[] = $current;

			unset( $indexed[ $item_by_idx['idx'] ] );
			$current_index = $current[ $id_key ];
		}

		return $sorted;
	}

	private static function get_matching_item_by_sort_key( $index, $items ) {
		$checked = array_filter( $items, function( $item ) use ( $index ) {
			return (int) $item['sort_val'] === (int) $index;
		} );

		if ( empty( $checked ) ) {
			return array(
				'data' => $items[ array_key_first( $items ) ]['data'],
				'idx' => array_key_first( $items ),
			);
		}

		return array(
			'data' => $checked[ array_key_first( $checked ) ]['data'],
			'idx' => array_key_first( $checked ),
		);
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
