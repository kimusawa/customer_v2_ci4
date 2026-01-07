<?php
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　Modeule           Spgsadmin　Controller
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2025.12.10  kimura       Mnt-000  CI4移行
//--------------------------------------------------------------------------
?>
<?= $this->extend('/layouts/admin_base'); ?>
<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?= base_url() ?>css/layouts/spgsadmin-upload.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>管理者用ページ：アップロードページ<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?= view('layouts/admin_header_button') ?>

<div class="content-wrapper" id="wrap">
  <div class="content" id="contents">

    <?= view('layouts/admin_header_menu') ?>

    <div class="pure-g" id="spgsadmin-kyotsu">
      <div class="pure-g" id="spgsadmin-kyotsu">
        <div class="l-box text-left pure-u-1">
          ログインユーザー<span>　<?= $login_id ?>様</span>
        </div>
      </div>
    </div>
    <div class="pure-g">
      <div class="pure-u-1-4">
      </div>
      <div class="is-center pure-u-1-2" id="spgsadmin-upload-fileup">
        <table align="center" class="" border="0" id="table01">
          <colgroup>
            <col class="col01" />
          </colgroup>

          <?php $attributes = array('id' => 'upload_exec'); ?>
          <?= form_open_multipart('spgsadmin/upload_exec', $attributes); ?>
          <tr>
            <td align="center"><img src="<?= base_url(); ?>images/toumei.gif" height="50" /></td>
          </tr>
          <tr>
            <td class="shokai-subtitle">照会用データファイル指定</td>
          </tr>
          <tr>
            <td class="text-center upload-area"><input type="file" id="userfile" name="userfile" size="30" class=""
                accept="text/plain" /></td>
          </tr>
          <tr>
            <td align="center"><img src="<?= base_url(); ?>images/toumei.gif" height="5" /></td>
          </tr>
          <tr>
            <td align="center"><input type="button" class="fileup_button pure-button topbutton" id="fileup_button"
                value="更新" /></td>
            <?= form_close(); ?>
          </tr>
          <tr>
            <td align="center">
              <span>
                <?php echo $error; ?>
                <?php if (session()->getFlashdata('error')): ?>
                  <p style="color:red"><?= session()->getFlashdata('error') ?></p>
                <?php endif; ?>
                <?php if (session()->getFlashdata('success')): ?>
                  <p style="color:green"><?= session()->getFlashdata('success') ?></p>
                <?php endif; ?>
              </span>
            </td>
          </tr>
        </table>
      </div>
      <div class="pure-u-1-4">
      </div>
    </div>
    <div class="pure-g">
    </div>
  </div>
  <div class="footer l-box is-center">
    SuperPGS for Web<!-- powered by ing corporation. -->
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="text/javascript">


  // Vanilla JS Event Delegation
  document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'fileup_button') {
      e.preventDefault();
      console.log('fileup_button clicked');

      var fileInput = document.getElementById('userfile');
      var files = fileInput.files;

      if (!files || files.length === 0) {
        swal({ title: "注意！", text: "ファイルが選択されていません。", icon: "info" });
        return false;
      }

      var uFile = files[0].name || '';

      if (uFile.indexOf("spgsuser") === -1 && uFile.indexOf("SPGSUSER") === -1) {
        swal({ title: "注意！", text: "ファイル名に［SPGSUSER］を含まないファイルのため\nアップロードできません。", icon: "info" });
        return false;
      }

      if (uFile.slice(-4).toLowerCase() !== ".txt") {
        swal({ title: "注意！", text: "ファイル拡張子が［TXT］でないファイルのため\nアップロードできません。", icon: "info" });
        return false;
      }

      var doSubmit = function () {
        // remove listener equivalent: nothing strictly needed for one-off submit, 
        // but let's ensure we don't double submit if we were attaching listeners.
        // in delegation, we just proceed.

        var loaderBg = document.getElementById('loader-bg');
        var loader = document.getElementById('loader');

        // Simple fade/display equivalent
        if (loaderBg) {
          loaderBg.style.display = 'block';
        }
        if (loader) {
          loader.style.display = 'block';
        }

        setTimeout(() => {
          if (loaderBg) loaderBg.style.opacity = 1;
          if (loader) loader.style.opacity = 1;

          console.log('submitting form #upload_exec');
          document.getElementById('upload_exec').submit();
        }, 10);
      };

      var sw = swal({
        title: "照会用データ読込・更新",
        text: "照会用データ読込・更新しますか？\n（お客様・個別）と間違いありませんか？",
        icon: "info",
        buttons: true,
        dangerMode: false
      });

      if (sw && typeof sw.then === 'function') {
        sw.then(function (willClear) {
          if (willClear) doSubmit();
        });
      } else {
        // sweetalert v1 fallback
        swal({
          title: "照会用データ読込・更新",
          text: "照会用データ読込・更新しますか？\n（お客様・個別）と間違いありませんか？",
          icon: "info",
          buttons: true,
          dangerMode: false
        }, function (willClear) {
          if (willClear) doSubmit();
        });
      }
    }
  });
</script>
<?= $this->endSection() ?>