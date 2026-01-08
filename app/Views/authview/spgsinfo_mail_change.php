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
//2025.12.02  kimura       Mnt-008  CI4対応
//--------------------------------------------------------------------------
?>
<?= $this->extend('/layouts/base'); ?>
<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsinfo-mail-change.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>お客様照会ページ<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?= view('layouts/header_button') ?>

<div class="content-wrapper">
    <div class="content">

		<?= view('layouts/header_menu') ?>

        <div class="pure-g" id="spgsinfo-kyotsu">
            <div class="l-box pure-u-1">
				ユーザーコード&nbsp;<br class="sp">
				<span class="usercode"><?=sprintf("%04d", $login_misecd);?>&nbsp;<?=$dspusercd?>&nbsp;</span><span class="kokyakumei"><br><?=$login_name?>&nbsp;様</span>
		    </div>
		</div>

		<div class="pure-g">
            <div class="l-box pure-u-1-4 spacer">
            </div>
            <div class="l-box pure-u-xl-1-2 pure-u-lg-1-2 pure-u-md-1 pure-u-sm-1 pure-u-1">
				<h3 align="center">メールアドレス変更</h3>
				<?php echo form_open('spgsinfo/mailupdate'); ?>
					<table id="table01">
						<colgroup>
							<col class="col01" />
							<col class="col02" />
						</colgroup>
						<tbody>
							<tr>
								<td>現在のメールアドレス</td>
								<td><?=$oldmail?><input type="hidden" name="oldmail" value="<?=$oldmail?>"></td>
						 	</tr>
							<tr>
								<td>新しいメールアドレス</td>
								<td><input type="text" name="newmail"></td>
						 	</tr>
							<tr>
								<td>新しいメールアドレス（確認）</td>
								<td><input type="text" name="newmail2"></td>
						 	</tr>
							<tr>
								<td colspan="2" class="text-center">&nbsp;<?=$errmsg?></td>
						 	</tr>
						 </tbody>
					</table>
					<input type="submit" class="pure-button topbutton" value="メールアドレス変更" >
				<?=form_close();?>
            </div>
            <div class="l-box pure-u-1-4 spacer">
            </div>
	    </div>
	</div>
	<div class="footer l-box is-center">
<!--		SuperPGS for Web powered by ing corporation. -->
	</div>
</div>

<?= $this->endSection() ?>
