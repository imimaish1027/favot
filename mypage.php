<?php

require('function.php');

debug('———————————————————————————————————————————————');
debug('ユーザーページ');
debugLogStart();

$user_id = $_SESSION['user_id'];
//$userData = getUser($u_id);

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
            <img src="" class="user__prof--img">
            <div class="user__prof--info">
              <div class="info__one">
                <p class="info__title">ユーザー名</p>
                <p class="info__value">たろう</p>
              </div>
              <div class="info__one">
                <p class="info__title">住み</p>
                <p class="info__value">東京都</p>
              </div>
              <div class="info__count">
                <p>
                  投稿数　件　　　いいね数　件
                </p>
              </div>
            </div>
          </div>
          <div class="user__msg">

          </div>
        </div>
      </div>
  </div>
  </section>

  </div>

  <?php
  require('footer.php');
  ?>