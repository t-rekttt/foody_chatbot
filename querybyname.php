<?php
  require("pre-funcs.php");
  header('Content-Type: application/json');
  if (isset($_GET['cityId']) && isset($_GET['query']) && isset($_GET['href'])) {
    $cityId=$_GET['cityId'];
    $query=$_GET['query'];
    $href=$_GET['href'];
    $page=1;
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    }

    $query_arr = [
      'provinceId'=>$cityId,
      'page'=>$page,
      'q'=>$query,
      'ds'=>'Restaurant',
      'append'=>'true'
    ];

    $query_str = http_build_query($query_arr);
    $data = _sendRequest('https://www.foody.vn'.$href.'/dia-diem?'.$query_str);
    $json = json_decode($data,1);
    $searchItems = $json["searchItems"];
    if (count($searchItems)===0) {
      $messages = ["messages"=> 
        [
          [
            "attachment"=>[
              "type"=> "template",
              "payload"=>[
                "template_type"=>"button",
                "text"=>"Đã hết kết quả tìm kiếm phù hợp với yêu cầu",
                "buttons"=>[
                  [
                    "type"=>"show_block",
                    "block_name"=>"menu",
                    "title"=>"Trở về menu"
                  ]
                ]
              ]
            ]
          ]
        ]
      ];
      echo json_encode($messages);
      exit;
    }
    //print_r($json["searchItems"][0]);
    $base_arr = 
        [ 
          "set_attributes"=>
            [
              "currentCityId"=>$cityId
            ],
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
    
    foreach ($searchItems as $item) {
      if (count($base_arr["messages"][0]["attachment"]["payload"]["elements"])>=10) break;
      $Name = $item["Name"];
      $Address = $item['Address'];
      $AvgRatingText = $item['AvgRating'];
      $PhotoUrl = $item['PicturePathLarge'];
      $url = "https://www.foody.vn".$item["BranchUrl"];
      //echo $Address.PHP_EOL.$AvgRatingText.PHP_EOL.$PhotoUrl;
      $base_arr["messages"][0]["attachment"]["payload"]["elements"][] = 
        [
          "title"=>$Name,
          "image_url"=>$PhotoUrl,
          "subtitle"=>"Địa chỉ: ".$Address.PHP_EOL."Đánh giá: ".$AvgRatingText,
          "buttons"=> [[
            "type"=>"web_url",
            "url"=>$url,
            "title"=>"Mở trên web"
          ],
          ["type"=>"element_share"]
          ]
        ];
    }

    $first_url = $searchItems[0]["PicturePathLarge"];
    $base_arr["messages"][0]["attachment"]["payload"]["elements"][0]["buttons"][] = 
      [
        "set_attributes" =>
        [
          "food_name_page"=>$page+1
        ],
        "title"=>"Xem thêm kết quả",
        "image_url"=>$first_url,
        "type"=>"show_block",
        "block_name"=>"query_name"
      ];

    echo json_encode($base_arr);
  }