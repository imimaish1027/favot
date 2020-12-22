<?php

require('function.php');

debug('———————————————————————————————————————————————');
debug('スポット詳細ページ');
debugLogStart();


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
          <div class="user__day">
            <div class="user__day__one"></div>
            <div class="user__day__create">
              <?php echo substr(sanitize($val['create_date']), 0, 10); ?>
            </div>
          </div>
          <div class="user__comment"></div>
          <div class="btn__area"></div>
        </div>

        <div class="comment">
          <h2 class="comment__title">コメント 件</h2>
          <div class="comment__one">
            <div class="user__day">
              <div class="user__day__one"></div>
              <div class="user__day__create">
                <?php echo substr(sanitize($val['create_date']), 0, 10); ?>
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