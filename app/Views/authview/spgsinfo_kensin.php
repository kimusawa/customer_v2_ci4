<?php 
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　Modeule           Spgsinfo　Controller
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//						   Mnt-005  3部制料金対応
//2025.02.04  tanaka       Mnt-007  請求書ボタンを表示する場合、PDFの存在する場合のみ表示する。
//2025.11.25  kimura       Mnt-000  CI4移行
//--------------------------------------------------------------------------
?>
<?= $this->extend('/layouts/base'); ?>
<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsinfo-kensin.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>お客様照会ページ：当月検針情報v2<?= $this->endSection() ?>

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
	        <div class="pure-hidden-phone pure-u-1 pure-u-md-1-8">
		    </div>
	        <div class="l-box is-center pure-u-1 pure-u-md-7-8" id="spgsinfo-kensin-kensin">
				<table id="table01">
					<colgroup>
						<col class="col01" />
						<col class="col02" />
						<col class="col03" />
						<col class="col04" />
					</colgroup>
					<?php
						$wkkoukan = trim($spgskensin_result['meterkoukanymd']);
						$koukandisp = '';
						if(empty($wkkoukan)){
							$koukandisp = 'style="display : none;"';
						}else{
							$wkkoukanymd = explode("/", $spgskensin_result['meterkoukanymd']);
							$wkmm = $wkkoukanymd[1];
							$wkdd = $wkkoukanymd[2];
						}
					?>
					<tbody>
						<tr class="pc-siyoryo">
							<td class="text-left">今回検針日</td>
							<td class="text-right">
<?php /*								<?=$konkensinymd?> */ ?>
								<?php
									if(trim($spgskensin_result['konkensinymd']) != ''){
										$array_konkensinymd = explode("/", $spgskensin_result['konkensinymd']);
										$wk_konkensinymd = $array_konkensinymd[0] . '年' . $array_konkensinymd[1] . '月' . $array_konkensinymd[2] . '日';
										echo $wk_konkensinymd;
									}
								?>							
							</td>
							<td class="text-left">今回指針</td>
							<td class="text-right"><?=$spgskensin_result['konkensinsisin']?></td>
						</tr><tr class="pc-siyoryo">
							<td class="text-left">前回検針日</td>
							<td class="text-right">
<?php /*								<?=$zenkensinymd?> */ ?>
								<?php
									if(trim($spgskensin_result['zenkensinymd']) != ''){
										$array_zenkensinymd = explode("/", $spgskensin_result['zenkensinymd']);
										$wk_zenkensinymd = $array_zenkensinymd[0] . '年' . $array_zenkensinymd[1] . '月' . $array_zenkensinymd[2] . '日';
										echo $wk_zenkensinymd;
									}
								?>		
							</td>
							<td class="text-left">
								<?php
									if($koukandisp == ''){
										echo '取付指針';
									}else{
										echo '前回指針';
									}
								?>
							</td>
							<td class="text-right">
								<?php
									if($koukandisp == ''){
										echo $spgskensin_result['meterkoukantorituke'];
									}else{
										echo $spgskensin_result['zenkensinsisin'];
									}
								?>
							</td>
						</tr><tr class="pc-siyoryo">
							<td class="text-left zen">前月使用量</td>
							<td class="text-right zen"><?=$spgskensin_result['zensiyoryo']?></td>
							<td class="text-left">今月使用量</td>
							<td class="text-right"><?=$spgskensin_result['konkensinsiyoryo']?></td>
						</tr><tr class="mobile-siyoryo">
							<td class="text-left">今回検針日</td>
							<td class="text-right">
<?php /*								<?=$konkensinymd?> */ ?>
								<?php
									if(trim($spgskensin_result['konkensinymd']) != ''){
										$array_konkensinymd = explode("/", $spgskensin_result['konkensinymd']);
										$wk_konkensinymd = $array_konkensinymd[0] . '年' . $array_konkensinymd[1] . '月' . $array_konkensinymd[2] . '日';
										echo $wk_konkensinymd;
									}
								?>	
							</td>
							<td class="text-left">前回検針日</td>
							<td class="text-right">
