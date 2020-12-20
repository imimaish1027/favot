<?php

ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');

$delete_flg = true;
// デバッグ
function debug($str)
{
  global $delete_flg;
  if (!empty($delete_flg)) {
    error_log('デバッグ：' . $str);
  }
}

session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
session_start();
session_regenerate_id();

function debugLogStart()
{
  debug('———————————————デバッグログスタート———————————————');
  debug('セッションID：' . session_id());
  debug('セッション変数の中身' . print_r($_SESSION, true));
  debug('現在日時タイムスタンプ：' . time());
}

// ——————————————————————————————
// エラーメッセージ
// ——————————————————————————————
define('MSG01', '入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03', 'パスワード（再入力）が合っていません');
define('MSG04', '半角英数字のみご利用いただけます');
define('MSG05', '6文字以上で入力してください');
define('MSG06', '255文字以内で入力してください');
define('MSG07', 'エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG10', '半角数字のみご利用いただけます');
define('MSG11', '20文字以内で入力してください');
define('MSG12', '500文字以内で入力してください');

// ——————————————————————————————
// グローバル変数
// ——————————————————————————————
$err_msg = array();

// ——————————————————————————————
// バリデーション関数
// ——————————————————————————————
// 未入力チェック
function validRequired($str, $key)
{
  if ($str === '') {
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
// Email形式チェック
function validEmail($str, $key)
{
  if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)) {
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
// Email重複チェック
function validEmailDup($email)
{
  global $err;

  try {
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM users WHERE email = :email';
    $data = array(':email' => $email);
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty(array_shift($result))) {
      $err_msg['email'] = MSG08;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
// 同値チェック
function validMatch($str1, $str2, $key)
{
  if ($str1 !== $str2) {
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
// 最小文字数チェック
function validMinLen($str, $key, $min = 6)
{
  if (mb_strlen($str) < $min) {
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}
// 最大文字数チェック
function validMaxLen($str, $key, $max = 255)
{
  if (mb_strlen($str) > $max) {
    global $err_msg;
    if ($key === 'name' || $key === 'address') {
      $err_msg[$key] = MSG11;
    } elseif ($key === 'comment')
      $err_msg[$key] = MSG12;
  }
}
// 半角文字チェック
function validHalf($str, $key)
{
  if (!preg_match("/^[a-zA-Z0-9]+$/", $str)) {
    global $err_msg;
    $err_msg[$key] = MSG10;
  }
}
// パスワードチェック
function validPass($str, $key)
{
  //半角英数字チェック
  validHalf($str, $key);
  //最大文字数チェック
  validMaxLen($str, $key);
  //最小文字数チェック
  validMinLen($str, $key);
}

// ——————————————————————————————
// データベース
// ——————————————————————————————
function dbConnect()
{
  $dsn = 'mysql:dbname=favot;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );

  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}
function queryPost($dbh, $sql, $data)
{
  global $err_msg;

  $stmt = $dbh->prepare($sql);

  if (!$stmt->execute($data)) {
    debug('クエリに失敗しました。');
    debug('失敗したSQL：' . print_r($stmt, true));
    $err_msg['common'] = MSG07;
    return 0;
  }
  debug('クエリ成功');
  return $stmt;
}

// ——————————————————————————————
// get関数
// ——————————————————————————————
// フォームデータ取得
function getFormData($str, $flg = false)
{
  if ($flg) {
    $method = $_GET;
  } else {
    $method = $_POST;
  }
  global $dbFormData, $err_msg;

  if (!empty($dbFormData)) {
    if (!empty($err_msg[$str])) {
      if (isset($method[$str])) {
        return sanitize($method[$str]);
      } else {
        return sanitize($dbFormData[$str]);
      }
    } else {
      if (isset($method[$str]) && $method[$str] !== $dbFormData[$str]) {
        return sanitize($method[$str]);
      } else {
        return sanitize($dbFormData[$str]);
      }
    }
  } else {
    if (isset($method[$str])) {
      return sanitize($method[$str]);
    }
  }
}
// 1個のスポット情報取得
function getSpot($user_id, $spot_id)
{
  debug('スポット情報を取得します。');
  debug('ユーザーID:' . $user_id);
  debug('スポットID:' . $spot_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM spots WHERE user_id=:u_id AND id=:s_id AND delete_flg = 0';
    $data = array(':u_id' => $user_id, ':p_id' => $spot_id);
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
//スポット一覧取得
function getSpotList($currentMinNum = 1, $sort, $span = 5)
{
  debug('スポット一覧を取得します。');
  try {
    $dbh = dbConnect();

    switch ($sort) {
      case 0:
        $sql = 'SELECT id FROM spots ORDER BY create_date DESC';
        break;
      case 1:
        $sql = 'SELECT id,COUNT(*) AS likes FROM spots INNER JOIN likes ON id = likes.spot_id GROUP BY spots.id ORDER BY likes DESC';
        break;
    }

    $data = array();
    $stmt = queryPost($dbh, $sql, $data);

    $rst['total'] = $stmt->rowCount();
    $rst['total_page'] = ceil($rst['total'] / $span);
    debug('=========:' . print_r($rst, true));

    if (!$stmt) {
      return false;
    }

    switch ($sort) {
      case 0:
        $sql = 'SELECT spots.id, spots.name AS spot_name, spots.address, spots.comment, spots.tag, spots.pic AS spot_pic, users.name AS user_name, users.pic AS user_pic, spots.create_date FROM spots INNER JOIN users ON spots.user_id = users.id ORDER BY spots.create_date DESC';
        break;
      case 1:
        $sql = 'SELECT spots.id, spots.name AS spot_name, spots.address, spots.comment, spots.tag, spots.pic AS spot_pic, users.name AS user_name, users.pic, COUNT(*) AS likes FROM spots INNER JOIN likes ON spots.id = likes.spot_id JOIN users ON spots.user_id = users.id GROUP BY spots.id ORDER BY likes DESC';
        break;
    }
    $sql .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
    $data = array();
    debug('SQL：' . $sql);
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
// ユーザー画像取得
function getUserInPhoto($user_id)
{
  debug('ユーザー画像を取得します。');

  try {
    $dbh = dbConnect();
    $sql = 'SELECT pic FROM users WHERE id = :u_id';
    $data = array(':u_id' => $user_id);
    $stmt = queryPost($dbh, $sql, $data);
    $rst = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($rst)) {
      return $rst['pic'];
    } else {
      debug('写真の取得に失敗しました。');
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

// ——————————————————————————————
// その他
// ——————————————————————————————
// パラメータ付与
function appendGetParam($arr_del_key = array())
{
  if (!empty($_GET)) {
    $str = '?';
    foreach ($_GET as $key => $val) {
      if (!in_array($key, $arr_del_key, true)) {
        $str .= $key . '=' . $val . '&';
      }
    }
    $str = mb_substr($str, 0, -1);
    return $str;
  }
}
// 並び順パラメータ付与
function append($url, $par)
{
  return 'spotList.php?' . $par;
}
// 画像アップロード
function uploadImg($file, $key)
{
  debug('画像アップロード処理開始');
  debug('FILE情報：' . print_r($file, true));

  if (isset($file['error']) && is_int($file['error'])) {
    try {

      switch ($file['error']) {
        case UPLOAD_ERR_OK: // OK
          break;
        case UPLOAD_ERR_NO_FILE:   // ファイル未選択の場合
          throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズが超過した場合
          throw new RuntimeException('ファイルサイズが大きすぎます');
        case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過した場合
          throw new RuntimeException('ファイルサイズが大きすぎます');
        default: // その他の場合
          throw new RuntimeException('その他のエラーが発生しました');
      }

      $type = @exif_imagetype($file['tmp_name']);
      if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
        throw new RuntimeException('画像形式が未対応です');
      }

      $path = 'uploads/' . sha1_file($file['tmp_name']) . image_type_to_extension($type);
      $pic =
        sha1_file($file['tmp_name']) . image_type_to_extension($type);

      if (!move_uploaded_file($file['tmp_name'], $path)) { //ファイルを移動する
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }
      chmod($path, 0644);

      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス：' . $pic);
      return $pic;
    } catch (RuntimeException $e) {

      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
  }
}
//画像表示
function showImg($path)
{
  if (empty($path)) {
    return 'img/no-avatar.jpeg';
  } else {
    return $path;
  }
}
// サニタイズ
function sanitize($str)
{
  return htmlspecialchars($str, ENT_QUOTES);
}
//ページネーション 
function pagination($currentPageNum, $totalPageNum, $link = '', $pageColNum = 5)
{
  if ($totalPageNum <= 5) {
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
    //総ページ数が5以上かつ現在のページが3,2,1の場合は1〜5を表示
  } elseif ($currentPageNum <= 3) {
    $minPageNum = 1;
    $maxPageNum = 5;
    //総ページ数が5以上かつ現在のページが総ページ-2,-1,-0の場合はラスト5個を表示
  } elseif ($currentPageNum >= $totalPageNum - 2) {
    $minPageNum = $totalPageNum - 4;
    $maxPageNum = $totalPageNum;
    //それ以外の場合は現在ページの前後2つを表示
  } else {
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }

  echo '<div class="pagination">';
  echo '<ul class="pagination-list">';
  if ($currentPageNum != 1) {     //現在のページが1以外の時
    echo '<li class="list-item"><a href="?p=1' . $link . '">&lt;</a></li>';
  }
  for ($i = $minPageNum; $i <= $maxPageNum; $i++) { //表示ページネーションのMinページ数をMaxページ数になるまでプラス
    echo '<li class="list-item ';
    if ($currentPageNum == $i) {
      echo 'active';
    }
    echo '"><a href="?p=' . $i . $link . '">' . $i . '</a></li>';
  }

  if ($currentPageNum != $maxPageNum && $maxPageNum > 1) {
    echo '<li class="list-item"><a href="?p=' . $maxPageNum . $link . '">&gt;</a></li>';
  }
  echo '</ul>';
  echo '</div>';
}
