<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

require_once("./conn_db.php");

$data =array(
  'success' => false,
  'message' => 'Not even started'
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  try {
    $title = !empty($_POST['title']) ? $_POST['title'] : NULL; 

    if (isset($_FILES['video']) && $_FILES['video']['error'] == UPLOAD_ERR_OK) {
      $sqlLastId = "SELECT MAX(id) AS 'lastid' FROM video";
      $queryLastId = $link->query($sqlLastId);
      $rowLastId = $queryLastId->fetch(PDO::FETCH_ASSOC);
      $lastId = ($rowLastId) ?intval($rowLastId['lastid']) : 0;
      echo $lastId;
      $newId = str_pad(($lastId + 1), 3, '0', STR_PAD_LEFT);
      $fileExtension = strrchr($_FILES['video']['name'],'.');
      $fileName = $newId. $fileExtension;
      // $fileName = $newFileName;
      $fileMoved = move_uploaded_file($_FILES['video']['tmp_name'], './media/'.$fileName);
      
      if($fileMoved) {
        $sql = "INSERT INTO video (title, video) VALUES (?, ?)";
        $params = [$title, $newId];
        $stmt = $link->prepare($sql);
        $query = $stmt->execute($params);

        if($query) {
          $sqlUpdate = "SELECT * FROM video";
          $queryUpdate = $link->query($sqlUpdate);
          $videoList = $queryUpdate->fetchAll(PDO::FETCH_ASSOC);
          if ($videoList) {
            $data = array(
              'success' => true,
              'message' => 'Video uploaded',
              'videoList' => $videoList,
            );
          }
        }
      } 
    }
  } catch(Exception $e) {
    $data = array(
      'success' => false,
      'message' => 'Upload failed'
    );
  }
}
echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>