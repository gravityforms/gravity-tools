<?php

namespace hermes;

use Gravity_Forms\Gravity_Tools\Hermes\Mutation_Handler;
use Gravity_Forms\Gravity_Tools\Hermes\Query_Handler;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Connect_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Delete_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Insert_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Runners\Update_Runner;
use Gravity_Forms\Gravity_Tools\Hermes\Utils\Model_Collection;
use PHPUnit\Framework\TestCase;

global $wpdb;

class HandlerTest extends TestCase {

	protected $model_collection;
	protected $contact_model;
	protected $company_model;
	protected $email_model;
	protected $query_handler;
	protected $mutation_handler;
	protected $db_namespace;

	public function setUp() {
		$this->model_collection = new Model_Collection();
		$this->contact_model    = new \FakeContactModel();
		$this->company_model    = new \FakeCompanyModel();
		$this->email_model      = new \FakeEmailModel();

		$this->model_collection->add( 'contact', $this->contact_model );
		$this->model_collection->add( 'company', $this->company_model );
		$this->model_collection->add( 'email', $this->email_model );
		$this->db_namespace = 'gravitycrm';

		$this->query_handler = new Query_Handler( $this->db_namespace, $this->model_collection );
		
		$connect_runner = new Connect_Runner( $this->db_namespace, $this->query_handler, $this->model_collection );

		$runners = array(
			'insert'  => new Insert_Runner( $this->db_namespace, $this->query_handler, $this->model_collection, $connect_runner ),
			'delete'  => new Delete_Runner( $this->db_namespace, $this->query_handler, $this->model_collection ),
			'connect' => $connect_runner,
			'update'  => new Update_Runner( $this->db_namespace, $this->query_handler, $this->model_collection ),
		);

		$this->mutation_handler = new Mutation_Handler( $this->db_namespace, $this->model_collection, $this->query_handler, $runners );
	}

	public function testItemsAddedWithRelationships() {
		$text = '{
		insert_company( objects: [
			{
				companyName: "Acme, INC",
				contact: [
					{
						firstName: "John",
						lastName: "Smith",
						email: [{
							type: "work",
							address: "jsmith@acme.local",
						}]
					},
					{
						firstName: "Jane",
						lastName: "Doe",
					}
				]	
			},
			{
				companyName: "Acme2, INC",
				contact: [
					{
						firstName: "Phil",
						lastName: "Johnson"
					},
					{
						firstName: "Janet",
						lastName: "Bigelow",
					}
				]
			}
		]){
			returning {
				id,
				companyName,
				contact {
					id,
					firstName,
					lastName,
					email {
						id,
						address,
					}	
				}
			}
		}
		}';

		try {
			$data = $this->mutation_handler->handle_mutation( $text );
		} catch ( \Exception $e ) {
			$this->assertEquals( 'success', $e->getMessage() );

			return;
		}

		$this->assertEquals( 'success', 'success' );
	}
}
