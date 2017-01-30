<?php
	require("pre-funcs.php");
	if (isset($_GET['lat']) && isset($_GET['long']) && isset($_GET['cityId'])) {
		$page=0;
		$lat=$_GET['lat'];
		$lon=$_GET['long'];
		$cityId=$_GET['cityId'];
		$count=10;
		$page=0;
		$type=2;
		$query_arr = array(
		    'lat'=>$lat,
		    'lon'=>$lon,
		    'count'=>$count,
		    'type'=>$type,
		    'page'=>$page,
		    'cityId' =>$cityId
		  );
		  $query_str = http_build_query($query_arr);
		  $data = _sendRequest('https://www.foody.vn/__get/Place/HomeListPlace?'.$query_str);
		  $base_arr = 
		    [
		      "messages"=> [
		        [
		          "attachment"=>[
		            "type"=>"template",
		            "payload"=>[
		              "template_type"=>"generic",
		              "elements"=>[]
		            ]
		          ]
		        ]
		      ]
		    ];

		  $json = json_decode($data,true);
		  if (count($json["Items"])===0) {
		    $base_arr = ["messages"=>[["text"=>"Đã hết địa điểm phù hợp với yêu cầu"]]];
		    echo json_encode($base_arr);
		    exit();
		  }

		  foreach ($json['Items'] as $item) {
		    $Name = $item["Name"];
		    $Address = $item['Address'];
		    $AvgRatingText = $item['AvgRatingText'];
		    $PhotoUrl = $item['PhotoUrl'];
		    $url = "https://www.foody.vn".$item["Url"];
		    //echo $Address.PHP_EOL.$AvgRatingText.PHP_EOL.$PhotoUrl;
		    $base_arr["messages"][0]["attachment"]["payload"]["elements"][] = 
		      [
		        "set_attributes"=>
		        [
		          "currentCityId"=>$cityId
		        ],
		        "title"=>$Name,
		        "image_url"=>$PhotoUrl,
		        "subtitle"=>"Địa chỉ: ".$Address.PHP_EOL."Đánh giá: ".$AvgRatingText,
		        "buttons"=> [
			        [
			          "type"=>"web_url",
			          "url"=>$url,
			          "title"=>"Mở trên web"
			        ],
			        ["type"=>"element_share"]
		        ]
		      ];
		  }
		  $base_arr["messages"][0]["attachment"]["payload"]["elements"][0]["buttons"][] = [
		  	"type"=>"show_block",
		  	"block_name"=>"weekly_top_location",
		  	"title"=>"Xem vị trí khác"
		  ];
		  echo json_encode($base_arr);
	}