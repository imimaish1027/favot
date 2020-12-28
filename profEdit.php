<?php

require('function.php');

debug('———————————————————————————————————————————————');
debug('プロフィール編集ページ');
debugLogStart();

$db_form_data = getUser($_SESSION['user_id']);

require('auth.php');

if (!empty($_POST)) {
  debug('POST送信があります。');
  debug('POST情報：' . print_r($_POST, true));
  debug('FILE情報：' . print_r($_FILES, true));

  $name = $_POST['name'];
  $email = $_POST['email'];
  $introduction = $_POST['introduction'];
  $address = $_POST['address'];

  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
  $pic = (empty($pic) && !empty($db_form_data['pic'])) ? $db_form_data['pic'] : $pic;

  if ($db_form_data['name'] !== $name) {
    validMaxLen($name, 'name');
  }
  if ($db_form_data['address'] !== $address) {
    validMaxLen($address, 'address');
  }
  if ($db_form_data['email'] !== $email) {
    validMaxLen($email, 'email');
    if (empty($err_msg['email'])) {
      validEmailDup($email);
    }
    validEmail($email, 'email');
    validRequired($email, 'email');
  }
  if ($db_form_data['introduction'] !== $introduction) {
    validMaxLen($introduction, 'introduction');
  }

  if (empty($err_msg)) {
    debug('バリデーションOKです。');

    try {
      $dbh = dbConnect();
      $sql = 'UPDATE users SET name = :name, email = :email, introduction = :introduction, address = :address, pic = :pic WHERE id = :id';
      $data = array(':name' => $name, ':email' => $email, ':introduction' => $introduction, ':address' => $address, ':pic' => $pic, ':id' => $db_form_data['id']);
      debug('流し込みデータ：' . print_r($data, true));
      $stmt = queryPost($dbh, $sql, $data);

      if ($stmt) {
        debug('マイページへ遷移します。');
        header("Location:mypage.php");
      }
    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}

debug('デバッグログ終了');
debug('———————————————————————————————————————————————');

$siteTitle = 'プロフィール編集';
require('head.php');

?>

<body>

  <?php
  require('header.php');
  ?>

  <div id="contents" class="contents">

    <section class="wrapper">
      <?php
      require('sidebar.php');
      ?>

      <div class="main">
        <h1 class="main__title">プロフィール編集</h1>
        <form action="" method="post" class="form main__user" enctype="multipart/form-data">
          <div class="area-msg">
            <?php
            if (!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>

          <div class="imgDrop-container">
            <label class="area-drop <?php if (!empty($err_msg['pic'])) echo 'err'; ?>">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic" class="input-file">
              <img src="uploads/<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if (empty(getFormData('pic'))) echo 'display:none;' ?>">
              ドラッグ＆ドロップ
            </label>
            <div class="area-msg">
              <?php
              if (!empty($err_msg['pic'])) echo $err_msg['pic'];
              ?>
            </div>
          </div>

          <div class="form__one">
            <label class="<?php if (!empty($err_msg['name'])) echo 'err'; ?>">
              <p class="form__title">名前</p>
              <input type="text" name="name" placeholder="" value="<?php echo $db_form_data['name'] ?>">
            </label>

            <div class="area-msg">
              <?php
              if (!empty($err_msg['name'])) echo $err_msg['name'];
              ?>
            </div>
          </div>

          <div class="form__one">
            <label class="<?php if (!empty($err_msg['address'])) echo 'err'; ?>">
              <p class="form__title">住み</p>
              <input type="text" name="address" placeholder="" value="<?php echo $db_form_data['address'] ?>">
            </label>

            <div class="area-msg">
              <?php
              if (!empty($err_msg['address'])) echo $err_msg['address'];
              ?>
            </div>
          </div>

          <div class="form__one">
            <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
              <p class="form__title">メールアドレス</p>
              <input type="text" name="email" placeholder="" value="<?php echo $db_form_data['email'] ?>">
            </label>

            <div class="area-msg">
              <?php
              if (!empty($err_msg['email'])) echo $err_msg['email'];
              ?>
            </div>
          </div>

          <div class="form__one">
            <label class="<?php if (!empty($err_msg['introduction'])) echo 'err'; ?>">
              <p class="form__title">コメント</p>
            </label>
            <p class="counter-text">
              <textarea name="introduction" id="js-count" cols="30" rows="10"><?php echo getFormData('introduction'); ?></textarea>
              <span id="js-count-view">0</span>/500字以内</p>
            <div class="area-msg">
              <?php
              if (!empty($err_msg['introduction'])) echo $err_msg['introduction'];
              ?>
            </div>
          </div>

          <div class="form__btn">
            <input type="submit" class="btn btn-mid" value="編集">
          </div>
        </form>
      </div>
  </div>
  </section>

  </div>

  <?php
  require('footer.php');
  ?>