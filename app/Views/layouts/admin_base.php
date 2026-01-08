<!doctype html>
<html lang="ja">
<head>
	<meta charset="utf-8">
  <title><?= $this->renderSection('title') ?: 'お客様照会ページ' ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">

  <!-- 共通CSS -->
  <link rel="stylesheet" href="<?=base_url()?>css/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="<?=base_url()?>css/pure/pure-min.css">
	<link rel="stylesheet" href="<?=base_url()?>css/pure/grids-responsive-min.css">
  <link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsadmin.css?<?php echo date("YmdHis"); ?>">
 	<link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsadmin-color.css?<?php echo date("YmdHis"); ?>">
  <link rel="icon" type="image/x-icon" href="<?=base_url()?>images/spgs/favicon_ing.ico">

  <!-- ページ専用CSS（なければ表示しない） -->
  <?= $this->renderSection('css') ?>

</head>

<body>
  <div class="header">
      <div class="home-menu pure-menu pure-menu-open pure-menu-horizontal pure-menu-fixed">
          <a class="pure-menu-heading" href="<?=$header_url01?>"><img src="<?=base_url()?>images/spgs/<?=$header_img01?>" title="for CodeIgniter ver.3.2.0-dev"></a>

          <ul>
              <li><a href="<?=$header_url02?>"><img src="<?=base_url()?>images/spgs/<?=$header_img02?>"></a></li>
          </ul>
      </div>
  </div>
  <header>
    <section class="header-inner">
      <?= $this->renderSection('headerButtons') ?>
    </section>
  </header>
  <main>
    <?= $this->renderSection('content') ?>
  </main>
  <footer>
      <section class="bg"></section>
  </footer>
  <?= $this->renderSection('scripts') ?>
    <script>
      window.addEventListener('DOMContentLoaded', function() {
        let form = document.getElementById("searchForm");
        let searchInput = document.getElementById("searchWord");
        if (searchInput) {
          searchInput.focus();
        }
      });

      window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
          const errorMessage = document.getElementById("errorMessage");
          if (errorMessage) {
            errorMessage.textContent = "";
            errorMessage.style.display = "none";
          }
          window.location.reload();
        }
      });
    </script>
</body>
</html>
