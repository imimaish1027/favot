<?php

$user_id = !empty($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

?>

<header id="header">
  <div class="header">
    <h1 class="header__logo"><a href="index.php">Favot</a></h1>
    <nav class="header__nav">
      <ul class="nav__list">
        <?php
        if (empty($_SESSION['user_id'])) {
        ?>
          <li class="nav__one"><a href="">テストログイン</a></li>
          <li class="nav__one"><a href="login.php">ログイン</a></li>
          <li class="nav__one"><a href="signup.php">新規登録</a></li>
        <?php
        } else {
        ?>
          <li class="nav__one"><a href="logout.php">ログアウト</a></li>
          <li class="nav__one"><a href="mypage.php">マイページ</a></li>
          <li class="nav__one"><a href="spot.php">スポット一覧</a></li>
          <li class="nav__one"><a href="registSpot.php">投稿</a></li>
        <?php
        }
        ?>
      </ul>
    </nav>
  </div>
</header>