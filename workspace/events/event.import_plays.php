<?php

	require_once(TOOLKIT . '/class.event.php');

	Class eventimport_plays extends SectionEvent{

		public $ROOTELEMENT = 'import-plays';

		public $eParamFILTERS = array(
			
		);

		public static function about(){
			return array(
				'name' => 'Import Plays',
				'author' => array(
					'name' => 'test test',
					'website' => 'http://symphony-2',
					'email' => 'test@test.com'),
				'version' => 'Symphony 2.3RC2',
				'release-date' => '2012-05-05T22:11:37+00:00',
				'trigger-condition' => 'action[import-plays]'
			);
		}

		public static function getSource(){
			return '1';
		}

		public static function allowEditorToParse(){
			return true;
		}

		public static function documentation(){
			return '
        <h3>Success and Failure XML Examples</h3>
        <p>When saved successfully, the following XML will be returned:</p>
        <pre class="XML"><code>&lt;import-plays result="success" type="create | edit">
  &lt;message>Entry [created | edited] successfully.&lt;/message>
&lt;/import-plays></code></pre>
        <p>When an error occurs during saving, due to either missing or invalid fields, the following XML will be returned:</p>
        <pre class="XML"><code>&lt;import-plays result="error">
  &lt;message>Entry encountered errors when saving.&lt;/message>
  &lt;field-name type="invalid | missing" />
  ...
&lt;/import-plays></code></pre>
        <h3>Example Front-end Form Markup</h3>
        <p>This is an example of the form markup you can use on your frontend:</p>
        <pre class="XML"><code>&lt;form method="post" action="" enctype="multipart/form-data">
  &lt;input name="MAX_FILE_SIZE" type="hidden" value="5242880" />
  &lt;label>Title
    &lt;input name="fields[title]" type="text" />
  &lt;/label>
  &lt;input name="action[import-plays]" type="submit" value="Submit" />
&lt;/form></code></pre>
        <p>To edit an existing entry, include the entry ID value of the entry in the form. This is best as a hidden field like so:</p>
        <pre class="XML"><code>&lt;input name="id" type="hidden" value="23" /></code></pre>
        <p>To redirect to a different location upon a successful save, include the redirect location in the form. This is best as a hidden field like so, where the value is the URL to redirect to:</p>
        <pre class="XML"><code>&lt;input name="redirect" type="hidden" value="http://symphony-2/success/" /></code></pre>';
		}

		public function load(){
			if(isset($_POST['action']['import-plays'])) return $this->__trigger();
		}

	}
