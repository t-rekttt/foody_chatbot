<?php
	require_once("pre-funcs.php");
	function city_match ($lat,$long)
	{
		$API_KEY = 'AIzaSyB0d2WX4lJsBznn8EUK52xCE0RYCYjoAYI';
		$csv = array_map('str_getcsv', file('cities.txt'));
		$coors = $lat.','.$long;
		$data = _sendRequest("https://maps.googleapis.com/maps/api/geocode/json?latlng=".$coors);
		$json = json_decode($data,1);
		$components = $json["results"][0]['address_components'];
		$temp=0;
		foreach ($components as $address) {
			if ($address["types"][0]==="administrative_area_level_1") {
				$temp = $address;
			}
		}
		$temp_name = format($temp['long_name']);
		$types = $temp['types'][0];
		$city = FALSE;
		foreach ($csv as $data) {
			$data_name = format($data[2]);
			if (strpos($temp_name,$data_name)!==FALSE) {
				$city = $data[0];
				break;
			}
		}
		return $city;
	}

	function city_match_new($string)
	{
		$API_KEY = 'AIzaSyB0d2WX4lJsBznn8EUK52xCE0RYCYjoAYI';
		$csv = array_map('str_getcsv', file('cities.txt'));
		if (strpos($string, "http://www.google.com/maps/place/")!==FALSE) {
			$coors = explode("http://www.google.com/maps/place/", $string)[1];
			$data = _sendRequest("https://maps.googleapis.com/maps/api/geocode/json?latlng=".$coors);
			$json = json_decode($data,1);
			$lat = explode(",", $coors)[0];
			$long = explode(",", $coors)[1];
			$status = $json["status"];
			if ($status!=='OK') {
				return FALSE;
			}
		}
		else {
			$data = _sendRequest("https://maps.googleapis.com/maps/api/geocode/json?address=".$string);
			$json = json_decode($data,1);
			$status = $json["status"];
			if ($status!=='OK') {
				return FALSE;
			}
			$lat = $json["results"][0]["geometry"]["location"]["lat"];
			$long = $json["results"][0]["geometry"]["location"]["lng"];
		}
		//$coors = $lat.','.$long;
		
		$components = $json["results"][0]['address_components'];
		$temp=0;
		foreach ($components as $address) {
			if ($address["types"][0]==="administrative_area_level_1") {
				$temp = $address;
			}
		}
		$temp_name = format($temp['long_name']);
		$types = $temp['types'][0];
		$status = FALSE;
		foreach ($csv as $data) {
			$data_name = format($data[2]);
			if (strpos($temp_name,$data_name)!==FALSE) {
				$city = $data[0];
				$href = $data[1];
				$status = TRUE;
				break;
			}
		}
		if (!$status) {
			return FALSE;
		}
		$result_arr = [
			"status"=>TRUE,
			"cityId"=>$city,
			"lat"=>$lat,
			"long"=>$long,
			"href"=>$href
		];
		return $result_arr;
	}