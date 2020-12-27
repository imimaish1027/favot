<?php
require('function.php');

debug('———————————————————————————————————————————————');
debug('　Ajax　');
debugLogStart();

if (isset($_POST['spotId']) && isset($_SESSION['user_id'])) {
  debug('POST送信があります。');
  $spot_id = $_POST['spotId'];
  debug('スポットID：' . $spot_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM likes WHERE spot_id = :spot_id AND user_id = :user_id';
    $data = array(':user_id' => $_SESSION['user_id'], ':spot_id' => $spot_id);
    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug($resultCount);

    if (!empty($resultCount)) {
      $sql = 'DELETE FROM likes WHERE spot_id = :spot_id AND user_id = :user_id';
      $data = array(':user_id' => $_SESSION['user_id'], ':spot_id' => $spot_id);
      $stmt = queryPost($dbh, $sql, $data);
    } else {
      $sql = 'INSERT INTO likes (spot_id, user_id, create_date) VALUES (:spot_id, :user_id, :date)';
      $data = array(':user_id' => $_SESSION['user_id'], ':spot_id' => $spot_id, ':date' => date('Y-m-d H:i:s'));
      $stmt = queryPost($dbh, $sql, $data);
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
debug('Ajax処理終了');
debug('———————————————————————————————————————————————');
