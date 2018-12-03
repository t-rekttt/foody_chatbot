<?php
	require_once('pre-funcs.php');
	require('citymatch.php');
	if (isset($_GET['string'])) {
		$string = $_GET['string'];
		$match = city_match_new($string);
			if (!$match["status"]) {
			echo json_encode(["messages"=>[["text"=>"Hiện chưa có dữ liệu cho vị trí này"]]]);
			exit();
		}
		$cityId = $match["cityId"];
		$lat = $match["lat"];
		$long = $match["long"];
		$href = $match["href"];
		$data_arr = [
			"set_attributes"=>
	        [
	          "currentCityId"=>$cityId,
	          "currentLat"=>$lat,
	          "currentLong"=>$long,
	          "currentHref"=>$href
	        ],
	        "messages" => [
	        	["text"=>"Lưu vị trí thành công!"]
	        ]
		];
		echo json_encode($data_arr);
	}