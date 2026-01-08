<?php 
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　Modeule           Spgsinfo　Controller
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2025.02.04  tanaka       Mnt-007  請求書ボタンを表示する場合、PDFの存在する場合のみ表示する。
//2025.11.27  kimura       Mnt-000  CI4移行
//--------------------------------------------------------------------------
?>
<?= $this->extend('/layouts/base'); ?>

<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsinfo-kensin.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>お客様照会ページ：請求照会<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?= view('layouts/header_button') ?>

<!-- <script>
	function open_func(filepath, filename){
		a = document.createElement("a");
		document.body.appendChild(a);
		a.download = filename;
		a.href = "<?=base_url()?>seikyu/" + filepath;
		a.click();
		a.remove(); 
	}
</script> -->

<body>

<div class="content-wrapper">
    <div class="content seikyu-content">

		<?= view('layouts/header_menu') ?>

        <div class="pure-g" id="spgsinfo-kyotsu">
            <div class="l-box pure-u-1">
				ユーザーコード&nbsp;<br class="sp">
				<span class="usercode"><?=sprintf("%04d", $login_misecd);?>&nbsp;<?=$dspusercd?>&nbsp;</span><span class="kokyakumei"><br><?=$login_name?>&nbsp;様</span>
		    </div>
		</div>
	
<!-- ここから -->

	<div class="seikyu_syokai">
		<p>過去6ヶ月分の請求書がダウンロードできます。</p>

		<?php for($wki=0;$wki<count($files);$wki++):?>
		<button type="button" name="seikyu" class="seikyu_syokai_btn kari1" onclick="location.href='<?= base_url('spgsinfo/download_seikyu/' . basename($files[$wki]['path'])) ?>'">
			<i class="fa fa-file-text-o" aria-hidden="true"></i> 
			<?=$files[$wki]['y']?>年<?=$files[$wki]['m']?>月御請求書
		</button>
		<?php endfor?>
	</div>

<!-- ここまで -->

		<div class="space"></div>
	</div>
	<div class="footer l-box is-center">
<!--		SuperPGS for Web powered by ing corporation. -->
	</div>
</div>
</body>

<?= $this->endSection() ?>
