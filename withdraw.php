<?php

require('function.php');

debug('———————————————————————————————————————————————');
debug('退会ページ');
debugLogStart();

$user_id = $_SESSION['user_id'];

require('auth.php');

if (!empty($_POST['withdraw'])) {
  debug('退会処理をします。');
  debug('POST送信があります。');

  try {
    $dbh = dbConnect();
    $sql1 = 'UPDATE users SET delete_flg=1 WHERE id=:user_id';
    $sql2 = 'UPDATE spots SET delete_flg=1 WHERE user_id=:user_id';
    $sql3 = 'DELETE FROM likes WHERE user_id=:user_id';

    $data = array(':user_id' => $user_id);
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);
    $stmt3 = queryPost($dbh, $sql3, $data);

    if ($stmt1) {
      session_destroy();
      debug('セッション変数の中身：' . print_r($_SESSION, true));
      debug('トップページへ遷移します。');
      header('Location:index.php');
    } else {
      debug('クエリが失敗しました。');
      $err_msg['common'] = MSG07;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
debug('画面表示処理終了');
debug('———————————————————————————————————————————————');
?>
<?php
$siteTitle = '退会';
require('head.php');
?>

<body>

  <?php
  require('header.php');
  ?>

  <div id="contents" class="contents">
    <section class="container">
      <div class="container__form">
        <h1 class="title">退会</h1>
        <form action="" method="post" class="form">
          <p class="form__withdraw__intro">本当に退会しますか？</p>
          <div class="area-msg">
            <?php
            if (!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <div class="form__btn">
            <input type="submit" class="btn btn-mid" value="退会" name="withdraw">
          </div>
        </form>
        <div class="to__mypage">
          <a href="mypage.php">キャンセル</a>
        </div>
      </div>
    </section>
  </div>

  <?php
  require('footer.php');
  ?>