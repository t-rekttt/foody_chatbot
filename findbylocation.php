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

if (isset($_GET['lat']) && isset($_GET['lon'])) {
  $lat=$_GET['lat'];
  $lon=$_GET['lon'];
  $count=9;
  $type=3;
  $page=1;
  if (isset($_GET['page'])) {
    $page = $_GET['page'];
  }
  $query_arr = array(
    'lat'=>$lat,
    'lon'=>$lon,
    'count'=>$count,
    'type'=>$type,
    'page'=>$page,
  );
  $query_str = http_build_query($query_arr);
  header('Content-Type: application/json');
  $data = _sendRequest('https://www.foody.vn/__get/Place/HomeListPlace?'.$query_str);
  echo $data;
}
