<?php 
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　VIEW：ログイン画面
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2025.11.21  kimura       Mnt-000  CI4移行
//--------------------------------------------------------------------------
?>
<?= $this->extend('/layouts/base'); ?>

<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsinfo-login.css?<?= date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>お客様照会ページ<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content">
        <div class="pure-g" id="spgsinfo-login-form">
	        <div class="pure-u-1">
				<?php echo form_open('spgsinfo/menu'); ?>
					<table id="table01">
						<colgroup>	
							<col class="col01" />
							<col class="col02" />
						</colgroup>	
						<tbody>
							<tr>
								<td colspan="2" class="text-center login-title">お客様情報ページ：ログイン</td>
							</tr><tr>
								<td>ログインＩＤ</td>
								<td><input type="text" name="login_id" value="<?php echo set_value('login_id'); ?>" id="id-input" class="imedisabled" /></td>
							</tr><tr>
								<td>&nbsp;</td>
								<td class="error-disp"><?= isset($validation) ? $validation->showError('login_id') : '' ?></td>
							</tr><tr>
								<td>パスワード</td>
								<td><input type="password" name="login_pwd" value="<?php echo set_value('login_pwd'); ?>" id="pwd-input" class="imedisabled" /></td>
							</tr><tr>
								<td>&nbsp;</td>
								<td class="error-disp"><?= isset($validation) ? $validation->showError('login_pwd') : '' ?></td>
							</tr>
						</tbody>
					</table>
					<div class="login-w">
						<input type="submit" class="pure-button login-button" value="ログイン">
							<p>
								&nbsp;
								<?php if (session()->getFlashdata('error')): ?>
									<span class="error-message"><?= session()->getFlashdata('error') ?></span>
								<?php endif; ?>
								&nbsp;
							</p>
					</div>
					<div class="login-w2">
						<p class="hajimete-t">初めてご利用のお客様へ</p>
						<p class="hajimete-p">先日お客様にご連絡させていただきました、ログインIDと初期パスワードを入力してください。<br>
						<span style="color:red;">※ログインIDと初期パスワードが分からないお客様は当店までお問い合わせください。</span>
						</p>
					</div>


				<?=form_close();?>
		    </div>
	    </div>
	</div>
	
	<div class="footer l-box is-center">
		<?php if($dgf_flg == 1): ?>
				<form method="get" action="<?= site_url('spgsinfo/toksho') ?>">
						<button type="submit" class="tokusyoho-btn">特定商取引に基づく表記</button>
				</form>
		<?php else: ?>
				SuperPGS for Web powered by ing corporation.
		<?php endif; ?>
	</div>
</div>

<?= $this->endSection() ?>
