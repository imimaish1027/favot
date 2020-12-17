<?php

require('function.php');

debug('———————————————————————————————————————————————');
debug('ログインページ');
debugLogStart();

require('auth.php');

if (!empty($_POST)) {

  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;

  validRequired($email, 'email');
  validRequired($pass, 'pass');

  validEmail($email, 'email');
  validMaxLen($email, 'email');

  validHalf($pass, 'pass');
  validMaxLen($pass, 'pass');
  validMinLen($pass, 'pass');

  if (empty($err_msg)) {
    debug('バリデーションOKです。');

    try {
      $dbh = dbConnect();
      $sql = 'SELECT password,id FROM users WHERE email = :email';
      $data = array(':email' => $email);
      $stmt = queryPost($dbh, $sql, $data);

      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      debug('クエリ結果の中身：' . print_r($result, true));

      if (!empty($result) && password_verify($pass, array_shift($result))) {
        debug('パスワードがマッチしました。');

        $sesLimit = 60 * 60;
        $_SESSION['login_date'] = time();

        if ($pass_save) {
          debug('ログイン保持にチェックがあります。');
          $_SESSION['login_limit'] = $sesLimit * 24 * 30;
        } else {
          debug('ログイン保持にチェックはありません。');
          $_SESSION['login_limit'] = $sesLimit;
        }

        $_SESSION['user_id'] = $result['id'];
        debug('セッション変数の中身：' . print_r($_SESSION, true));
        debug('マイページへ遷移します。');
        header("Location:mypage.php");
      } else {
        debug('パスワードがアンマッチです。');
        $err_msg['common'] = MSG09;
      }
    } catch (Exception $e) {
      error_log('エラー発生：' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('デバッグログ終了');
debug('———————————————————————————————————————————————');
?>

<?php
$siteTitle = 'ログイン';
require('head.php');
?>

<body>

  <?php
  require('header.php');
  ?>

  <div id="contents" class="contents">
    <section class="container">

      <div class="container__form">
        <h1 class="title">ログイン</h1>

        <form action="" method="post" class="form">

          <div class="form__one">
            <div class="area-msg">
              <?php
              if (!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>

            <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
              <p class="form__title">メールアドレス</p>
              <input type="text" name="email" placeholder="メールアドレス" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">
            </label>

            <div class="area-msg">
              <?php
              if (!empty($err_msg['email'])) echo $err_msg['email'];
              ?>
            </div>
          </div>

          <div class="form__one">
            <label class="<?php if (!empty($err_msg['pass'])) echo 'err' ?>">
              <p class="form__title">パスワード</p>
              <input type="password" name="pass" placeholder="半角英数6文字以上" value="<?php if (!empty($_POST['pass'])) echo $_POST['pass']; ?>">
            </label>

            <div class="area-msg">
              <?php
              if (!empty($err_msg['pass'])) echo $err_msg['pass'];
              ?>
            </div>
          </div>

          <label class="form__keep">
            <input type="checkbox" name="pass_save">次回ログインを省略する
          </label>
          <div class="form__btn">
            <input type="submit" class="btn btn-mid" value="ログイン">
          </div>
        </form>
      </div>
    </section>
  </div>

  <?php
  require('footer.php');
  ?>