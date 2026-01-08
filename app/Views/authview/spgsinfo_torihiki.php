<?php 
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　Modeule           Spgsinfo　Controller
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2025.02.04  tanaka       Mnt-007  請求書ボタンを表示する場合、PDFの存在する場合のみ表示する。
//2025.11.27  kimura       Mnt-008  CI4移行
//**************************************************************************
?>
<?= $this->extend('/layouts/base'); ?>

<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsinfo-torihiki.css?<?php echo date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>お客様照会ページ<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?= view('layouts/header_button') ?>

<body>

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
	        <div class="pure-u-1 pagination-waku">
				<?php
					$pn_disp = '';
					if($list_total <= $list_limit) $pn_disp = 'display:none;';
				?>
				<div id="pagination" style="<?=$pn_disp?>">
					<?php echo form_open('spgsinfo/torihiki_disp/0/');?>
						<input type="submit" value="最初へ" class="pure-button">
						<input type="hidden" name="usercd" value="<?=$login_usercd?>">
						<input type="hidden" name="misecd" value="<?=$login_misecd?>">
					<?=form_close();?>
					<?php
						$page_before = $list_offset - $list_limit;
						$disabled1 = '';
						if($page_before<0) $disabled1 = 'disabled';
						if($page_before<=0) $page_before = 0;
						echo form_open('spgsinfo/torihiki_disp/' . $page_before);
					?>
						<input type="submit" value="前へ" <?=$disabled1?>  class="pure-button">
						<input type="hidden" name="usercd" value="<?=$login_usercd?>">
						<input type="hidden" name="misecd" value="<?=$login_misecd?>">
					<?=form_close();?>
					<?php
						$page_next = $list_offset + $list_limit;
						$disabled2 = '';
						if($page_next>=$list_total)	$disabled2 = 'disabled';
						echo form_open('spgsinfo/torihiki_disp/' . $page_next);
					?>
						<input type="hidden" name="usercd" value="<?=$login_usercd?>">
						<input type="hidden" name="misecd" value="<?=$login_misecd?>">
						<input type="submit" value="次へ" <?=$disabled2?>  class="pure-button">
					<?=form_close();?>
					<?php
						$page_end = $list_total - $list_limit;
						echo form_open('spgsinfo/torihiki_disp/' . $page_end);
					?>
						<input type="submit" value="最後へ" class="pure-button">
						<input type="hidden" name="usercd" value="<?=$login_usercd?>">
						<input type="hidden" name="misecd" value="<?=$login_misecd?>">
					<?=form_close();?>
					TOTAL：<?=$list_total?>件
					<?=$list_offset+1?>～<?=$list_offset+$list_limit?>
				</div>
			</div>
	        <div class="pure-u-1 spgsinfo-list" id="spgsinfo-torihiki-list">
				<table class="pure-table pure-table-striped pure-table-horizontal" id="table01">
					<colgroup>
						<col class="col01" />
						<col class="col02" />
						<col class="col03" />
						<col class="col04" />
						<col class="col05" />
						<col class="col06" />
					</colgroup>
					<thead>
						<tr>
							<th class="text-left">日付</th>
							<th class="text-left">品名</th>
							<th>型式</th>
							<th class="text-right">数量</th>
							<th class="text-right">単価</th>
							<th class="text-right">金額</th>
						</tr>
					</thead>
					<tbody>
						<?php $cnt=0;?>
						<?php foreach($query as $row): ?>
						<tr>
							<td><?=$row['ymd']?></td>
							<td><?=$row['hin']?></td>
							<td><?=$row['kata']?></td>
						<!--	<td class="text-right"><?=$row['suu']?></td> -->
							<td class="text-right">
							<?php
								if(strpos($row['suu'],'-') === false)
								{
									echo $row['suu'];
								}else{
									echo '';
								}	
								?>
							</td>
							<td class="text-right">
								<?php
									if($row['tanka']=='0.00'){
										echo '';
									}else{
										echo $row['tanka'];
									}
								?>
							</td>
							<td class="text-right"><?=$row['kin']?></td>
					 	</tr> 
						<?php endforeach; ?>
					</tbody>
				</table>
		    </div>
	    </div>
	</div>
	<div class="footer l-box is-center">
<!--		SuperPGS for Web powered by ing corporation. -->
	</div>
</div>

</body>
<?= $this->endSection() ?>
