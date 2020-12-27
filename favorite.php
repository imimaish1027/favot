<?php

require('function.php');

debug('———————————————————————————————————————————————');
debug('いいね一覧ページ');
debugLogStart();

$user_id = $_SESSION['user_id'];
$like_data = getMyLike($user_id);
debug('お気に入りデータ：' . print_r($like_data, true));

require('auth.php');

debug('デバッグログ終了');
debug('———————————————————————————————————————————————');

$siteTitle = 'いいね一覧';
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
        <h1 class="main__title">いいね一覧</h1>
        <div class="spot__list">
          <?php
          foreach ($like_data as $key => $val) :
          ?>
            <div class="spot__one">
              <a href="spotDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam() . '&spot_id=' . $val['id'] : '?spot_id=' . $val['id']; ?>" class="spot__panel">
                <div class="spot__user">
                  <div class="user__avatar">
                    <img src="<?php print_r(showImg(getUserInPhoto($val['user_id']))); ?>" alt="" class="avatar">
                  </div>
                  <div class="user__name">
                    <?php echo sanitize($val['user_name']); ?>
                  </div>
                  <div class="user__create__day">
                    <?php echo substr(sanitize($val['create_date']), 0, 10); ?>
                  </div>
                </div>
                <div class="spot__info">
                  <img src="uploads/<?php echo sanitize($val['spot_pic']); ?>" alt="<?php echo sanitize($val['spot_name']) . "の画像"; ?>" class="spot__img">
                  <div class="spot__detail">
                    <p class="spot__title spot__detail__one"><?php echo sanitize($val['spot_name']); ?></p>
                    <div class="spot__address spot__detail__one">
                      <img src="img/mark.png" style="padding-right: 8px;">
                      <div style="padding-bottom: 2px;"><?php echo sanitize($val['address']); ?></div>
                    </div>
                    <p class="spot__tag spot__detail__one">#<?php echo sanitize($val['tag']); ?></p>
                  </div>
                </div>
              </a>
            </div>

          <?php
          endforeach;
          ?>
        </div>
      </div>
  </div>
  </section>

  </div>

  <?php
  require('footer.php');
  ?>