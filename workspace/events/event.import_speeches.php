<?php

	require_once(TOOLKIT . '/class.event.php');

	Class eventimport_speeches extends SectionEvent{

		public $ROOTELEMENT = 'import-speeches';

		public $eParamFILTERS = array(
			'expect-multiple'
		);

		public static function about(){
			return array(
				'name' => 'Import Speeches',
				'author' => array(
					'name' => 'test test',
					'website' => 'http://symphony-2',
					'email' => 'test@test.com'),
				'version' => 'Symphony 2.3RC2',
				'release-date' => '2012-05-05T22:11:57+00:00',
				'trigger-condition' => 'action[import-speeches]'
			);
		}

		public static function getSource(){
			return '4';
		}

		public static function allowEditorToParse(){
			return true;
		}

		public static function documentation(){
			return '
        <h3>Success and Failure XML Examples</h3>
        <p>When saved successfully, the following XML will be returned:</p>
        <pre class="XML"><code>&lt;import-speeches>
  &lt;entry index="0" result="success" type="create | edit">
    &lt;message>Entry [created | edited] successfully.&lt;/message>
  &lt;/entry>
&lt;/import-speeches></code></pre>
        <p>When an error occurs during saving, due to either missing or invalid fields, the following XML will be returned (<strong> Notice that it is possible to get mixtures of success and failure messages when using the ‘Allow Multiple’ option</strong>):</p>
        <pre class="XML"><code>&lt;import-speeches>
  &lt;entry index="0" result="error">
    &lt;message>Entry encountered errors when saving.&lt;/message>
    &lt;field-name type="invalid | missing" />
  &lt;/entry>
  &lt;entry index="1" result="success" type="create | edit">
    &lt;message>Entry [created | edited] successfully.&lt;/message>
  &lt;/entry>
  ...
&lt;/import-speeches></code></pre>
        <p>The following is an example of what is returned if any options return an error:</p>
        <pre class="XML"><code>&lt;import-speeches result="error">
  &lt;message>Entry encountered errors when saving.&lt;/message>
  &lt;filter name="admin-only" status="failed" />
  &lt;filter name="send-email" status="failed">Recipient not found&lt;/filter>
  ...
&lt;/import-speeches></code></pre>
        <h3>Example Front-end Form Markup</h3>
        <p>This is an example of the form markup you can use on your frontend:</p>
        <pre class="XML"><code>&lt;form method="post" action="" enctype="multipart/form-data">
  &lt;input name="MAX_FILE_SIZE" type="hidden" value="5242880" />
  &lt;label>Speaker
    &lt;input name="fields[0][speaker]" type="text" />
  &lt;/label>
  &lt;label>Lines
    &lt;textarea name="fields[0][lines]" rows="15" cols="50">&lt;/textarea>
  &lt;/label>
  &lt;label>Scene
    &lt;select name="fields[0][scene]" disabled="disabled">&lt;/select>
  &lt;/label>
  &lt;label>Act
    &lt;select name="fields[0][act]" disabled="disabled">&lt;/select>
  &lt;/label>
  &lt;label>Play
    &lt;select name="fields[0][play]" disabled="disabled">&lt;/select>
  &lt;/label>
  &lt;input name="action[import-speeches]" type="submit" value="Submit" />
&lt;/form></code></pre>
        <p>To edit an existing entry, include the entry ID value of the entry in the form. This is best as a hidden field like so:</p>
        <pre class="XML"><code>&lt;input name="id[0]" type="hidden" value="23" /></code></pre>
        <p>To redirect to a different location upon a successful save, include the redirect location in the form. This is best as a hidden field like so, where the value is the URL to redirect to:</p>
        <pre class="XML"><code>&lt;input name="redirect" type="hidden" value="http://symphony-2/success/" /></code></pre>';
		}

		public function load(){
			if(isset($_POST['action']['import-speeches'])) return $this->__trigger();
		}

	}
