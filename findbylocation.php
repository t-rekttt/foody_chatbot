<?php
#error_reporting(E_ALL); 
#ini_set('display_errors', 1);
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
require_once ROOT . 'vendor/autoload.php';
Requests::register_autoloader();

function _sendRequest($url)
{
  $res = Requests::get($url, array('X-Requested-With' => 'XMLHttpRequest'));
  return $res->status_code === 200 ? $res->body : FALSE;
}

if (isset($_GET['lat']) && isset($_GET['lon']) && isset($_GET['cityId'])) {
  $lat=$_GET['lat'];
  $lon=$_GET['lon'];
  $count=9;
  $type=3;
  $page=1;
  $cityId=$_GET['cityId'];
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
  header('Content-Type: application/json');
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
  //print_r($json);
  //echo $json["CityId"];
  foreach ($json['Items'] as $item) {
    $Name = $item["Name"];
    $Address = $item['Address'];
    $AvgRatingText = $item['AvgRatingText'];
    if ($AvgRatingText = '-.-') {
      $AvgRatingText = 'Chưa có';
    }
    $PhotoUrl = $item['PhotoUrl'];
    $url = "https://www.foody.vn".$item["Url"];
    //echo $Address.PHP_EOL.$AvgRatingText.PHP_EOL.$PhotoUrl;
    $base_arr["messages"][0]["attachment"]["payload"]["elements"][] = 
      [
        "title"=>$Name,
        "image_url"=>$PhotoUrl,
        "subtitle"=>"Address: ".$Address.PHP_EOL."Rating: ".$AvgRatingText,
        "buttons"=> [[
          "type"=>"web_url",
          "url"=>$url,
          "title"=>"Mở trên web"
        ]]
      ];
  }

  $base_arr["messages"][0]["attachment"]["payload"]["elements"][] = [
      "title"=>"Xem thêm những địa điểm khác",
      "buttons"=>[[
        "set_attributes" =>
        [
          "page"=>$page+1
        ],
        "title"=>"Xem thêm",
        "type"=>"show_block",
        "block_name"=>"query"
      ]]
    ];

  if (count($json["Items"])===0) {
    $base_arr = ["messages"=>[["text"=>"Đã hết địa điểm phù hợp với yêu cầu"]]];
  }
  echo json_encode($base_arr);
}
