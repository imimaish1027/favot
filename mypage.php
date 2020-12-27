<?php

require('function.php');

debug('———————————————————————————————————————————————');
debug('ユーザーページ');
debugLogStart();

$user_id = $_SESSION['user_id'];
$user_data = getUser($user_id);
$post_data = getPost($user_id);
$count_post_data = count($post_data['data']);
debug('投稿データ：' . print_r($post_data, true));
debug('投稿カウントデータ：' . print_r($count_post_data, true));
$like_data = getMyLike($user_id);
$count_like_data = count($like_data);
debug('いいねデータ：' . print_r($like_data, true));
debug('いいねカウントデータ：' . print_r($count_like_data, true));

require('auth.php');

debug('デバッグログ終了');
debug('———————————————————————————————————————————————');

$siteTitle = 'マイページ';
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
        <h1 class="main__title">マイページ</h1>
        <div class="main__user">
          <div class="user__prof">
            <img src="<?php echo isset($user_data['pic']) ? "uploads/" . $user_data['pic'] : "img/no-avatar.jpeg" ?>" class="user__prof--img">
            <div class="user__prof--info">
              <div class="info__one">
                <p class="info__title">ユーザー名</p>
                <p class="info__value"><?php echo (isset($user_data['name'])) ? sanitize($user_data['name']) : '名無し' ?></p>
              </div>
              <div class="info__one">
                <p class="info__title">住み</p>
                <p class="info__value"><?php echo (isset($user_data['address'])) ? sanitize($user_data['address']) : '不明' ?></p>
              </div>
              <div class="info__count">
                <p>
                  投稿数　<?php echo $count_post_data ?>件　　　いいね数　<?php echo $count_like_data ?>件
                </p>
              </div>
            </div>
          </div>
          <div class="user__msg">
            <?php echo (isset($user_data['introduction'])) ? sanitize($user_data['introduction']) : '' ?>
          </div>
        </div>
      </div>
  </div>
  </section>

  </div>

  <?php
  require('footer.php');
  ?>