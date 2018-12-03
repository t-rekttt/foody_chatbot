<?php
#error_reporting(E_ALL); 
#ini_set('display_errors', 1);
require_once("citymatch.php");
require_once("pre-funcs.php");
/*define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
require_once ROOT . 'vendor/autoload.php';
Requests::register_autoloader();

function _sendRequest($url)
{
  $res = Requests::get($url, array('X-Requested-With' => 'XMLHttpRequest'));
  return $res->status_code === 200 ? $res->body : FALSE;
}*/

header('Content-Type: application/json');
function find_old() {
  if (isset($_GET['lat']) && isset($_GET['lon']) /*&& isset($_GET['cityId'])*/) {
    $lat=$_GET['lat'];
    $lon=$_GET['lon'];
    $count=10;
    $type=3;
    $page=1;
    $cityId= city_match($lat,$lon);
    if ($cityId=="0") {
      $messages = ["messages"=> 
          [
            [
              "attachment"=>[
                "type"=> "template",
                "payload"=>[
                  "template_type"=>"button",
                  "text"=>"Vị trí bạn cần tìm chưa có trên hệ thống",
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
      exit();
    }
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    }
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
        "set_attributes"=>
          [
            "cityId"=>$cityId
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
    $json = json_decode($data,true);
    //print_r($json);
    //echo $json["CityId"];
    if (count($json["Items"])===0) {
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

    foreach ($json['Items'] as $item) {
      $Name = $item["Name"];
      $Address = $item['Address'];
      $AvgRatingText = $item['AvgRatingText'];
      $PhotoUrl = $item['PhotoUrl'];
      $url = "https://www.foody.vn".$item["Url"];
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

    $first_url = $json['Items'][0]["PhotoUrl"];
    /*$base_arr["messages"][0]["attachment"]["payload"]["elements"][] = [
        "title"=>"Xem thêm những địa điểm khác",
        "subtitle"=>" ",
        "image_url"=>$first_url,
        "buttons"=>[[
          "set_attributes" =>
          [
            "page"=>$page+1
          ],
          "title"=>"Xem thêm",
          "image_url"=>$first_url,
          "type"=>"show_block",
          "block_name"=>"query"
        ]]
      ];*/
    $base_arr["messages"][0]["attachment"]["payload"]["elements"][0]["buttons"][] = 
        [
          "set_attributes" =>
          [
            "page"=>$page+1
          ],
          "title"=>"Xem thêm địa điểm",
          "image_url"=>$first_url,
          "type"=>"show_block",
          "block_name"=>"query"
        ];
    echo json_encode($base_arr);
  }
}

function find_new() {
  if (isset($_GET["string"])) {
    $string=$_GET["string"];
    $count=10;
    $type=3;
    $page=1;
    $match= city_match_new($string);
    if (!$match["status"]) {
      $messages = ["messages"=> 
          [
            [
              "attachment"=>[
                "type"=> "template",
                "payload"=>[
                  "template_type"=>"button",
                  "text"=>"Vị trí bạn cần tìm chưa có trên hệ thống",
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
      exit();
    }
    $lat = $match["lat"];
    $lon = $match["long"];
    $cityId = $match["cityId"];
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    }
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
        "set_attributes"=>
          [
            "cityId"=>$cityId
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
    $json = json_decode($data,true);
    if (count($json["Items"])===0) {
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

    foreach ($json['Items'] as $item) {
      $Name = $item["Name"];
      $Address = $item['Address'];
      $AvgRatingText = $item['AvgRatingText'];
      $PhotoUrl = $item['PhotoUrl'];
      $url = "https://www.foody.vn".$item["Url"];
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

    $first_url = $json['Items'][0]["PhotoUrl"];
    /*$base_arr["messages"][0]["attachment"]["payload"]["elements"][] = [
        "title"=>"Xem thêm những địa điểm khác",
        "subtitle"=>" ",
        "image_url"=>$first_url,
        "buttons"=>[[
          "set_attributes" =>
          [
            "page"=>$page+1
          ],
          "title"=>"Xem thêm",
          "image_url"=>$first_url,
          "type"=>"show_block",
          "block_name"=>"query"
        ]]
      ];*/
    $base_arr["messages"][0]["attachment"]["payload"]["elements"][0]["buttons"][] = 
        [
          "set_attributes" =>
          [
            "page"=>$page+1
          ],
          "title"=>"Xem thêm địa điểm",
          "image_url"=>$first_url,
          "type"=>"show_block",
          "block_name"=>"query"
        ];
    echo json_encode($base_arr);
  }
}

find_old();
find_new();