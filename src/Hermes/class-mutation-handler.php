<?php

namespace Gravity_Forms\Gravity_Tools\Hermes;

use Gravity_Forms\Gravity_Tools\Hermes\Enum\Field_Type_Validation_Enum;
use Gravity_Forms\Gravity_Tools\Hermes\Models\Model;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Connect_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Delete_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Insert_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Update_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Data_Object_From_Array_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Field_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Connect\Connect_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Delete\Delete_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Generic_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Insert\Insert_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Mutations\Update\Update_Mutation_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Tokens\Query_Token;
use Gravity_Forms\Gravity_Tools\Hermes\Utils\Model_Collection;

class Mutation_Handler {

	/**
	 * @var string
	 */
	protected $db_namespace;

	/**
	 * @var Model_Collection
	 */
	protected $models;

	/**
	 * @var Query_Handler
	 */
	protected $query_handler;

	/**
	 * @var Insert_Runner
	 */
	protected $insert_runner;

	/**
	 * @var Delete_Runner
	 */
	protected $delete_runner;

	/**
	 * @var Update_Runner
	 */
	protected $update_runner;

	/**
	 * @var Connect_Runner
	 */
	protected $connect_runner;

	public function __construct( $db_namespace, $models, $query_handler, $runners ) {
		$this->db_namespace   = $db_namespace;
		$this->models         = $models;
		$this->query_handler  = $query_handler;
		$this->insert_runner  = $runners['insert'];
		$this->delete_runner  = $runners['delete'];
		$this->update_runner  = $runners['update'];
		$this->connect_runner = $runners['connect'];
	}

	public function handle_mutation( $mutation_string ) {
		global $wpdb;

		$generic_mutation = new Generic_Mutation_Token( $mutation_string );

		/**
		 * Mutation_Token $mutation
		 */
		$mutation = $generic_mutation->mutation();

		if ( ! $this->models->has( $mutation->object_type() ) ) {
			$error_message = sprintf( 'Mutation attempted with invalid object type: %s', $mutation->object_type() );
			throw new \InvalidArgumentException( $error_message );
		}

		$object_model = $this->models->get( $mutation->object_type() );

		if ( ! $object_model->has_access() ) {
			$error_message = sprintf( 'Access not allowed for object type %s', $mutation->object_type() );
			throw new \InvalidArgumentException( $error_message );
		}

		switch ( $mutation->operation() ) {
			case 'insert':
				$this->insert_runner->run( $mutation, $object_model );
				break;
			case 'update':
				$this->update_runner->run( $mutation, $object_model );
				break;
			case 'delete':
				$this->delete_runner->run( $mutation, $object_model );
				break;
			case 'connect':
				$this->connect_runner->run( $mutation, $object_model );
				break;
			default:
				break;
		}
	}

	/**
	 * @param Connect_Mutation_Token $mutation
	 * @param Model                  $object_model
	 *
	 * @return void
	 */
	private function handle_connect_mutation( $mutation, $object_model ) {

	}

}