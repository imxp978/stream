<?php 
header("Access-Control-Allow-Origin: *");  
header("Access-Control-Allow-Methods: GET"); 
header("Access-Control-Allow-Headers: Content-Type");  

$media = $_GET['media'];
$file = './'. $media . '.mp4';
$size = filesize($file);
$start = 0;
$end = $size - 1;

if (isset($_SERVER['HTTP_RANGE']) && $_SERVER['HTTP_RANGE'] !== '') {
  $range = explode('=', $_SERVER['HTTP_RANGE'])[1];
  $start = intval(explode('-', $range)[0]);
  $end = intval(explode('-', $range)[1]);
}
ob_get_clean();
header("Content-Type: video/mp4");
header("Content-Disposition: inline");
header('HTTP/1.1 206 Partial Content');
header("Content-Length: ". ($end - $start + 1));
header("Content-Range: bytes $start-$end/$size");
header("Cache-Control: max-age=2592000, public");
header("Expires: ".gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT');
header("Last-Modified: ".gmdate('D, d M Y H:i:s', @filemtime($file)) . ' GMT');
header("Accept-Ranges: bytes");

$video = fopen($file, 'rb');
fseek($video, $start);
$buffer = 1024000;
$position = ftell($video);

while ((!feof($video) && $position <= $end)) {
  if ($position + $buffer > $end) {
    $buffer = $end - $position + 1;
  }
  set_time_limit(0);
  echo fread($video, $buffer);
  flush();
}

fclose($video)


?>