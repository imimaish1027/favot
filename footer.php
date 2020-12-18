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

    // テキストエリアカウント
    var $countUp = $('#js-count'),
      $countView = $('#js-count-view');
    $countUp.on('keyup', function(e) {
      $countView.html($(this).val().length);
    });
  });

  var $dropArea = $('.area-drop');
  var $fileInput = $('.input-file');
  $dropArea.on('dragover', function(e) {
    e.stopPropagation(); //余計なイベントをキャンセル
    e.preventDefault();
    $(this).css('border', '3px #ccc dashed');
  });
  $dropArea.on('dragleave', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', 'none');
  });
  $fileInput.on('change', function(e) {
    $dropArea.css('border', 'none');
    var file = this.files[0],
      $img = $(this).siblings('.prev-img'),
      fileReader = new FileReader();

    fileReader.onload = function(event) {
      $img.attr('src', event.target.result).show();
    };
    fileReader.readAsDataURL(file);

  });
</script>
</body>

</html>