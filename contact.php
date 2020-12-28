<?php

require('function.php');

debug('———————————————————————————————————————————————');
debug('お問い合わせページ');
debugLogStart();

$u_id = $_SESSION['user_id'];


require('auth.php');

debug('画面表示処理終了');
debug('———————————————————————————————————————————————');

$siteTitle = 'お問い合わせ';
require('head.php');

?>

<body>

  <?php
  require('header.php');
  ?>

  <div id="contents" class="contents">


    <h1 class="page-title">お問い合わせ</h1>

    <section class="contact">
      <form method="post" class="contact__form">

        <input type="text" name="email" placeholder="email" value="" class="contact__email">
        <input type="text" name="subject" placeholder="件名" value="" class="contact__subject">
        <textarea name="comment" placeholder='内容' class="contact__comment"></textarea>
        <input type="submit" value="送信" class="contact__submit">
        <?php /*
                <?php if(!empty($_POST["email"])) echo $_POST["email"];?>
                <?php if(!empty($_POST["subject"])) echo $_POST["subject"];?>
                <?php if(!empty($_POST["comment"])) echo $_POST["comment"];?>
                */ ?>
      </form>
    </section>
  </div>

  <?php
  require('footer.php');
  ?>