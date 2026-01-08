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
<?= log_message('debug', '★AdminSpgsController adminview/spgsadmin_pwduserchange_disp 実行'); ?>
<?= $this->extend('/layouts/admin_base'); ?>
<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?= base_url() ?>css/layouts/spgsadmin-pwduser-disp.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>管理者用ページ：ログイン情報照会<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?= view('layouts/admin_header_button') ?>

<body>
	<div class="content-wrapper">
		<div class="content">
			<?= view('layouts/admin_header_menu') ?>
			<div class="pure-g" id="spgsadmin-kyotsu">
				<div class="l-box text-left pure-u-1">
					ログインユーザー<span>　<?= $login_id ?>様</span>
				</div>
			</div>

			<div class="pure-g">
				<div class="userpwd-change-date">
					<p>得意先コード：<?= $dspusercd; ?></p>
					<p>得意先名：<?= $name; ?> 様</p>
				</div>
			</div>


			<div class="pure-g">
				<div class="l-box pure-u-1-4 spacer">
				</div>
				<div class="l-box pure-u-xl-1-2 pure-u-lg-1-2 pure-u-md-1 pure-u-sm-1 pure-u-1">
					<h3 align="center">パスワード変更</h3>
					<?php echo form_open('spgsadmin/pwdupdate'); ?>
					<table id="table03">
						<colgroup>
							<col class="col01" />
							<col class="col02" />
						</colgroup>
						<tbody>
							<tr>
								<td colspan="2" class="text-center">※パスワードは英数半角６文字以上でお願いします。<br>※英字の大文字／小文字は識別されます。<br>
								</td>
							</tr>
							<tr>
								<td>現在のパスワード</td>
								<td><input type="text" name="oldpwd" id="oldpwd" value="<?= $pwd ?>"></td>
							</tr>
							<tr>
								<td>新しいパスワード</td>
								<td><input type="password" name="newpwd" id="newpwd"></td>
							</tr>
							<tr>
								<td colspan="2" class="text-center">&nbsp;<?= $errmsg ?></td>
							</tr>
						</tbody>
					</table>
					<input type="submit" class="pure-button topbutton" id="pwd_change_submit" value="パスワード変更">
					<input type="hidden" name="pwdchange_misecd" value="<?= $misecd ?>">
					<input type="hidden" name="pwdchange_usercd" value="<?= $usercd ?>">
					<?= form_close(); ?>
				</div>
				<div class="l-box pure-u-1-4  spacer">
				</div>
			</div>
		</div>
		<div class="footer l-box is-center">
			SuperPGS for Web<!-- powered by ing corporation. -->
		</div>
	</div>
</body>

<?= $this->endSection() ?>