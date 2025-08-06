<?php

namespace emails;

use Gravity_Forms\Gravity_Tools\Emails\Email_Templatizer;
use PHPUnit\Framework\TestCase;

class EmailTemplatizerTest extends TestCase {

	public function testTokenReplacement() {
		$markup = '<div>{{ foo }} <span>{{ bar.0.bash }}</span></div>';
		$data = array(
			'foo' => 'Hey this is foo.',
			'bar' => array(
				array(
					'bash' => 'And this is bash.'
				)
			)
		);

		$templatizer = new Email_Templatizer( $markup );

		$result = $templatizer->render( $data );

		$this->assertEquals( '<div>Hey this is foo. <span>And this is bash.</span></div>', $result );
	}

	public function testConditionals() {
		$markup = '<div>
			<span>Hey there</span>
			{{%if stats.recipients.0 %}}
				This should only be rendered if recipient 1 exists.
			{{%endif%}}

			{{%if stats.recipients.1 %}}
				This should only be rendered if recipient 2 exists.
			{{%endif%}}
		</div>';

		$data = array(
			'stats' => array(
				'recipients' => array(
					array(
						'foo' => 'bar',
					)
				)
			)
		);

		$templatizer = new Email_Templatizer( $markup );

		$result = $templatizer->render( $data );

		$this->assertTrue( strpos( $result, 'recipient 1' ) !== false );
		$this->assertTrue( strpos( $result, 'recipient 2' ) === false );
	}
}
