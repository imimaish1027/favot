<footer id="footer">
  <div class="footer">
    <nav class="footer__nav">
      <ul class="nav__list">
        <li class="nav__one"><a href="index.php">TOP</a></li>
        <li class="nav__one"><a href="">ヘルプ</a></li>
        <li class="nav__one"><a href="contact.php">お問い合わせ</a></li>
      </ul>
    </nav>
  </div>

  <div class="footer__bottom">
    Copyright ©︎ Favot. All Rights Reserved.
  </div>
</footer>

<script src="js/vendor/jquery-2.2.2.min.js"></script>
<script>
  $(function() {
    // フッターを最下部に固定
    var $ftr = $('#footer');
    if (window.innerHeight > $ftr.offset().top + $ftr.outerHeight()) {
      $ftr.attr({
        'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'
      });
    }
  });
</script>
</body>

</html>