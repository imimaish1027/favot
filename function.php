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
define('MSG06', '256文字以内で入力してください');
define('MSG07', 'エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG10', '半角数字のみご利用いただけます');

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
    $err_msg[$key] = MSG06;
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
