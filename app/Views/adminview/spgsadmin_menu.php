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
<link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsadmin-menu.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>管理者用ページ：メニュー<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="header">
    <div class="home-menu pure-menu pure-menu-open pure-menu-horizontal pure-menu-fixed">
        <a class="pure-menu-heading" href="<?=$header_url01?>"><img src="<?=base_url()?>images/spgs/<?=$header_img01?>"></a>

        <ul>
            <li><a href="<?=$header_url02?>"><img src="<?=base_url()?>images/spgs/<?=$header_img02?>"></a></li>
            <li>
				<?php echo form_open('spgsadmin/logout/'); ?>
				<input type="submit" class="pure-button header-button" value="ログアウト" >
				<?=form_hidden('errmsg', 'ログアウトしました。');?>
				<?=form_close();?>
			</li>
        </ul>
    </div>
</div>
<div class="content-wrapper">
    <div class="content">
        <div class="pure-g" id="spgsadmin-kyotsu">
            <div class="l-box pure-u-1" id="login_user_label">
		ログインユーザー　<span><?=$login_id?>　様</span>
            </div>
        </div>
        <div class="pure-g" id="spgsadmin-topbutton">
            <div class="l-box pure-u-1-4 pc-only-button"></div>
            <div class="l-box pure-u-1-2 pc-only-button">
                <?php echo form_open('spgsadmin/upload/'); ?>
                <button type="submit" class="pure-button topbutton">照会用データ読込</button>
                <?=form_close();?>
            </div>
            <div class="l-box pure-u-1-4 pc-only-button"></div>
            <div class="l-box pure-u-1-4"></div>
            <div class="l-box pure-u-1-2">
                <?php echo form_open('spgsadmin/userlogin_disp/'); ?>
                <button type="submit" class="pure-button topbutton">閲覧履歴情報</button>
                <?=form_close();?>
            </div>
            <div class="l-box pure-u-1-4"></div>
            <div class="l-box pure-u-1-4 pc-only-button"></div>
            <div class="l-box pure-u-1-2 pc-only-button">
                <?php echo form_open('spgsadmin/pwdmail/'); ?>
                <button type="submit" class="pure-button topbutton">管理者データ出力</button>
                <?=form_close();?>
            </div>
            <div class="l-box pure-u-1-4 pc-only-button"></div>
            <div class="l-box pure-u-1-4 pc-only-button"></div>
            <div class="l-box pure-u-1-2 pc-only-button">
                <?php echo form_open('spgsadmin/pwduser_disp/'); ?>
                <button type="submit" class="pure-button topbutton">お客様パスワード変更・初期化</button>
                <?=form_close();?>
            </div>
            <div class="l-box pure-u-1-4 pc-only-button"></div>
            <div class="l-box pure-u-1-4 pc-only-button"></div>
            <div class="l-box pure-u-1-2 pc-only-button">
                <?php echo form_open('spgsadmin/upload2/'); ?>
                <button type="submit" class="pure-button topbutton">照会用データ（お客様 個別）読込</button>
                <?=form_close();?>
            </div>
            <div class="l-box pure-u-1-4 pc-only-button"></div>
        </div>
    </div>
    <div class="footer l-box is-center">
        SuperPGS for Web<!-- powered by ing corporation. -->
    </div>
</div>

<?= $this->endSection() ?>
