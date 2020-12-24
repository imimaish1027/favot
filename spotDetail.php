<?php

require('function.php');

debug('———————————————————————————————————————————————');
debug('スポット詳細ページ');
debugLogStart();

$user_id = !empty($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
$spot_id = (!empty($_GET['spot_id'])) ? $_GET['spot_id'] : '';
$user_info = getUser($user_id);
$spot_info = getSpotOne($spot_id);
$spot_user_info = getSpotUser($spot_id);
debug('スポットユーザー：' . print_r($spot_user_info, true));
$msg_data = getMessages($spot_id);

if (empty($spot_info)) {
  error_log('エラー発生：指定ページに不正な値が入りました。');
  header('Location:index.php');
}

if (!empty($_POST['comment'])) {
  debug('メッセージのPOST送信があります。');

  require('auth.php');

  $msg = (!empty($_POST['comment'])) ? $_POST['comment'] : '';
  msgSend($comment, $spot_id);
}

debug('デバッグログ終了');
debug('———————————————————————————————————————————————');

?>

<?php
$siteTitle = 'スポット詳細';
require('head.php');
?>

<body>

  <?php
  require('header.php');
  ?>

  <div id="contents" class="contents">

    <section class="wrapper">
      <div class="main">

        <h1 class="main__title">スポット詳細</h1>
        <div class="spot">
          <div class="spot__info">
            <img src="uploads/<?php echo sanitize($spot_info['pic']); ?>" alt="<?php echo sanitize($spot_info['name']) . "の画像"; ?>" class="spot__img">
            <div class="spot__detail">
              <p class="spot__title spot__detail__one"><?php echo sanitize($spot_info['name']); ?></p>
              <div class="spot__address spot__detail__one">
                <img src="img/mark.png" style="padding-right: 8px;">
                <div style="padding-bottom: 2px;"><?php echo sanitize($spot_info['address']); ?></div>
              </div>
              <p class="spot__tag spot__detail__one">#<?php echo sanitize($spot_info['tag']); ?></p>
            </div>
          </div>
          <div class="user__day">
            <div class="user__day__one">
              <div class="user__avatar">
                <img src="<?php echo isset($spot_user_info['user_pic']) ? sanitize("uploads/" . $spot_user_info['user_pic']) : "img/no-avatar.jpeg"; ?>" alt="" class="avatar">
              </div>
              <div class="user__name">
                <?php echo sanitize($spot_user_info['user_name']); ?>
              </div>
            </div>
            <div class="user__day__create">
              <?php echo substr(sanitize($spot_info['create_date']), 0, 10); ?>
            </div>
          </div>
          <div class="user__comment"><?php echo sanitize($spot_info['comment']); ?></div>
          <div class="btn__area" style="<?php echo ($user_id !== $spot_info['user_id']) ? 'display: none;' : ''; ?>">
            <div class="form__btn">
              <input type="submit" class="btn btn-mid" value="削除">
              <input type="submit" class="btn btn-mid" value="編集">
            </div>
          </div>
        </div>

        <div class="comment">
          <h2 class="comment__title">コメント 件</h2>
          <div class="comment__one">
            <div class="user__day">
              <div class="user__day__one"></div>
              <div class="user__day__create">
                <?php echo substr(sanitize($spot_info['create_date']), 0, 10); ?>
              </div>
              <div class="comment__detail"></div>
            </div>
          </div>

          <div class="area-send-msg">
            <form action="" method="post">
              <textarea name="msg" cols="30" rows="3"></textarea>
              <input type="submit" value="送信" class="btn btn-send">
            </form>
          </div>
        </div>
    </section>
  </div>

  <?php
  require('footer.php');
  ?>