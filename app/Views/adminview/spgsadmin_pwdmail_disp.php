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
<?= log_message('debug', '★AdminSpgsController adminview/spgsadmin_pwdmail_disp 実行'); ?>
<?= $this->extend('/layouts/admin_base'); ?>
<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?= base_url() ?>css/layouts/spgsadmin-pwdmail-disp.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>管理者用ページ：パスワード・メールダウンロード<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?= view('layouts/admin_header_button') ?>

<div class="content-wrapper" id="wrap">
	<div class="content">

		<?= view('layouts/admin_header_menu') ?>

		<div class="pure-g" id="spgsadmin-kyotsu">
			<div class="pure-g" id="spgsadmin-kyotsu">
				<div class="l-box text-left pure-u-1">
					ログインユーザー<span>　<?= $login_id ?>様</span>
				</div>
			</div>
		</div>
		<div class="pure-g" id="spgsadmin-login-form">
			<div class="pure-u-lg-1-6 pure-u-1"></div>
			<div class="pure-u-lg-2-3  pure-u-1">
				<?php echo form_open('spgsadmin/pwdmail_disp', ['method' => 'get']); ?>
				<table id="table01" align="center">
					<colgroup>
						<col class="col01" />
					</colgroup>
					<tbody>
						<tr>
							<td class="text-center">パスワード及びメールの情報を表示します。</td>
						</tr>
						<tr>
							<td><input type="submit" class="pure-button pwdmail-button" value="データ表示"></td>
						</tr>
					</tbody>
				</table>
				<?= form_close(); ?>
				<?php echo form_open('spgsadmin/pwdmail_download'); ?>
				<table id="table02" align="center">
					<colgroup>
						<col class="col01" />
						<col class="col02" />
						<col class="col03" />
						<col class="col04" />
						<col class="col05" />
						<col class="col06" />
						<col class="col07" />
					</colgroup>
					<tbody>
						<?php
						if (((int) $pwdcount != 0) or ((int) $mailcount != 0)) {
							$nodisp = '';
							$nodisp2 = '';
						} else {
							$nodisp = 'class="no-disp"';
							$nodisp2 = 'no-disp';
						}
						?>
						<tr <?= $nodisp ?>>
							<td colspan="7" class="text-center">パスワード及びメールのファイルを作成・ダウンロードします。</td>
						</tr>
						<tr <?= $nodisp ?>>
							<td colspan="3">変更データ<br /></td>
							<td colspan="2">ﾊﾟｽﾜｰﾄﾞ変更：<?= $pwdcount ?>件<br /></td>
							<td colspan="2">メール変更：<?= $mailcount ?>件<br /></td>
						</tr>
						<?php
						if ($pwdlist == '') {
							echo '</tbody>';
							echo '</table>';
							echo '</div>';
							echo '<div class="pure-u-1-4">';
							echo '</div>';
							echo '</div>';
							echo '</div>';
							echo '<div class="footer l-box is-center">';
							echo 'SuperPGS for Web';
							echo '</div>';
							echo '</div>';
							echo '</body>';
							echo '</html>';
						}
						?>
						<?php
						if ((int) $pwdcount != 0) {
							echo ('<tr class="border-download list-title">');
							echo ('<td>ﾊﾟｽﾜｰﾄﾞ変更</td>');
							echo ('<td>店ｺｰﾄﾞ</td>');
							echo ('<td>ﾕｰｻﾞｰｺｰﾄﾞ</td>');
							echo ('<td>OLD</td>');
							echo ('<td>NEW</td>');
							echo ('<td>登録日</td>');
							echo ('<td class="text-right">時刻</td>');
							echo ('</tr>');
						}
						?>

						<?php
						if ($pwdlist != '') {

							foreach ($pwdlist as $row) {
								if ((int) $row['entryymd'] != 0) {
									$entryy = substr($row['entryymd'], 0, 4);
									$entrym = substr($row['entryymd'], 4, 2);
									$entryd = substr($row['entryymd'], 6, 2);
									$dspentryymd = $entryy . '/' . $entrym . '/' . $entryd;
								} else {
									$dspentryymd = '  ';
								}
								if ((int) $row['entrytime'] != 0) {
									if ((int) $row['entrytime'] > 99999)
										$entryh = substr((int) $row['entrytime'], -6, 2);
									if ((int) $row['entrytime'] <= 99999)
										$entryh = substr((int) $row['entrytime'], -5, 1);
									$entrymm = substr((int) $row['entrytime'], -4, 2);
									$entrys = substr((int) $row['entrytime'], -2, 2);
									$dspentrytime = $entryh . ':' . $entrymm;
								} else {
									$dspentrytime = '  ';
								}
								echo ('<tr class="border-download">');
								echo ('	<td>' . $row['recno'] . '</td>');
								echo ('	<td>' . sprintf("%04d", $row['misecd']) . '</td>');
								echo ('	<td>' . $row['usercd'] . '</td>');
								echo ('	<td>' . $row['oldpwd'] . '</td>');
								echo ('	<td>' . $row['newpwd'] . '</td>');
								echo ('	<td>' . $dspentryymd . '</td>');
								echo ('	<td class="text-right">' . $dspentrytime . '</td>');
								echo ('</tr>');

							}
						}
						?>
						<?php
						if ((int) $mailcount != 0) {
							echo ('<tr class="border-download list-title">');
							echo ('<td>ﾒｰﾙｱﾄﾞﾚｽ変更</td>');
							echo ('<td>店ｺｰﾄﾞ</td>');
							echo ('<td>ﾕｰｻﾞｰｺｰﾄﾞ</td>');
							echo ('<td>OLD</td>');
							echo ('<td>NEW</td>');
							echo ('<td>登録日</td>');
							echo ('<td class="text-right">時刻</td>');
							echo ('</tr>');
						}
						?>

						<?php
						if ($maillist != '') {

							foreach ($maillist as $row) {

								if ((int) $row['entryymd'] != 0) {
									$entryy = substr($row['entryymd'], 0, 4);
									$entrym = substr($row['entryymd'], 4, 2);
									$entryd = substr($row['entryymd'], 6, 2);
									$dspentryymd = $entryy . '/' . $entrym . '/' . $entryd;
								} else {
									$dspentryymd = '';
								}
								if ((int) $row['entrytime'] != 0) {
									if ((int) $row['entrytime'] > 99999)
										$entryh = substr((int) $row['entrytime'], -6, 2);
									if ((int) $row['entrytime'] <= 99999)
										$entryh = substr((int) $row['entrytime'], -5, 1);
									$entrymm = substr((int) $row['entrytime'], -4, 2);
									$entrys = substr((int) $row['entrytime'], -2, 2);
									$dspentrytime = $entryh . ':' . $entrymm;
								} else {
									$dspentrytime = '';
								}
								echo ('<tr class="border-download">');

								echo ('<td>' . $row['recno'] . '</td>');
								echo ('	<td>' . sprintf("%04d", $row['misecd']) . '</td>');
								echo ('	<td>' . $row['usercd'] . '</td>');
								echo ('	<td>' . $row['oldmail'] . '</td>');
								echo ('	<td>' . $row['newmail'] . '</td>');
								echo ('	<td>' . $dspentryymd . '</td>');
								echo ('	<td class="text-right">' . $dspentrytime . '</td>');
								echo ('</tr>');


							}

						}
						?>

						<tr>
							<td colspan="7">
								<input type="submit" class="pure-button pwdmail-button <?= $nodisp2 ?>" value="ダウンロード">
								<?php
								if ($nodisp2 != '')
									echo '<p style="text-align:center;color:red;">ダウンロードするデータが存在しません。</p>';
								?>
							</td>
						</tr>
					</tbody>
				</table>
				<?= form_close(); ?>
				<br>
			</div>
			<div class="pure-u-lg-1-6 pure-u-1"></div>
		</div>
	</div>
	<div class="footer l-box is-center">
		SuperPGS for Web<!-- powered by ing corporation. -->
	</div>
</div>

<?= $this->endSection() ?>