<?php /*								<?=$zenkensinymd?> */ ?>
								<?php
									if(trim($spgskensin_result['zenkensinymd']) != ''){
										$array_zenkensinymd = explode("/", $spgskensin_result['zenkensinymd']);
										$wk_zenkensinymd = $array_zenkensinymd[0] . '年' . $array_zenkensinymd[1] . '月' . $array_zenkensinymd[2] . '日';
										echo $wk_zenkensinymd;
									}
								?>	
							</td>
						</tr><tr class="mobile-siyoryo">
							<td class="text-left">今回指針</td>
							<td class="text-right"><?=$spgskensin_result['konkensinsisin']?></td>
							<td class="text-left">
								<?php
									if($koukandisp == ''){
										echo '取付指針';
									}else{
										echo '前回指針';
									}
								?>
							</td>
							<td class="text-right">
								<?php
									if($koukandisp == ''){
										echo($spgskensin_result['meterkoukantorituke']);
									}else{
										echo($spgskensin_result['zenkensinsisin']);
									}
								?>
							</td>
						</tr><tr class="mobile-siyoryo">
							<td class="text-left">今月使用量</td>
							<td class="text-right"><?=$spgskensin_result['konkensinsiyoryo']?></td>
							<td class="text-left zen">前月使用量</td>
							<td class="text-right zen"><?=$spgskensin_result['zensiyoryo']?></td>
						</tr>
					</tbody>
				</table>
					<?php if(!empty($wkmm) && !empty($wkdd)): ?>
						<span <?=$koukandisp?>><br>※<?=$wkmm?>月<?=$wkdd?>日にメーター器を交換しました</span>
					<?php endif; ?> 
				<table id="table06" <?=$koukandisp?>>
					<colgroup>
						<col class="col01">
						<col class="col02">
						<col class="col03">
						<col class="col04">
					</colgroup>
					<tbody>
						<tr>
							<td class="text-left">前回指針</td>
							<td class="text-right"><?=$spgskensin_result['meterkoukanzenkai']?></td>
							<td class="text-left">引取指針</td>
							<td class="text-right"><?=$spgskensin_result['meterkoukanhikitori']?></td>
						</tr>
						<tr>
							<td class="text-left">旧使用量</td>
							<td class="text-right"><?=$spgskensin_result['meterkoukankyusiyo']?></td>
							<td class="text-left"></td>
							<td class="text-center"></td>
						</tr>
					</tbody>
				</table>
		    </div>
	    </div>

		<div class="pure-g">
			<div class="l-box pure-u-1" id="spgsinfo-kensin-kei">
				<table id="table04">
					<colgroup>
						<col class="col01">
						<col class="col02">
					</colgroup>
					<tbody>
						<tr>
							<td class="text-center red">ご請求額　①＋②</td>
							<td class="text-center red"><?=$spgskensin_result['seikyukin']?></td>
						</tr>
					</tbody>
				</table><br>
				<table id="table05" align="right">
					<colgroup>
						<col class="col01">
						<col class="col02">
						<col class="col03">
						<col class="col04">
					</colgroup>
					<tbody>
						<tr>
							<td>お支払方法</td>
							<td class="text-center"><?=$spgskensin_result['siharai']?></td>
							<td>
							<?php	
								if((trim($spgskensin_result['siharai'])=='口座振替')or(trim($spgskensin_result['siharai'])=='自振')) echo '次回振替予定日';
							?>
							</td>
							<td class="text-center"><?=$spgskensin_result['furiymd']?></td>
						</tr>
						<tr>
							<td class="text-center" colspan="4">
							<?php	
								if((trim($spgskensin_result['siharai'])=='口座振替')or(trim($spgskensin_result['siharai'])=='自振')) echo '※振替日が金融機関の休日にあたる場合は<br class="mobile-br" />翌営業日の引落しとなります。';
							?>
							</td>
						</tr>
					</tbody>
				</table>

			</div>
		</div>

<!-- 決済ボタン -->
<?php if($dgf_flg == 1):?>
	<?php if($spgskensin_result['seikyukin'] != '0'):?>
		<?php echo form_open('spgsinfo/dgfpayment/'); ?>
			<input type="submit" class="seikyubotton kari1" value="決済" >
		<?=form_close();?>
	<?php else:?>
		<?php echo form_open('spgsinfo/getReceipt/'); ?>
			<input type="submit" class="seikyubotton kari1" value="領収書" >
		<?=form_close();?>
	<?php endif;?>
