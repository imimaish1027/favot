<?php

require('function.php');

debug('———————————————————————————————————————————————');
debug('スポット一覧ページ');
debugLogStart();

$current_page_num = (!empty($_GET['p'])) ? $_GET['p'] : 1;

$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '0';

$url = $_SERVER["REQUEST_URI"];

// 表示件数
$list_span = 5;
// 現在の表示レコード先頭を算出
$current_min_num = (($current_page_num - 1) * $list_span);
// DBから商品データを取得
$db_spot_data = getSpotList($current_min_num, $sort);
debug('スポットデータ：' . print_r($db_spot_data, true));

debug('デバッグログ終了');
debug('———————————————————————————————————————————————');
?>
<?php
$siteTitle = 'スポット一覧';
require('head.php');
?>

<body>

  <?php
  require('header.php');
  ?>

  <div id="contents" class="contents">

    <section class="wrapper">
      <div class="main">
        <h1 class="main__title">スポット一覧</h1>
        <div class="sort__order">
          【　<a href="<?php echo (append($url_, 'sort=0')) ?>" class=<?php echo (strpos($url, 'sort=0')) ? 'sort-active' : ''; ?>>新着順</a>　|　
          <a href="<?php echo (append($url_, 'sort=1')) ?>" class=<?php echo (strpos($url, 'sort=1')) ? 'sort-active' : ''; ?>>いいね数順</a>　】
        </div>

        <div class="spot__list">
          <?php
          foreach ($db_spot_data['data'] as $key => $val) :
          ?>
            <div class="spot__one">
              <a href="spotDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam() . '&p_id=' . $val['id'] : '?p_id=' . $val['id']; ?>" class="spot__panel">
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

        <?php pagination($current_page_num, $db_spot_data['total_page']); ?>

      </div>
    </section>

  </div>

  <?php
  require('footer.php');
  ?>