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
				<span class="usercode"><?=sprintf("%04d", $login_misecd);?>&nbsp;<?=$login_dspusercd?>&nbsp;</span><span class="kokyakumei"><br><?=$login_name?>&nbsp;様</span>
		    </div>
		</div>

		<div class="pure-g">
            <div class="l-box pure-u-1-4 spacer">
            </div>
            <div class="l-box pure-u-xl-1-2 pure-u-lg-1-2 pure-u-md-1 pure-u-sm-1 pure-u-1">
<!--            <div class="l-box pure-u-1-2"> -->
				<h3 align="center">メールアドレス登録</h3>
				<p>
<!--
					お客様情報ページの使用にあたり、お客様のメールアドレス登録が必要となります。<br>
					恐れ入りますが、お客様がご利用のメールアドレスをご登録ください。<br>
-->
					お客様のメールアドレスをご登録頂けますと、当店より各種情報を随時メール配信いたします。<br>
					恐れ入りますが、お客様がご利用のメールアドレスをご登録ください。<br>
<!--					なお、個人情報の取扱いに関しては<a href="http://www.takasakigas.jp/privacy/">プライバシーポリシー</a>をご参照ください。<br> -->
<!--
					<?php

						if((preg_match('/red/',$errmsg))or(empty($errmsg))){
							echo '<p class="blue text-center">※メールアドレスをお持ちでない方は、下記電話番号にご連絡ください。<br>';
							echo 'ＴＥＬ：９９９－９９９－９９９９</p>';
						}
					?>
-->
				</p>

				<?php echo form_open('spgsinfo/mailentry'); ?>
					<table id="table01">
						<colgroup>
							<col class="col01" />
							<col class="col02" />
						</colgroup>
						<?php
							if(preg_match('/blue/',$errmsg)){
								$entry_complete = 'style="display:none;"';
							}else{
								$entry_complete = '';
							}
						?>
						<tbody>
							<tr <?=$entry_complete?>>
								<td>登録メールアドレス</td>
								<td><input type="text" name="newmail"></td>
						 	</tr>
							<tr <?=$entry_complete?>>
								<td>登録メールアドレス（確認）</td>
								<td><input type="text" name="newmail2"></td>
						 	</tr>
							<tr>
								<td colspan="2" class="text-center">&nbsp;<?=$errmsg?></td>
						 	</tr>
						 </tbody>
					</table>
					<input type="hidden" name="oldmail" value="">
					<?php
						if((preg_match('/red/',$errmsg))or(empty($errmsg)))
							 echo '<input type="submit" class="pure-button topbutton" value="メールアドレス登録" >';
					?>
				<?=form_close();?>
				<?php echo form_open('spgsinfo/menu/'); ?>
				<?=form_hidden('login_id', $login_id);?>
				<?=form_hidden('login_pwd', $login_pwd);?>
				<?php
					if(preg_match('/blue/',$errmsg))
						 echo '<input type="submit" class="pure-button topbutton" value="メニューへ" >';
				?>
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
