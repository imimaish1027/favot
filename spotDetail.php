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
$comment_data = getComments($spot_id);
$comment_amount = countComments($spot_id);

if (empty($spot_info)) {
  error_log('エラー発生：指定ページに不正な値が入りました。');
  header('Location:index.php');
}

if (!empty($_POST['comment'])) {
  debug('メッセージのPOST送信があります。');

  require('auth.php');

  $comment = (!empty($_POST['comment'])) ? $_POST['comment'] : '';
  commentSend($comment, $spot_id);
}

if (!empty($_POST['delete'])) {
  debug('POST送信があります。');
  //$b_id = $_POST['buy_del'];

  try {
    $dbh = dbConnect();
    $sql = 'UPDATE spots SET delete_flg = 1 WHERE id=:spot_id';
    $data = array(':spot_id' => $spot_id);
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('スポットを削除します。');
      header('Location:spotList.php');
    } else {
      debug('クエリが失敗しました。');
      $err_msg['common'] = MSG07;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
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
              <form action="" method="post">
                <input type="submit" class="btn btn-mid" value="削除" name="delete">
                <input type="submit" class="btn btn-mid" value="編集">
              </form>
            </div>
          </div>
        </div>

        <div class="comment">
          <h2 class="comment__title"><?php echo $comment_amount ?></h2>

          <?php
          foreach ($comment_data as $key => $val) {
          ?>
            <div class="comment__one">
              <div class="user__day">
                <div class="user__day__one">
                  <div class="user__avatar">
                    <img src="<?php echo isset($val['pic']) ? sanitize("uploads/" . $spot_user_info['user_pic']) : "img/no-avatar.jpeg"; ?>" alt="" class="avatar">
                  </div>
                  <div class="user__name">
                    <?php echo sanitize($val['name']); ?>
                  </div>
                </div>
                <div class="user__day__create">
                  <?php echo substr(sanitize($val['create_date']), 0, 10); ?>
                </div>
              </div>
              <div class="comment__detail">
                <?php echo sanitize($val['comment']); ?>
              </div>
            </div>
          <?php
          }
          ?>

          <div class="area__send__comment">
            <form action="" method="post" class="comment__area">
              <textarea name="comment" cols="50" rows="" class="msg"></textarea>
              <input type="submit" value="送信" class="btn btn-send">
            </form>
          </div>
        </div>
    </section>
  </div>

  <?php
  require('footer.php');
  ?>