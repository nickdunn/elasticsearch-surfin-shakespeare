<?php
class elasticsearch_scenes {
	
	public function mapData(Array $data, Entry $entry) {
		$json = array();
		$json['title'] = $data['title']['value'];
		return $json;
	}
	
}