<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");

require_once('conn_db.php');

try{
  $sql = "SELECT * FROM video ORDER BY id DESC";
  $query = $link->query($sql);
  if ($query) {
    $videoList = $query->fetchAll(PDO::FETCH_ASSOC);
    if ($videoList && !empty($videoList)) {
      $data = array (
        'success'=>true,
        'message'=>'Video got',
        'videoList'=>$videoList,
      );
    }
  }
} catch(Exception $e) {
  $data = array(
    'success' => false,
    'message' => 'Error: '. $e->getMessage(),
  );

}
echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>