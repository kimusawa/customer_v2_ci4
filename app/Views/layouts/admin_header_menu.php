<div class="pure-g" id="spgsadmin-topmenu">
	<!-- 該当ページのボタンはthispageクラスを入れる -->
	<div class="l-box pure-u-1-5">
		<?php echo form_open('spgsadmin/upload/'); ?>
		<button type="submit" class="pure-button topbutton">照会用データ読込</button>
		<?= form_close(); ?>
	</div>
	<div class="l-box pure-u-1-5">
		<?php echo form_open('spgsadmin/userlogin_disp/'); ?>
		<button type="submit" class="pure-button topbutton">ログイン履歴照会</button>
		<?= form_close(); ?>
	</div>
	<div class="l-box pure-u-1-5">
		<?php echo form_open('spgsadmin/pwdmail/'); ?>
		<button type="submit" class="pure-button topbutton">管理者データ出力</button>
		<?= form_close(); ?>
	</div>
	<div class="l-box pure-u-1-5">
		<?php echo form_open('spgsadmin/pwduser_disp/0'); ?>
		<button type="submit" class="pure-button topbutton thispage">お客様パスワード変更・初期化</button>
		<?= form_close(); ?>
	</div>
	<div class="l-box pure-u-1-5">
		<?php echo form_open('spgsadmin/upload2/'); ?>
		<button type="submit" class="pure-button topbutton">照会用データ<br>（お客様 個別）読込</button>
		<?= form_close(); ?>
	</div>
</div>
<div class="pure-g" id="spgsadmin-topmenu-mobile">
	<!-- 該当ページのボタンはthispageクラスを入れる -->
	<button type="submit" class="">照会用データ読込</button>
	<button type="submit" class="">ログイン履歴照会</button>
</div>


<!-- ローディング画面 -->
<div id="loader-bg">
	<div id="loader">
		<img src="<?= base_url() ?>images/loading.gif" alt="Now Loading...">
		<p>データを更新中です。しばらくお待ち下さい。</p>
	</div>
</div>

<script>
	window.onload = function () {

		const loading = document.getElementById('loader-bg');
		const contents = document.getElementById('wrap');

		// 念のため存在チェック
		if (loading) loading.style.display = 'none';
		if (contents) contents.style.display = 'block';
	};
</script>
<script src="<?= base_url('javascript/sweetalert/sweetalert.min.js') ?>?<?= date('YmdHis') ?>"></script>