<?php 
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　Modeule           Spgsinfo　Controller
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//						   Mnt-005  三部料金修正
//2025.02.04  tanaka       Mnt-007  請求書ボタンを表示する場合、PDFの存在する場合のみ表示する。
//2025.03.06  tanaka       Mnt-008  フォントサイズが1.4remで固定だったのを修正(spgsinfo-ryokin.cssも修正)
//2025.03.13  s.matsumoto  Mnt-009  三部料金の設備使用料の説明を修正(spgsinfo.cssも修正)
//--------------------------------------------------------------------------
?>
<?= $this->extend('/layouts/base'); ?>
<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsinfo-ryokin.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>お客様照会ページ：ＬＰガス料金表<?= $this->endSection() ?>

<head>

<style>
#table14 th{font-size:16px;}
#table14 tr {border-bottom : 1px solid #ddd;}
/*
#table14 .col01{width: 120px;}
#table14 .col02{width: 20px;}
#table14 .col03{width: 70px;}
#table14 .col04{width: 200px;}
#table14 .col05{width: 200px;}
*/
#table14 .col01{width: 80px;}
#table14 .col02{width: 120px;}
#table14 .col03{width: 20px;}
#table14 .col04{width: 70px;}
#table14 .col05{width: 200px;}
#table14 .col06{width: 200px;}

@media screen and (max-width: 520px) {
	#table14 th{font-size:11px;	}
	#table14 td{font-size:11px;	}
	#table12 td{font-size:11px;	}
/*
	#table14 .col01{width: 80px;}
	#table14 .col02{width: 20px;}
	#table14 .col03{width: 80px;}
	#table14 .col04{width: 100px;}
	#table14 .col05{width: 100px;}
*/
	#table14 .col01{width: 80px;}
	#table14 .col02{width: 60px;}
	#table14 .col03{width: 8px;}
	#table14 .col04{width: 60px;}
	#table14 .col05{width: 100px;}
	#table14 .col06{width: 110px;}

}
</style>

</head>


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
            <div class="l-box pure-u-1 pure-u-lg-1-8 pure-u-xl-1-8 spacer">
		    </div>
            <div class="l-box pure-u-xl-3-4 pure-u-xl-3-4 pure-u-lg-1 pure-u-md-1 pure-u-sm-1 pure-u-1" id="spgsinfo-kensin-meisai">
				<div class="border-waku">
			<?php if ($bunrui=='1' || $bunrui=='0'): ?>

				<?php if (($ryokincnt!='0')or($ryokinno=='0')): ?>
					<h3>ＬＰガス料金の内訳</h3>
				<?php endif; ?>
					<div>
				<?php if (($ryokincnt!='0')or($ryokinno=='0')): ?>
					<table id="table01" align="center">
						<colgroup>
							<col class="col01">
							<col class="col02">
						</colgroup>
						<tbody>
							<tr>
								<td>１．</td>
<!--[Mnt-005]----------------------------------------------------------------------------------------- >> Edit Start 25/01/15 -->
								<!-- <td>料金体系 二部料金制</td> -->
								<?php if(trim($setubiryokin) != ""):?>
								<td>料金体系 三部料金制</td>
								<?php else:?>
								<td>料金体系 二部料金制</td>
								<?php endif;?>
<!--[Mnt-005]<<------------------------------------------------------------------------------------------ Edit E n d 25/01/15 -->	
						 	</tr><tr>
								<td></td>
								<?php if(trim($setubiryokin) != ""):?>
								<td>ガス料金（円／月／税込）　＝　<br class="mobile-br2" />＜基本料金　＋<br class="mobile-br2" />　［（従量料金±原料調整額）×ご使用量］<br class="mobile-br2" />　＋　設備使用料＞　×　消費税</td>
								<?php else:?>
								<td>ガス料金（円／月／税込）　＝　<br class="mobile-br2" />＜基本料金　＋<br class="mobile-br2" />　［（従量料金±原料調整額）×ご使用量］＞<br class="mobile-br2" />　×　消費税</td>
								<?php endif;?>
						 	</tr><tr>
								<td>①</td>
								<td>基本料金　＝　<br class="mobile-br" />容器、調整器、ガスメータ等の供給設備及び<br class="mobile-br2" />保安関係費に充当されます。</td>
						 	</tr><tr>
								<td>②</td>
								<td>従量料金　＝　<br class="mobile-br" />ガス原材料（ガス仕入れ価格）、容器配送費、<br class="mobile-br2" />一般販売経費（人件費、修理費等）に充当<br class="mobile-br2" />されます。</td>
						 	</tr> 
