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
<link rel="stylesheet" href="<?= base_url() ?>css/layouts/spgsadmin-userlogin-disp.css?<?php echo date("YmdHis"); ?>">
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
				<?php echo form_open('spgsadmin/userlogin_disp/0', ['method' => 'get']); ?>
				<table id="table02" align="center">
					<colgroup>
						<col class="col01" />
						<col class="col02" />
						<col class="col03" />
						<col class="col04" />
						<col class="col05" />
						<col class="col06" />
					</colgroup>
					<tbody>
						<tr>
							<td class="td-title text-right">
								<label for="kensaku_ymd">日付</label>
							</td>
							<td class="td-input">
								<?php
								if ($kensaku_ymd != '') {
									$kensakuy = substr($kensaku_ymd, 0, 4);
									$kensakum = substr($kensaku_ymd, 4, 2);
									$kensakud = substr($kensaku_ymd, 6, 2);
									$dspkensakuymd = $kensakuy . '/' . $kensakum . '/' . $kensakud;
								} else {
									$dspkensakuymd = '';
								}
								?>
								<input type="text" id="kensaku_ymd" name="kensaku_ymd" value="<?= $dspkensakuymd ?>">
							</td>
							<td class="td-title text-right"><label for="kensaku_usercd">コード</label></td>
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

							<td class="td-submit">
								<button type="submit" class="spgsadmin-color02" name="kensaku_submit" value="search">
									検　索　開　始
								</button>
							</td>

							<!-- 20240129 csvダウンロードボタン設置 -->
							<td class="td-submit">
								<button type="submit" class="spgsadmin-color02 space-left" name="csv_submit"
									value="csv">
									CSVダウンロード
								</button>
							</td>
						</tr>
					</tbody>
				</table>
				<?= form_close(); ?>
			</div>
		</div>

		<div class="pure-g">
			<div class="pure-u-1 mobile-only-title text-center">
				<h3>ユーザーログイン履歴</h3>
			</div>
			<div class="pure-u-1 spgsadmin-list">


				<?php
				$pn_disp = '';
				if ($list_total <= $list_limit)
					$pn_disp = 'display:none;';
				?>
				<div id="pagination" style="<?= $pn_disp ?>">
					<?php echo form_open('spgsadmin/userlogin_disp/0'); ?>
					<input type="hidden" id="kensaku_ymd" name="kensaku_ymd" value="<?= $kensaku_ymd ?>">
					<input type="hidden" id="kensaku_usercd" name="kensaku_usercd" value="<?= $kensaku_usercd ?>">
					<input type="submit" value="最初へ" class="pure-button pn-button">
					<?= form_close(); ?>
					<?php
					$page_before = $list_offset - $list_limit;
					$disabled1 = '';
					if ($page_before < 0)
						$disabled1 = 'disabled';
					if ($page_before <= 0)
						$page_before = 0;
					echo form_open('spgsadmin/userlogin_disp/' . $page_before);
					?>
					<input type="hidden" id="kensaku_ymd" name="kensaku_ymd" value="<?= $kensaku_ymd ?>">
					<input type="hidden" id="kensaku_usercd" name="kensaku_usercd" value="<?= $kensaku_usercd ?>">
					<input type="submit" value="前へ" <?= $disabled1 ?> class="pure-button pn-button">
					<?= form_close(); ?>
					<?php
					$page_next = $list_offset + $list_limit;
					$disabled2 = '';
					if ($page_next >= $list_total)
						$disabled2 = 'disabled';
					echo form_open('spgsadmin/userlogin_disp/' . $page_next);
					?>
					<input type="hidden" id="kensaku_ymd" name="kensaku_ymd" value="<?= $kensaku_ymd ?>">
					<input type="hidden" id="kensaku_usercd" name="kensaku_usercd" value="<?= $kensaku_usercd ?>">
					<input type="submit" value="次へ" <?= $disabled2 ?> class="pure-button pn-button">
					<?= form_close(); ?>
					<?php
					$page_end = $list_total - $list_limit;
					echo form_open('spgsadmin/userlogin_disp/' . $page_end);
					?>
					<input type="hidden" id="kensaku_ymd" name="kensaku_ymd" value="<?= $kensaku_ymd ?>">
					<input type="hidden" id="kensaku_usercd" name="kensaku_usercd" value="<?= $kensaku_usercd ?>">
					<input type="submit" value="最後へ" class="pure-button pn-button">
					<?= form_close(); ?>
					TOTAL：<?= $list_total ?>件
					<?= $list_offset + 1 ?>～<?= $list_offset + $list_limit ?>
				</div>


				<table class="pure-table pure-table-striped pure-table-horizontal" id="table01">
					<colgroup>
						<col class="col01" />
						<col class="col02" />
						<col class="col03" />
						<col class="col04" />
					</colgroup>
					<thead>
						<tr>
							<th>日付</th>
							<th class="text-center">時刻</th>
							<th>コード</th>
							<th>顧客名</th>
						</tr>
					</thead>
					<tbody>
						<?php $cnt = 0; ?>
						<?php foreach ($query as $row): ?>
							<tr>
								<td>
									<?php
									if ($row['entryymd'] != 0) {
										$entryy = substr($row['entryymd'], 0, 4);
										$entrym = substr($row['entryymd'], 4, 2);
										$entryd = substr($row['entryymd'], 6, 2);
										$dspentryymd = $entryy . '/' . $entrym . '/' . $entryd;
									} else {
										$dspentryymd = '';
									}
									?>
									<?= $dspentryymd ?>
								</td>
								<td class="text-right">
									<?php
									if ($row['entrytime'] != 0) {
										if ($row['entrytime'] > 99999)
											$entryh = substr($row['entrytime'], -6, 2);
										if ($row['entrytime'] <= 99999)
											$entryh = substr($row['entrytime'], -5, 1);
										$entrymm = substr($row['entrytime'], -4, 2);
										$entrys = substr($row['entrytime'], -2, 2);
										//	$dspteltime = $telh . ':' . $telmm  . ':' . $tels;
										$dspentrytime = $entryh . ':' . $entrymm;
									} else {
										$dspentrytime = '';
									}
									?>
									<?= $dspentrytime ?>
								</td>
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
								<td><?= $row['name'] ?></td>
							</tr>
						<?php endforeach; ?>

					</tbody>
				</table>

			</div>
		</div>
		<div class="footer l-box is-center">
			SuperPGS for Web<!-- powered by ing corporation. -->
		</div>
	</div>
</div>

<?= $this->endSection() ?>