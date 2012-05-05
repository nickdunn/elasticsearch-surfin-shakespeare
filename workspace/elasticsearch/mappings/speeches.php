<?php
class elasticsearch_speeches {
	
	public function mapData(Array $data, Entry $entry) {
		$json = array();
		$json['speaker'] = $data['speaker']['value'];
		$json['lines'] = $data['lines']['value'];
		return $json;
	}
	
}