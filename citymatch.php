<?php
	function city_match ($lat,$long)
	{
		$API_KEY = 'AIzaSyB0d2WX4lJsBznn8EUK52xCE0RYCYjoAYI';
		$csv = array_map('str_getcsv', file('cities.txt'));
		$coors = $lat.','.$long;
		$data = _sendRequest("https://maps.googleapis.com/maps/api/geocode/json?latlng=".$coors."&key=".$API_KEY);
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