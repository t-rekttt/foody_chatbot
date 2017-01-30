<?php
	require('pre-funcs.php');
	require('citymatch.php');
	if (isset($_GET['lat']) && isset($_GET['long'])) {
		$lat = $_GET['lat'];
		$long = $_GET['long'];
		$cityId= city_match($lat,$long);
			if ($cityId=="0") {
			echo json_encode(["messages"=>[["text"=>"Hiện chưa có dữ liệu cho vị trí này"]]]);
			exit();
		}
		$data_arr = [
			"set_attributes"=>
	        [
	          "currentCityId"=>$cityId,
	          "currentLat"=>$lat,
	          "currentLong"=>$long
	        ],
	        "messages" => [
	        	["text"=>"Lưu vị trí thành công!"]
	        ]
		];
		echo json_encode($data_arr);
	}