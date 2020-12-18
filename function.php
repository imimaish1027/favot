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
function getProduct($user_id, $spot_id)
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

// ——————————————————————————————
// その他
// ——————————————————————————————
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
// サニタイズ
function sanitize($str)
{
  return htmlspecialchars($str, ENT_QUOTES);
}
