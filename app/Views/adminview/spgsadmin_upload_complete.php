<?php 
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　Modeule           Spgsadmin　Controller
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2025.12.26  kimura       Mnt-000  CI4移行
//--------------------------------------------------------------------------
?>
<?= log_message('debug', '★AdminSpgsController adminview/spgsadmin_upload_complete 実行'); ?>
<?= $this->extend('/layouts/admin_base'); ?>
<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsadmin-upload.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>管理者用ページ：アップロード完了ページ<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?= view('layouts/admin_header_button') ?>

<body>
<div class="content-wrapper">
    <div class="content">
		<?= view('layouts/admin_header_menu') ?>
        <div class="pure-g" id="spgsadmin-kyotsu">
        <div class="pure-g" id="spgsadmin-kyotsu">
            <div class="l-box text-left pure-u-1">
				ログインユーザー<span>　<?=$login_id?>　様</span>
		    </div>
		</div>
		</div>
        <div class="pure-g">
	        <div class="l-box-lrg is-center pure-u-1" id="spgsadmin-upload-fileup">
				更新完了しました。
		    </div>
	    </div>
		<div class="pure-g">
		</div>
	</div>
	<div class="footer l-box is-center">
		SuperPGS for Web<!-- powered by ing corporation. -->
	</div>
</div>
</body>

<?= $this->endSection() ?>

