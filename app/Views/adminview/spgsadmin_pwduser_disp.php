<?php
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　Modeule           Spgsadmin　Controller
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2025.12.24  kimura       Mnt-000  CI4移行
//--------------------------------------------------------------------------
?>
<?= $this->extend('/layouts/admin_base'); ?>
<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?= base_url() ?>css/layouts/spgsadmin-pwduser-disp.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>管理者用ページ：ログイン情報照会<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?= view('layouts/admin_header_button') ?>

<div class="content-wrapper" id="wrap">
	<div class="content">

		<?= view('layouts/admin_header_menu') ?>

		<div class="pure-g" id="spgsadmin-kyotsu">
			<div class="l-box text-left pure-u-1">
				ログインユーザー<span>　<?= $login_id ?>様</span>
			</div>
		</div>

		<div class="pure-g" id="spgsjob-list-kensaku">
			<div class="l-box pure-u-1">
				<?php echo form_open('spgsadmin/pwduser_disp/0'); ?>
				<table id="table02" align="center">
					<colgroup>
						<col class="col01" />
						<col class="col02" />
						<col class="col03" />
						<col class="col04" />
						<col class="col05" />

					</colgroup>
					<tbody>
						<tr>
							<td class="td-title text-right"><label for="kensaku_usercd">得意先コード</label></td>
							<td class="td-input">
								<?php
								if ($kensaku_usercd != '') {
									$kensakuucd1 = substr($kensaku_usercd, 0, 2);
									$kensakuucd2 = substr($kensaku_usercd, 2, 4);
									$kensakuucd3 = substr($kensaku_usercd, 6, 3);
									if ($kensakuucd3 != '000') {
										$dspkensakuucd = $kensakuucd1 . '-' . $kensakuucd2 . '-' . $kensakuucd3;
									} else {
										$dspkensakuucd = $kensakuucd1 . '-' . $kensakuucd2;
									}
								} else {
									$dspkensakuucd = '';
								}
								?>
								<input type="text" id="kensaku_usercd" name="kensaku_usercd"
									value="<?= $dspkensakuucd ?>">
							</td>
							<td class="td-title text-right"><label for="kensaku_username">得意先名</label></td>
							<td class="td-input">
								<input type="text" id="kensaku_name" name="kensaku_name"
									value="<?= $kensaku_name ?>">
							</td>
							<td class="td-submit"><input type="submit" id="kensaku_submit"
									class="userpwd-change-button" name="kensaku_submit" value="検　索"></td>
						</tr>
					</tbody>
				</table>
				<?= form_close(); ?>
			</div>
		</div>

		<div class="pure-g">
			<div class="pure-u-1 spgsadmin-list">
				<?php
				$pn_disp = '';
				if ($list_total <= $list_limit)
					$pn_disp = 'display:none;';
				?>
				<div id="pagination" style="<?= $pn_disp ?>">
					<?php echo form_open('spgsadmin/pwduser_disp/0'); ?>
					<input type="hidden" id="kensaku_name" name="kensaku_name" value="<?= $kensaku_name ?>">
					<input type="hidden" id="kensaku_usercd" name="kensaku_usercd" value="<?= $kensaku_usercd ?>">
					<input type="submit" value="最初へ" class="pure-button pn-button">
					<?= form_close(); ?>
					<?php
					$page_before = $list_offset - $list_limit;
					$disabled1 = '';
					if ($list_offset >= $list_limit || $list_offset == 0) {
						if ($page_before < 0)
							$disabled1 = 'disabled';
					}
					if ($page_before <= 0)
						$page_before = 0;
					echo form_open('spgsadmin/pwduser_disp/' . $page_before);
					?>
					<input type="hidden" id="kensaku_name" name="kensaku_name" value="<?= $kensaku_name ?>">
					<input type="hidden" id="kensaku_usercd" name="kensaku_usercd" value="<?= $kensaku_usercd ?>">
					<input type="submit" value="前へ" <?= $disabled1 ?> class="pure-button pn-button">
					<?= form_close(); ?>
					<?php
					$page_next = $list_offset + $list_limit;
					$disabled2 = '';
					if ($page_next >= $list_total)
						$disabled2 = 'disabled';
					echo form_open('spgsadmin/pwduser_disp/' . $page_next);
					?>
					<input type="hidden" id="kensaku_name" name="kensaku_name" value="<?= $kensaku_name ?>">
					<input type="hidden" id="kensaku_usercd" name="kensaku_usercd" value="<?= $kensaku_usercd ?>">
					<input type="submit" value="次へ" <?= $disabled2 ?> class="pure-button pn-button">
					<?= form_close(); ?>
					<?php
					$page_end = $list_total - $list_limit;
					echo form_open('spgsadmin/pwduser_disp/' . $page_end);
					?>
					<input type="hidden" id="kensaku_name" name="kensaku_name" value="<?= $kensaku_name ?>">
					<input type="hidden" id="kensaku_usercd" name="kensaku_usercd" value="<?= $kensaku_usercd ?>">
					<input type="submit" value="最後へ" class="pure-button pn-button">
					<?= form_close(); ?>
					TOTAL：<?= $list_total ?>件
					<?= $list_offset + 1 ?>～<?= $list_offset + $list_limit ?>

					<?php if ($init_usercd != ''): ?>
						<span style="color:blue;">　得意先コード
							<?php
							$ucd1 = substr($init_usercd, 0, 2);
							$ucd2 = substr($init_usercd, 2, 4);
							$ucd3 = substr($init_usercd, 6, 3);
							if ($ucd3 != '000') {
								echo $ucd1 . '-' . $ucd2 . '-' . $ucd3;
							} else {
								echo $ucd1 . '-' . $ucd2;
							}
							?>
							のデータを初期化しました。</span>
					<?php endif; ?>

				</div>
				<?php if ($pn_disp != ''): ?>
					<div id="pagination">
						<?php if ($init_usercd != ''): ?>
							<span style="color:blue;">得意先コード
								<?php
								$ucd1 = substr($init_usercd, 0, 2);
								$ucd2 = substr($init_usercd, 2, 4);
								$ucd3 = substr($init_usercd, 6, 3);
								if ($ucd3 != '000') {
									echo $ucd1 . '-' . $ucd2 . '-' . $ucd3;
								} else {
									echo $ucd1 . '-' . $ucd2;
								}
								?>
								のデータを初期化しました。</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<table class="pure-table2 pure-table-striped2 pure-table-horizontal" id="table01">
					<colgroup>
						<col class="col01" />
						<col class="col02" />
						<col class="col03" />
					</colgroup>
					<thead>
						<tr>
							<th>得意先コード</th>
							<th>得意先名</th>
							<th><span class="pwd_change">パスワード変更</span><span class="pwd_reset">顧客情報</span></th>
						</tr>
					</thead>
					<tbody>
						<?php $cnt = 0; ?>
						<?php foreach ($query as $row): ?>

							<tr>
								<td>
									<?php
									if ($row['usercd'] != '') {
										$ucd1 = substr($row['usercd'], 0, 2);
										$ucd2 = substr($row['usercd'], 2, 4);
										$ucd3 = substr($row['usercd'], 6, 3);
										if ($ucd3 != '000') {
											$dspusercd = $ucd1 . '-' . $ucd2 . '-' . $ucd3;
										} else {
											$dspusercd = $ucd1 . '-' . $ucd2;
										}
									} else {
										$dspusercd = '';
									}
									echo $dspusercd;
									?>

								</td>
								<td>
									<?= $row['name'] ?>
								</td>
								<td class="flex">
									<?php echo form_open('spgsadmin/pwdchange_disp'); ?>
									<input type="submit" id="kensaku_submit" class="userpwd-change-button2"
										name="kensaku_submit" value="変　更">
									<input type="hidden" name="pwdchange_misecd" value="<?= $row['misecd'] ?>">
									<input type="hidden" name="pwdchange_usercd" value="<?= $row['usercd'] ?>">
									<?= form_close(); ?>

									<?php $attributes = array('id' => 'init_exec' . $cnt); ?>
									<?php echo form_open('spgsadmin/kokyaku_init', $attributes); ?>
									<input type="button" id="kokyaku_init_submit<?= $cnt ?>"
										class="userpwd-change-button3" name="kokyaku_init_submit" value="初期化">
									<input type="hidden" name="kokyakuinit_misecd" value="<?= $row['misecd'] ?>">
									<input type="hidden" name="kokyakuinit_usercd" value="<?= $row['usercd'] ?>">

									<input type="hidden" id="kensaku_name" name="kensaku_name"
										value="<?= $kensaku_name ?>">
									<input type="hidden" id="kensaku_usercd" name="kensaku_usercd"
										value="<?= $kensaku_usercd ?>">
									<input type="hidden" name="pwdchange_name" value="<?= $row['name'] ?>">
									<?= form_close(); ?>
								</td>
							</tr>
							<?php $cnt++; ?>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="footer l-box is-center">
			SuperPGS for Web<!-- powered by ing corporation. -->
		</div>
	</div>
	</body>

	<?= $this->endSection('content') ?>

	<?= $this->section('scripts') ?>
	<script type="text/javascript">
		document.addEventListener('click', function (e) {
			if (e.target && e.target.name === 'kokyaku_init_submit') {
				var id = e.target.id;
				// id format: kokyaku_init_submit0, kokyaku_init_submit1, etc.
				// we need the index.
				var idindex = id.replace("kokyaku_init_submit", "");

				swal({
					text: "この顧客データを初期化しますか？",
					icon: "info",
					buttons: true,
					dangerMode: false,
				}).then((willClear) => {
					if (willClear) {
						// loader handling (using global loader found in header by ID 'loader-bg')
						var loaderBg = document.getElementById('loader-bg');
						var loader = document.getElementById('loader'); // Assuming header has loader div inside loader-bg? 
						// Admin header menu has <div id="loader-bg"><div id="loader"></div></div>. Correct.

						if (loaderBg) {
							loaderBg.style.display = 'block';
						}
						if (loader) {
							loader.style.display = 'block';
						}

						setTimeout(() => {
							if (loaderBg) loaderBg.style.opacity = 1;
							if (loader) loader.style.opacity = 1;

							// Submit the form
							var form = document.getElementById('init_exec' + idindex);
							if (form) form.submit();
						}, 10);
					}
				});
			}
		});
	</script>
	<?= $this->endSection() ?>