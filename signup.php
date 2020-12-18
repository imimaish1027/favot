<?php

require('function.php');

debug('——————————————————————');
debug('新規会員登録ページ');
debug('——————————————————————');
debugLogStart();

if (!empty($_POST)) {

  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');

  if (empty($err_msg)) {

    validEmail($email, 'email');
    validMaxLen($email, 'email');
    validEmailDup($email, 'email');

    validHalf($pass, 'pass');
    validMaxLen($pass, 'pass');
    validMinLen($pass, 'pass');

    if (empty($err_msg)) {

      validEmailDup($pass, $pass_re, 'pass_re');

      if (empty($err_msg)) {
        try {
          $dbh = dbConnect();
          $sql = "INSERT INTO users(email,password,login_time,create_date) VALUES(:email,:pass,:login_time,:create_date)";
          $data = array(
            ':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
            ':login_time' => date('Y-m-d H:i:s'),
            ':create_date' => date('Y-m-d H:i:s'),
          );
          $stmt = queryPost($dbh, $sql, $data);

          if ($stmt) {
            $sesLimit = 60 * 60;
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            $_SESSION['user_id'] = $dbh->lastInsertId();

            debug('セッション変数の中身：' . print_r($_SESSION, true));

            header("Location:mypage.php");
          }
        } catch (Exception $e) {
          error_log('エラー発生：' . $e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
}

?>

<?php
$siteTitle = '新規会員登録';
require('head.php');
?>

<body>

  <?php
  require('header.php');
  ?>

  <div id="contents" class="contents">
    <section class="container">

      <div class="container__form">
        <h1 class="title">新規会員登録</h1>

        <form action="" method="post" class="form">
          <div class="area-msg">
            <?php
            if (!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>

          <div class="form__one">
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

          <div class="form__one">
            <label class="<?php if (!empty($err_msg['pass_re'])) echo 'err' ?>">
              <p class="form__title">パスワード(確認)</p>
              <input type="password" name="pass_re" placeholder="半角英数6文字以上" value="<?php if (!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
            </label>

            <div class="area-msg">
              <?php
              if (!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
              ?>
            </div>
          </div>

          <div class="form__btn">
            <input type="submit" class="btn btn-mid" value="登録する">
          </div>
        </form>
      </div>
    </section>
  </div>

  <?php
  require('footer.php');
  ?>