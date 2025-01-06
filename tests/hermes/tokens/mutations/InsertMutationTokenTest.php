<?php

namespace hermes\tokens\mutations;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Insert_Mutation_Token;
use PHPUnit\Framework\TestCase;

class QueryTokenTest extends TestCase {

	public function testMutationParsesToArray() {
		$text = '{
  insert_todos(objects: [{title: "New Todo", status: "active"}, {title: "Second Todo", status: "pending"}]) {
    returning {
      id
      title
      is_completed
      is_public
      created_at
    }
  }
}';
		$token = new Insert_Mutation_Token( $text );
		var_dump( $token );
	}

}