<!--[Mnt-005]----------------------------------------------------------------------------------------- >> Edit Start 25/01/15 -->
							<?php if(trim($setubiryokin) != ""):?>
							<tr>
								<td>③</td>
								<!--[Mnt-009]----------------------------------------------------------------------------------------- >> Edit Start 25/03/13 -->
								<!--<td>設備使用料　＝　<br class="mobile-br" />配管工事費やガス漏れ警報器の利用料、<br class="mobile-br2" />集中監視システムの利用料などに充当<br class="mobile-br2" />されます。</td>-->
								<td>設備使用料　＝　<br class="mobile-br" />ガス消費設備等をガス事業者から借り受けている場合の費用、<br class="pc-br" /><span class="pc-space"></span>ガス漏れ警報機や集中監視システムの利用料などに充当されます。</td>
								<!--[Mnt-009]<<------------------------------------------------------------------------------------------ Edit E n d 25/03/13 -->
						 	</tr> 
						 	<?php endif;?>
<!--[Mnt-005]<<------------------------------------------------------------------------------------------ Edit E n d 25/01/15 -->	
						</tbody>
					</table>
				<?php else: ?>
					<p style="text-align:center;">お客様にご利用いただいておりますガスの供給形態は「簡易ガス」となります。<br>
					ガス料金算出方法につきましては、ガス供給契約時に配布させていただきました<br>
					<strong><u>供給条件における重要事項</u>（４．ガス料金について）</strong>をご参照ください。
					</p>
				<?php endif; ?>

				<?php
				/*
					<p class="text-center"><!-- a href="<?=base_url()?>images/pdf/oshirase_H29_10.pdf" target="_blank" -->[ＬＰガス原料調整制度変更のお知らせ]<!-- /a --><br>&nbsp;</p>
				*/
				?>

					</div>
				<?php if (($ryokincnt!='0')or($ryokinno=='0')): ?>

					<?php
						if($ryokinno=='0'){
							if($zeikbn=='2'){
								$wkzeidsp = '従量料金（税込）';
							}else if($zeikbn=='1'){
								$wkzeidsp = '従量料金（税抜）';
							}else if($zeikbn=='3'){
								$wkzeidsp = '従量料金（非課税）';
							}else{
								$wkzeidsp = '従量料金';
							}
							echo '<table id="table03t" align="center">';
							echo '	<colgroup>';
							echo '		<col class="col01">';
							echo '		<col class="col02">';
							echo '		<col class="col03">';
							echo '	</colgroup>';
							echo '	<tbody>';
							echo '		<tr class="tr01">';
							echo '			<td class="text-left">基本料金：</td>';
							echo '			<td class="text-right"></td>';
							echo '			<td class="text-right">&yen;' . $kihon . '</td>';
							echo '		</tr>';
							echo '		<tr class="tr02">';
							echo '			<td colspan="5" class="text-left">' . $wkzeidsp . '</td>';
							echo '		</tr>';
							echo '	</tbody>';
							echo '</table>';
							echo '<table id="table04t" align="center">';
							echo '	<colgroup>';
							echo '		<col class="col01">';
							echo '		<col class="col02">';
							echo '		<col class="col03">';
							echo '		<col class="col04">';
							echo '		<col class="col05">';
							echo '	</colgroup>';
							echo '	<thead>';
							echo '		<tr>';
							echo '			<th colspan="3" class="text-left">使用量</th>';
							echo '			<th class="text-right"></th>';
							echo '			<th class="text-right">立方単価</th>';
							echo '		</tr>';
							echo '	</thead>';
							echo '	<tbody>';
							echo '		<tr>';
						/*
							echo '			<td class="text-right">'. $siyoryo . 'm&sup3;</td>';
							echo '			<td class="text-left">～</td>';
						*/
							echo '			<td class="text-right">'. $siyoryo . '</td>';

							echo '			<td class="text-right"></td>';
							echo '			<td class="text-right"></td>';
							echo '			<td class="text-left"></td>';
							echo '			<td class="text-right">' . $ryokin . '</td>';
							echo '		</tr>';
							echo '	</tbody>';
							echo '</table>';
							//[Mnt-005]----------------------------------------------------------------------------------------- >> Edit Start 25/01/15 
							$setubi_kin = trim(str_replace("円","",$setubiryokin));
							$setubi_kin = str_replace(",","",$setubi_kin);

							if(trim($setubiryokin) != ""){
								echo '<table id="table03t" align="center">';
								echo '	<colgroup>';
								echo '		<col class="col01">';
								echo '		<col class="col02">';
								echo '		<col class="col03">';
								echo '	</colgroup>';
								echo '	<tbody>';
								echo '		<tr class="tr01">';
//[Mnt-007]------------------------------------------------------------------------------------------>> Edit Start 25/03/06
/*								if($setubi_kin != "0"){
									if(is_numeric($setubi_kin)){
										if($setubiseigyo !== '2'){
											echo '			<td class="text-left" style="font-size: 1.4rem;">(設備使用料：</td>';
										}else{
											echo '			<td class="text-left" style="font-size: 1.4rem;">設備使用料：</td>';
										}
										echo '			<td class="text-right"></td>';
										if($setubiseigyo !== '2'){
											echo '			<td class="text-right" style="font-size: 1.4rem;">&yen;' . str_replace("円","",$setubiryokin) . ')</td>';
										}else{
											echo '			<td class="text-right" style="font-size: 1.4rem;">&yen;' . str_replace("円","",$setubiryokin) . '</td>';
										}
									}else{
										echo '			<td class="text-left" style="font-size: 1.4rem;">設備使用料：</td>';
										echo '			<td class="text-right"></td>';
										echo '			<td class="text-right" style="font-size: 1.4rem;">' . str_replace("円","",$setubiryokin) . '</td>';
									}
								}else{
									echo '<td class="text-left" style="font-size: 1.4rem;">設備使用料：</td>';
									echo '<td class="text-right"></td>';
									echo '<td class="text-right" style="font-size: 1.4rem;">&yen;' . str_replace("円","",$setubiryokin) . '</td>';
								}
*/
								if($setubi_kin != "0"){
									if(is_numeric($setubi_kin)){
										if($setubiseigyo !== '2'){
											echo '			<td class="text-left ryokin-font-size">(設備使用料：</td>';
										}else{
											echo '			<td class="text-left ryokin-font-size">設備使用料：</td>';
										}
										echo '			<td class="text-right"></td>';
										if($setubiseigyo !== '2'){
											echo '			<td class="text-right ryokin-font-size">&yen;' . str_replace("円","",$setubiryokin) . ')</td>';
										}else{
											echo '			<td class="text-right ryokin-font-size">&yen;' . str_replace("円","",$setubiryokin) . '</td>';
										}
									}else{
										echo '			<td class="text-left ryokin-font-size">設備使用料：</td>';
										echo '			<td class="text-right"></td>';
										echo '			<td class="text-right ryokin-font-size">' . str_replace("円","",$setubiryokin) . '</td>';
									}
								}else{
									echo '<td class="text-left ryokin-font-size">設備使用料：</td>';
									echo '<td class="text-right"></td>';
									echo '<td class="text-right ryokin-font-size">&yen;' . str_replace("円","",$setubiryokin) . '</td>';
								}
//[Mnt-007]<<------------------------------------------------------------------------------------------ Edit E n d 25/03/06
								echo '		</tr>';
								echo '	</tbody>';
								echo '</table>';
							}
							//[Mnt-005]<<------------------------------------------------------------------------------------------ Edit E n d 25/01/15 
						}
					?>
				<?php endif; ?>
					<?php
						$cnt=0;
						$wkstring	='';
						$wksiyou1	='';
						$wksiyou2	='';
						$wkryoukin	='';
					?>
				<?php if (($ryokincnt!='0')or($ryokinno=='0')): ?>
					<?php foreach($query as $row): ?>
						<?php
							if($cnt==0){
								if($row['zeikbn']=='2'){
									$wkzeidsp = '従量料金（税込）';
								}else if($row['zeikbn']=='1'){
									$wkzeidsp = '従量料金（税抜）';
								}else if($row['zeikbn']=='3'){
									$wkzeidsp = '従量料金（非課税）';
								}else{
									$wkzeidsp = '従量料金';
								}
								$wksiyou1 = $row['siyoryo'] . 'm&sup3;';
								$wkryoukin = $row['ryokin'];
								echo '<table id="table03" align="center">';
								echo '	<colgroup>';
								echo '		<col class="col01">';
								echo '		<col class="col02">';
								echo '		<col class="col03">';
								echo '	</colgroup>';
								echo '	<tbody>';
								echo '		<tr class="tr01">';
								echo '			<td class="text-left">基本料金：</td>';
								echo '			<td class="text-right"></td>';
								echo '			<td class="text-right">&yen;' . $row['kihon'] . '</td>';
								echo '		</tr>';
								echo '		<tr class="tr02">';
								echo '			<td colspan="5" class="text-left">' . $wkzeidsp . '</td>';
								echo '		</tr>';
								echo '	</tbody>';
								echo '</table>';
								echo '<table id="table04" align="center">';
								echo '	<colgroup>';
								echo '		<col class="col01">';
								echo '		<col class="col02">';
								echo '		<col class="col03">';
								echo '		<col class="col04">';
								echo '		<col class="col05">';
								echo '	</colgroup>';
								echo '	<thead>';
								echo '		<tr>';
								echo '			<th colspan="3" class="text-left">使用量</th>';
								echo '			<th class="text-right"></th>';
								echo '			<th class="text-right">立方単価</th>';
								echo '		</tr>';
								echo '	</thead>';
								echo '	<tbody>';
							}else{
								$wksiyou2 = $row['siyoryo'] . 'm&sup3';
								$wksiyouint2 = $row['siyoryo'];
								$wkstring.='<tr><td class="text-right">'. $wksiyou1 . '</td>';
								$wkstring.='<td class="text-left">～</td>';
								$wkstring.='<td class="text-right">' . $wksiyou2 . '</td>';
								$wkstring.='<td class="text-left"></td>';
								$wkstring.='<td class="text-right">' . $wkryoukin . '</td></tr>';
								$wksiyou1 = $wksiyouint2 + 0.1;
								$wksiyou1 .= 'm&sup3;';
								$wksiyou2 = '';
								$wkryoukin = $row['ryokin'];
							}
							$cnt+=1;
							if($cnt==$list_total){
								$wkstring.='<tr><td class="text-right">'. $wksiyou1 . '</td>';
								$wkstring.='<td class="text-left">～</td>';
								$wkstring.='<td colspan="2" class="text-left"></td>';
								$wkstring.='<td class="text-right">' . $wkryoukin . '</td></tr>';
								$wkstring.='</tbody></table>';
								//[Mnt-005]----------------------------------------------------------------------------------------- >> Edit Start 25/01/15 
								$setubi_kin = trim(str_replace("円","",$setubiryokin));
								$setubi_kin = str_replace(",","",$setubi_kin);
								
								if(trim($setubiryokin) != ""){
									$wkstring.='<table id="table03" align="center">';
									$wkstring.='<colgroup>';
									$wkstring.='<col class="col01">';
									$wkstring.='<col class="col02">';
									$wkstring.='<col class="col03">';
									$wkstring.='</colgroup><tbody>';
									
									$wkstring.='<tr class="tr01">';
//[Mnt-007]------------------------------------------------------------------------------------------>> Edit Start 25/03/06
/*									if($setubi_kin != "0"){
										if(is_numeric($setubi_kin)){
											if($setubiseigyo != '2'){
												$wkstring.='<td class="text-left" style="font-size: 1.4rem;">(設備使用料：</td>';
											}else{
												$wkstring.='<td class="text-left" style="font-size: 1.4rem;">設備使用料：</td>';
											}
											$wkstring.='<td class="text-right"></td>';
											if($setubiseigyo != '2'){
												$wkstring.='<td class="text-right" style="font-size: 1.4rem;">&yen;' . str_replace("円","",$setubiryokin) . ')</td>';
											}else{
												$wkstring.='<td class="text-right" style="font-size: 1.4rem;">&yen;' . str_replace("円","",$setubiryokin) . '</td>';
											}
										}else{
											$wkstring.='<td class="text-left" style="font-size: 1.4rem;">設備使用料：</td>';
											$wkstring.='<td class="text-right"></td>';
											$wkstring.='<td class="text-right" style="font-size: 1.4rem;">' . str_replace("円","",$setubiryokin) . '</td>';
										}
									}else{
										$wkstring.='<td class="text-left" style="font-size: 1.4rem;">設備使用料：</td>';
										$wkstring.='<td class="text-right"></td>';
										$wkstring.='<td class="text-right" style="font-size: 1.4rem;">&yen;' . str_replace("円","",$setubiryokin) . '</td>';
									}
									$wkstring.='</tr>';
									$wkstring.='</tbody></table>';
*/									
									if($setubi_kin != "0"){
										if(is_numeric($setubi_kin)){
											if($setubiseigyo != '2'){
												$wkstring.='<td class="text-left ryokin-font-size">(設備使用料：</td>';
											}else{
												$wkstring.='<td class="text-left ryokin-font-size">設備使用料：</td>';
											}
											$wkstring.='<td class="text-right"></td>';
											if($setubiseigyo != '2'){
												$wkstring.='<td class="text-right ryokin-font-size">&yen;' . str_replace("円","",$setubiryokin) . ')</td>';
											}else{
												$wkstring.='<td class="text-right ryokin-font-size">&yen;' . str_replace("円","",$setubiryokin) . '</td>';
											}
										}else{
											$wkstring.='<td class="text-left ryokin-font-size">設備使用料：</td>';
											$wkstring.='<td class="text-right"></td>';
											$wkstring.='<td class="text-right ryokin-font-size">' . str_replace("円","",$setubiryokin) . '</td>';
										}
									}else{
										$wkstring.='<td class="text-left ryokin-font-size">設備使用料：</td>';
										$wkstring.='<td class="text-right"></td>';
										$wkstring.='<td class="text-right ryokin-font-size">&yen;' . str_replace("円","",$setubiryokin) . '</td>';
									}
									$wkstring.='</tr>';
									$wkstring.='</tbody></table>';
//[Mnt-007]<<------------------------------------------------------------------------------------------ Edit E n d 25/03/06
								}
								//[Mnt-005]<<------------------------------------------------------------------------------------------ Edit E n d 25/01/15 
							}
						?>
					<?php endforeach; ?>

				<?php endif; ?>

					<?=$wkstring?>

			<?php elseif ($bunrui=='2'): ?>


				<p style="margin-bottom:2.0rem;"></p>
					<p style="text-align:center;">お客様にご利用いただいておりますガスの供給形態は「簡易ガス」となります。</p>
					<h3 style="text-align:center;">ガス料金表（税込）</h3>
					<table id="table14" align="center">
						<colgroup>
							<col class="col01">
							<col class="col02">
							<col class="col03">
							<col class="col04">
							<col class="col05">
							<col class="col06">
						</colgroup>
						<thead>
							<tr>
								<th colspan="4" class="text-left"></th>
								<th class="text-right">基本料金</th>
								<th class="text-right">調整単位料金</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$cnt=0;
								$wkstring	='';
								$wkksiyou1	='';
								$wkksiyou2	='';
								$wkkkihon	='';
								$wkkryoukin	='';
								$ryokinmark = range('A', 'Z');
							?>
							<?php if (($ryokincnt!='0')or($ryokinno=='0')): ?>
								<?php foreach($query as $row): ?>
								<?php
								/*
									<tr>
										<td class="text-left">料金表Ａ</td>
										<td class="text-right">0.0m<sup>3</sup></td>
										<td class="text-left">～</td>
										<td class="text-right">8.0m<sup>3</sup></td>
										<td class="text-right"><?=$row['kkihon']?></td>
										<td class="text-right"><?=$row['kryokin']?></td>
									</tr>
								*/
								?>
									<?php
										if($cnt==0){
											$wkksiyou1 = $row['siyoryo'] . 'm&sup3;';
											$wkkkihon = $row['kkihon'];
											$wkkryoukin = $row['kryokin'];
										}else{
										//	$wkksiyou2 = $row['siyoryo'] . 'm&sup3';
											$wkksiyou2 = $row['siyoryo'] - 0.1;
											$wkksiyou2 = number_format($wkksiyou2, 1) . 'm&sup3';
											$wkksiyouint2 = $row['siyoryo'];
											$wkstring.='<tr><td class="text-left">料金表'. $ryokinmark[$cnt -1] .'</td>';
											$wkstring.='<td class="text-right">'. $wkksiyou1 . '</td>';
											$wkstring.='<td class="text-left">～</td>';
											$wkstring.='<td class="text-right">' . $wkksiyou2 . '</td>';
											$wkstring.='<td class="text-right">' . $wkkkihon . '</td>';
											$wkstring.='<td class="text-right">' . $wkkryoukin . '</td></tr>';
										//	$wkksiyou1 = $wkksiyouint2 + 0.1;
											$wkksiyou1 = number_format($wkksiyouint2 + 0, 1);
											$wkksiyou1 .= 'm&sup3;';
											$wkksiyou2 = '';
											$wkkkihon = $row['kkihon'];
											$wkkryoukin = $row['kryokin'];
										}
										$cnt+=1;
										if($cnt==$list_total){
											$wkstring.='<tr><td class="text-left">料金表' . $ryokinmark[$cnt -1] . '</td>';
											$wkstring.='<td class="text-right">'. $wkksiyou1 . '</td>';
											$wkstring.='<td class="text-left">～</td>';
											$wkstring.='<td class="text-left"></td>';
											$wkstring.='<td class="text-right">' . $wkkkihon . '</td>';
											$wkstring.='<td class="text-right">' . $wkkryoukin . '</td></tr>';
										}
									?>
								<?php endforeach; ?>
							<?php endif; ?>
							<?=$wkstring?>
						</tbody>
					</table>
					<p style="text-align:center; font-size: 11pt;margin-bottom:2rem;" >※消費税込価格（小数点第３位以下切り捨て）</p>

					<h3 style="text-align:center;">ガス料金の算出方法</h3>
					<div>
						<table align="center" id="table12" >
							<caption>（円未満切捨て）</caption>
							<tr>
								<td style="border: 1px solid #888;width:90px;height:4rem; text-align:center;">基本料金</td>
								<td style="width:70px; text-align:center;">＋</td>
								<td style="border: 1px solid #888;width: 300px; text-align:center;">調整単位料金<hr>（基準単位料金 ± 基準単位料金調整額）</td>
								<td style="width:70px; text-align:center;">×</td>
								<td style="border: 1px solid #888;width:90px;height:4rem; text-align:center;">使用量</td>
							</tr>

						</table>
					</div>


			<?php endif; ?>

					<table id="table05" align="center">
						<colgroup>
							<col class="col01">
						</colgroup>

						<thead>
							<tr>
								<th class="text-center">
									＊　＊　お知らせ　＊　＊
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="text-left">
									<?=$msg1?><br><?=$msg2?><br><?=$msg3?>
									<?php 
										$extmsg = trim($msg4) . trim($msg5) . trim($msg6);
										if(mb_strlen($extmsg) > 0){
											echo '<br>' . $msg4 . '<br>' . $msg5 . '<br>' . $msg6;
										}
									?>
								</td>
						 	</tr>
						</tbody>
					</table>



				</div>
			</div>
		</div>
	</div>
	<div class="footer l-box is-center">
<!--		SuperPGS for Web powered by ing corporation. -->
	</div>
</div>

</body>
<?= $this->endSection() ?>