<?php endif;?>
<!-- 決済ボタン -->

        <div class="pure-g">
            <div class="l-box pure-u-1 pure-u-sm-1 pure-u-md-1 pure-u-lg-1-3 pure-u-xl-1-3" id="spgsinfo-kensin-ryokin">
				<div class="border-waku">
					<h3>当月ガス料金</h3>
					<table id="table02">
						<colgroup>
							<col class="col01">
							<col class="col02">
						</colgroup>
						<tbody>
							<tr>
								<td>基本料金</td>
								<td class="text-right"><?=$spgskensin_result['kihonryokin']?></td>
							</tr>
							<tr>
								<td>従量料金</td>
								<td class="text-right"><?=$spgskensin_result['jyuryoryokin']?></td>
							</tr>
<!--[Mnt-005]----------------------------------------------------------------------------------------- >> Edit Start 25/01/15 -->
							<?php
								$setubi_kin = trim(str_replace("円","",$spgskensin_result['setubiryokin']));
								$setubi_kin = str_replace(",","",$setubi_kin);
							?>
							<?php if (trim($spgskensin_result['setubiryokin']) !== ''): ?>
							<tr>
								<td><?php if(trim($spgskensin_result['setubiseigyo']) !== "2" and $setubi_kin != "0" and is_numeric($setubi_kin) == true){echo "(";}?>設備使用料</td>
								<td class="text-right"><?=$spgskensin_result['setubiryokin']?><?php if(trim($spgskensin_result['setubiseigyo']) !== "2" and $setubi_kin != "0" and is_numeric($setubi_kin) == true ){echo ")";}?></td>
							</tr>
							<?php endif; ?>
<!--[Mnt-005]<<------------------------------------------------------------------------------------------ Edit E n d 25/01/15 -->				
				<?php /* 2023/06/26 add start */ ?>
							<?php if (trim($spgskensin_result['gasnebiki']) !== ''): ?>
							<tr>
								<td>ガス値引</td>
								<td class="text-right"><?=$spgskensin_result['gasnebiki']?></td>
							</tr>
							<?php endif; ?>
				<?php /* 2023/06/26 add end */ ?>
							<tr>
								<td>消費税</td>
								<td class="text-right"><?=$spgskensin_result['shouhizei']?></td>
							</tr>
							<tr class="sashizan">
								<td class="red">①ガス料金</td>
								<td class="text-right"><?=$spgskensin_result['gasryokin']?></td>
							</tr>
						</tbody>
					</table>
			    </div>
		    </div>
<!--
        </div>
        <div class="pure-g">
-->
            <div class="l-box pure-u-1 pure-u-sm-1 pure-u-md-1 pure-u-lg-2-3 pure-u-xl-2-3" id="spgsinfo-kensin-meisai">
				<div class="border-waku spgsinfo-list">
					<h3>繰越金と当月の御買上明細</h3>
					<table id="table03">
						<colgroup>
							<col class="col01">
							<col class="col02">
							<col class="col03">
							<col class="col04">
							<col class="col05">
						</colgroup>
						<thead>
							<tr>
								<th class="text-left">日付</th>
								<th class="text-left">品名</th>
								<th class="text-right">数量</th>
								<th class="text-right">単価</th>
								<th class="text-right">金額</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								// ループの範囲を定数化
								$max_tori_count = 6;
								for ($i = 1; $i <= 6; $i++): 
							?>
								<tr>
									<td><?=$spgskensin_result['toriymd' . $i]?></td>
									<td><?=$spgskensin_result['toriname' . $i]?>&nbsp;</td>
									<td class="text-right">
										<?php
										$torisuu = $spgskensin_result['torisuu' . $i];
											if(strpos($torisuu,'-') === false)
											{
												echo $torisuu;
											}else{
												echo '';
											}	
										?>
									</td>
									<td class="text-right">
										<?php
											$tanka = $spgskensin_result['tanka' . $i];
											if($tanka !='0.00') echo $tanka;
										?>
									</td>
									<td class="text-right"><?=$spgskensin_result['torikin1']?></td>
								</tr>
							<?php endfor; ?>

							<tr class="kei">
								<td class="text-right" colspan="5"><span class="red">②明細の合計</span>　<?=$spgskensin_result['torigokeikin']?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="space"></div>
	</div>
	<div class="footer l-box is-center">
<!--		SuperPGS for Web powered by ing corporation. -->
	</div>
</div>

<?= $this->endSection() ?>
