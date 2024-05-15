<?php

namespace Gravity_Forms\Gravity_Tools\Query;

class Limit_Parser {

	public function process( $page, $per_page ) {
		$limit  = (int) $per_page;
		$offset = (int) max( $page - 1, 0 ) * (int) $per_page;

		return sprintf( 'LIMIT %d OFFSET %d', $limit, $offset );
	}

}