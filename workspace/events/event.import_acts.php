<?php

	require_once(TOOLKIT . '/class.event.php');

	Class eventimport_acts extends SectionEvent{

		public $ROOTELEMENT = 'import-acts';

		public $eParamFILTERS = array(
			
		);

		public static function about(){
			return array(
				'name' => 'Import Acts',
				'author' => array(
					'name' => 'test test',
					'website' => 'http://symphony-2',
					'email' => 'test@test.com'),
				'version' => 'Symphony 2.3RC2',
				'release-date' => '2012-05-05T22:11:30+00:00',
				'trigger-condition' => 'action[import-acts]'
			);
		}

		public static function getSource(){
			return '2';
		}

		public static function allowEditorToParse(){
			return true;
		}

		public static function documentation(){
			return '
        <h3>Success and Failure XML Examples</h3>
        <p>When saved successfully, the following XML will be returned:</p>
        <pre class="XML"><code>&lt;import-acts result="success" type="create | edit">
  &lt;message>Entry [created | edited] successfully.&lt;/message>
&lt;/import-acts></code></pre>
        <p>When an error occurs during saving, due to either missing or invalid fields, the following XML will be returned:</p>
        <pre class="XML"><code>&lt;import-acts result="error">
  &lt;message>Entry encountered errors when saving.&lt;/message>
  &lt;field-name type="invalid | missing" />
  ...
&lt;/import-acts></code></pre>
        <h3>Example Front-end Form Markup</h3>
        <p>This is an example of the form markup you can use on your frontend:</p>
        <pre class="XML"><code>&lt;form method="post" action="" enctype="multipart/form-data">
  &lt;input name="MAX_FILE_SIZE" type="hidden" value="5242880" />
  &lt;label>Title
    &lt;input name="fields[title]" type="text" />
  &lt;/label>
  &lt;label>Play
    &lt;select name="fields[play]" disabled="disabled">&lt;/select>
  &lt;/label>
  &lt;input name="action[import-acts]" type="submit" value="Submit" />
&lt;/form></code></pre>
        <p>To edit an existing entry, include the entry ID value of the entry in the form. This is best as a hidden field like so:</p>
        <pre class="XML"><code>&lt;input name="id" type="hidden" value="23" /></code></pre>
        <p>To redirect to a different location upon a successful save, include the redirect location in the form. This is best as a hidden field like so, where the value is the URL to redirect to:</p>
        <pre class="XML"><code>&lt;input name="redirect" type="hidden" value="http://symphony-2/success/" /></code></pre>';
		}

		public function load(){
			if(isset($_POST['action']['import-acts'])) return $this->__trigger();
		}

	}
