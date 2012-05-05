<?php
class elasticsearch_plays {
	
	public function mapData(Array $data, Entry $entry) {
		$json = array();
		$json['title'] = $data['title']['value'];
		$json['_boost'] = 3;
		return $json;
	}
	
}