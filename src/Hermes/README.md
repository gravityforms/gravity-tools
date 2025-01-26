# Project Hermes

__A Query Architecture for dynamic, performant, client-controlled data handling and processing.__

## AJAX, GraphQL, and The Problem of Query Structures
In our typical product configuration, bespoke endpoints are setup and maintained for each unique type 
of query that might come from the Client. Need to get a list of users? That's an endpoint. Get the companies users belong to? Another endpoint.
Save a setting, get a setting, update a field, get a field - all individual endpoints.

Oftentimes, this paradigm is more than acceptable. The total number of query types being handled is typically low, and the 
need for net-new endpoints rarely occurs. 

But what about situations in which there are many, many types of data to query, in all sorts of various configurations? What if the 
application is a single-page React app, where all the data comes view queries and nothing exists on page load? What if a given data type 
or object model requires a new piece of data? Suddenly we're not just spinning up unique endpoints for each situation, but also having to 
update/modify/drop database tables as well (something that has caused countless headaches for Plugin products like ours).

Enter GraphQL, an alternative Query standard to REST/AJAX. GraphQL differs from traditional REST architecture primarily in how 
query structures and data shapes are controlled. In REST, you retrieve a specific object from a specific endpoint. "Users" are queried via "/json/users". 
"Companies" are queried via "json/companies". If you need both, or need to get Users categorized by a given Company, it's two separate queries, 
while necessitating that values from the first are saved and passed to the second. In simple contexts, this is fine, but as complexity grows,
so do the headaches around this. 

GraphQL tackles this by having the Server simply establish a structured schema of Object Types, along with their
relationships, fields, and other criteria. The Client can then use this schema to query *anything it needs*, all at a single endpoint.

## That's great, but what are you talking about?

Let's look at a typical example: retrieving a list of companies, the departments at said company, and the employees assigned to each department. In REST, that
would be 3 separate requests, like the following pseudo-code:

```js
const companies = request( { dataType: 'company' } );

for ( company in companies ) {
	const departments = request( { dataType: 'department', companyId: company.id } );

	for ( department in departments ) {
		const employees = request( { dataType: 'employee', departmentId: department.id } );
		department.employees = employees;
	}

	company.departments = departments;
}

return companies;
```

Not terrible, but if more nesting/relationships were required, things would quickly balloon out of control.
Not to mention we're making 3 distinct requests to the server, requiring more time and bandwidth.

Now let's look at how this would work in GraphQL:

```js
const query = `company {
                    id,
                    name,
                    address,
                    department {
                        id,
                        name,
                        employeeNames: employee {
                            id,
                            first_name,
                            last_name
                        }
                    }
                }`;

const companies = request( { query } );
```

Not only do we reduce the total number of requests to one, we also have 
complete, full control over the data shape returned to us. We can even alias the various 
field values (like we do here by aliasing `employee` to `employeeNames` ). This results in substantially
decreased overhead for the server, as new data structures don't require any updates. It also makes architecting 
the client-side system easier, as dynamic queries can be made simply by traversing the schema provided 
by the server and selecting which fields/related objects to query.

Magic!

## Monkey wrench: GraphQL is enormé. 

This is all good and well, but how do we take advantage of GraphQL? On the plus side, it's an open-source 
system that is actively-maintained and provided by some incredible engineers at Facebook/Meta. 

Unfortunately, it's also absolutely gigantic. Including the library required to run a functioning GraphQL server 
in PHP is dozens of additional MB of file bloat, all replete with opportunities for conflicts with other plugins 
which may use GraphQL. Includig it in a distributed plugin is a non-starter, full-stop.

So are we out of luck?

## Solution: just write our own

Given the limited file size constraints and variety in hosting platforms, we decided the best approach 
would be to adopt GraphQL's syntax and functionality, but hand-roll our own server for parsing GraphQL 
queries into SQL statements that we can execute against the database. 

And thus, Project Hermes was born!

## The Bits and Bobs

Fundamentally, Hermes consists of five mechanisms: `Models`, `Handlers`, `Parsers`, `Tokens`, and `Runners`. 
During a given request, the `Models` registered to the system are referenced by a `Handler`, which takes the Query String
and psses it on to a `Parser`, which in turn lexes the string into defined `Tokens`, each of which are classes holding the 
structured data needed for the various `Runners` to execute the query.

That's a lot, but let's look at them one by one:

## Models: The Backbone of Hermes Data

`Models` are responsible for describing all of the various object types available for 
querying and mutation. A given `Model` defines:

- The fields available for the object type
- The meta fields available to assign to the object type
- The minimum Capability required for they querying user to access the object type
- The relationships a given object type has to _other_ object types.

When a query is executed, the registered `Models` are referenced to ensure the data being
requested exists, that the data is available to the user, and the various tables required 
for querying.

Consider the following:

```php
class FakeContactModel extends \Gravity_Forms\Gravity_Tools\Hermes\Models\Model {

	protected $type = 'contact';

	protected $access_cap = 'manage_options';

	public function fields() {
		return array(
			'id'         => Field_Type_Validation_Enum::INT,
			'first_name' => Field_Type_Validation_Enum::STRING,
			'last_name'  => Field_Type_Validation_Enum::STRING,
			'email'      => Field_Type_Validation_Enum::EMAIL,
			'phone'      => Field_Type_Validation_Enum::STRING,
			'foobar'     => function ( $value ) {
				if ( $value === 'foo' ) {
					return 'foo';
				}

				return null;
			},
		);
	}

	public function meta_fields() {
		return array(
			'secondary_phone'   => Field_Type_Validation_Enum::STRING,
			'alternate_website' => Field_Type_Validation_Enum::STRING,
		);
	}

	public function relationships() {
		return new \Gravity_Forms\Gravity_Tools\Hermes\Utils\Relationship_Collection(
			array(
				new \Gravity_Forms\Gravity_Tools\Hermes\Utils\Relationship( 'group', 'contact', 'manage_options', true )
			)
		);
	}

}
```

This code would register a new `Model` for an object type named `contact`. It defines several fields
that a `contact` can have, as well as any `meta` fields that might be assignable to it. The `fields` 
should exist in the related DB table as columns, while `meta` fields can be added ad-hoc and are stored
in the separate `meta` lookup table.

Each field, whether meta or local, references a specific Field Validation Type, either by directly referencing
one of the Field Validation Type Enum values, or by providing a custom callback. This determines how values 
will be validated and sanitized before being inserted into the database.

Finally, it establishes a relationship to another `Model`, `Group`. Since the table connects Groups _to_ Contacts,
this `Model` designates the relationship as `reversed`, telling the system to look in the `group_contact` table. 