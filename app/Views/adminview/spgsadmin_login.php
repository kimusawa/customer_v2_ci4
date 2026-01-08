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
<link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsadmin-login.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>管理者用ページ：ログイン<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content">
        <div class="pure-g" id="spgsadmin-login-form">
	        <div class="pure-u-1">
				<?php echo form_open('spgsadmin/menu'); ?>
					<table id="table01" align="center">
						<colgroup>	
							<col class="col01" />
							<col class="col02" />
						</colgroup>	
						<tbody>
							<tr>
								<td colspan="2" class="text-center login-title">管理者用ページ：ログイン</td>
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
							</tr><tr>
								<td colspan="2"><input type="submit" class="pure-button login-button" value="ログイン"></td>
							</tr><tr>
								<td colspan="2" class="text-center blue">
									&nbsp;
									<?php if (session()->getFlashdata('error')): ?>
										<span class="error-message"><?= session()->getFlashdata('error') ?></span>
									<?php endif; ?>
									&nbsp;</td>
							</tr>
						</tbody>
					</table>
				<?=form_close();?>
		    </div>
	    </div>
	</div>
	<div class="footer l-box is-center">
		SuperPGS for Web<!-- powered by ing corporation. -->
	</div>
</div>

<?= $this->endSection() ?>
