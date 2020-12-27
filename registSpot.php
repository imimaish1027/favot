<?php

require("function.php");

debug('———————————————————————————————————————————————');
debug('スポット登録ページ');
debugLogStart();

require("auth.php");

$spot_id = (isset($_GET['spot_id'])) ? $_GET['spot_id'] : '';
$db_form_data = (!empty($spot_id)) ? getSpot($_SESSION['user_id'], $spot_id) : '';
$edit_flg = (empty($db_form_data)) ? false : true;

debug('スポットID：' . $spot_id);
debug('フォーム用DBデータ：' . print_r($db_form_data, true));

if (!empty($spot_id) && empty($db_form_data)) {
  debug('GETパラメータのスポットIDが違います。マイページへ遷移します。');
  header('Location:mypage.php');
}

if (!empty($_POST)) {
  debug("POST送信があります。");
  debug("POST情報：" . print_r($_POST, true));
  debug("FILE情報：" . print_r($_FILES, true));

  $name = $_POST['name'];
  $address = $_POST['address'];
  $comment = $_POST['comment'];
  $tag = $_POST['tag'];

  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
  $pic = (empty($pic) && !empty($db_form_data['pic'])) ? $db_form_data['pic'] : $pic;

  if (empty($db_form_data)) {
    validRequired($name, 'name');
    validRequired($address, 'address');
    validMaxLen($name, 'name', 20);
    validMaxLen($address, 'address', 20);
    validMaxLen($comment, 'comment', 500);
  } else {
    if ($db_form_data['name'] !== $name) {
      validRequired($name, 'name');
      validMaxLen($name, 'name');
    }
    if ($db_form_data['comment'] !== $comment) {
      validMaxLen($comment, 'comment');
    }
  }

  if (empty($err_msg)) {
    debug("バリデーションOKです。");

    try {
      $dbh = dbConnect();

      if ($edit_flg) {
        debug("DB更新です。");
        $sql = 'UPDATE spots SET name = :name, address =:address, comment = :comment, tag = :tag, pic = :pic WHERE user_id = :user_id AND id = :spot_id';
        $data = array(':name' => $name, ':address' => $address, ':comment' => $comment, ':tag' => $tag, ':pic' => $pic, ':user_id' => $_SESSION['user_id'], ':spot_id' => $spot_id);
      } else {
        debug('DB新規登録です。');
        $sql = 'INSERT INTO spots (name, user_id, address, comment, tag, pic, create_date ) values (:name, :user_id, :address, :comment, :tag, :pic, :date)';
        $data = array(':name' => $name, ':address' => $address, ':comment' => $comment, ':tag' => $tag, ':pic' => $pic, ':user_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
      }
      debug('流し込みデータ：' . print_r($data, true));

      $stmt = queryPost($dbh, $sql, $data);

      if ($stmt) {
        //$_SESSION['msg_success'] = SUC04;
        debug("スポット一覧へ遷移します。");
        header("Location:spotList.php");
      }
    } catch (Exception $e) {
      error_log('エラー発生；' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('デバッグログ終了');
debug('———————————————————————————————————————————————');
?>

<?php
$siteTitle = (!$edit_flg) ? 'スポット登録' : 'スポット編集';
require('head.php');
?>

<body>

  <?php
  require('header.php');
  ?>

  <div id="contents" class="contents">

    <section class="wrapper">

      <div class="main">
        <h1 class="main__title"><?php echo (!$edit_flg) ? 'スポット登録' : 'スポット編集'; ?></h1>

        <div class="main__form">
          <form action="" method="post" class="form" enctype="multipart/form-data">
            <div class="form__one">
              <div class="area-msg">
                <?php
                if (!empty($err_msg['common'])) echo $err_msg['common'];
                ?>
              </div>
            </div>

            <div class="form__one">
              <label class="<?php if (!empty($err_msg['name'])) echo 'err'; ?>">
                <p class="form__title">スポット名</p>
                <input type="text" name="name" value="<?php echo $db_form_data['name'] ?>">
              </label>
              <div class="area-msg">
                <?php
                if (!empty($err_msg['name'])) echo $err_msg['name'];
                ?>
              </div>
            </div>

            <div class="form__one">
              <label class="<?php if (!empty($err_msg['address'])) echo 'err'; ?>">
                <p class="form__title">場所</p>
                <input type="text" name="address" value="<?php echo $db_form_data['address'] ?>">
              </label>
              <div class="area-msg">
                <?php
                if (!empty($err_msg['address'])) echo $err_msg['address'];
                ?>
              </div>

            </div>

            <div class="form__one">
              <label class="<?php if (!empty($err_msg['comment'])) echo 'err'; ?>">
                <p class="form__title">コメント</p>
              </label>
              <p class="counter-text">
                <textarea name="comment" id="js-count" cols="30" rows="10"><?php echo getFormData('comment'); ?></textarea>
                <span id="js-count-view">0</span>/500字以内</p>
              <div class="area-msg">
                <?php
                if (!empty($err_msg['comment'])) echo $err_msg['comment'];
                ?>
              </div>
            </div>

            <div class="form__one">
              <label class="<?php if (!empty($err_msg['tag'])) echo 'err'; ?>">
                <p class="form__title">タグ</p>
                <input type="text" name="tag" value="<?php echo $db_form_data['tag'] ?>">
              </label>

              <div class="area-msg">
                <?php
                if (!empty($err_msg['tag'])) echo $err_msg['tag'];
                ?>
              </div>
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

            <div class="form__btn">
              <input type="submit" class="btn btn-mid" value="<?php echo (!$edit_flg) ? '投稿する' : '更新する'; ?>">
            </div>
          </form>
        </div>

      </div>
  </div>
  </section>

  <?php
  require('footer.php');
  ?>