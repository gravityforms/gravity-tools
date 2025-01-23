<?php

namespace hermes\tokens\mutations;

use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Insert\Insert_Mutation_Token;
use PHPUnit\Framework\TestCase;

class QueryTokenTest extends TestCase {

	public function testMutationParsesToArray() {
		$text  = '{
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

		$expected_fields = array(
			"id",
			"title",
			"is_completed",
			"is_public",
			"created_at",
		);

        $this->assertEquals( $expected_fields, $token->return_fields() );
	}

}