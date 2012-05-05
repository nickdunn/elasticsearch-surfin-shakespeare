<?php

$handler = opendir('.');

$plays = array();
$acts = array();
$scenes = array();

while ($file = readdir($handler)) {
	
	if (preg_match('/.xml$/', $file)) {
		
		$xml = simplexml_load_file($file);
		
		$play_title = rawurlencode((string)reset($xml->xpath('/PLAY/TITLE')));
		post('fields[title]=' . $play_title . '&action[import-plays]=true');

		foreach($xml->xpath('//ACT') as $act) {

			$act_title = rawurlencode((string)$act->TITLE);
			post('fields[title]=' . $act_title . '&fields[play]=' . $play_title . '&action[import-acts]=true');

			foreach($act->xpath('SCENE') as $scene) {

				$scene_title = rawurlencode((string)$scene->TITLE);
				post('fields[title]=' . $scene_title . '&fields[act]=' . $act_title . '&fields[play]=' . $play_title . '&action[import-scenes]=true');

				$speeches_post = '';
				$i = 0;
				foreach($scene->xpath('SPEECH') as $speech) {

					$speaker_name = rawurlencode((string)$speech->SPEAKER);

					$lines = array();
					foreach($speech->xpath('LINE') as $line) {
						$lines[] = (string)$line;
					}
					
					$speeches_post .= '&fields['.$i.'][speaker]=' . $speaker_name . '&fields['.$i.'][lines]=' . rawurlencode(implode("\n", $lines)) . '&fields['.$i.'][scene]=' . $scene_title . '&fields['.$i.'][act]=' . $act_title . '&fields['.$i.'][play]=' . $play_title;
					$i++;
				}
				
				post($speeches_post . '&action[import-speeches]=true');

			}

		}
		
	}
}

closedir($handler);

function post($data) {
	$ch = curl_init('http://symphony-2/import/');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	echo curl_exec($ch);
	curl_close($ch);
}