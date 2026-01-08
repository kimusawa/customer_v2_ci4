<?php 
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　VIEW：メニュー画面
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2025.02.04  tanaka       Mnt-007  請求書ボタンを表示する場合、PDFの存在する場合のみ表示する。
//2025.09.08  s.matsumoto  Mnt-008  メニューの下にチラシや広告を表示する。
//2025.11.21  kimura       Mnt-000  CI4移行
//--------------------------------------------------------------------------
?>
<?= $this->extend('/layouts/base'); ?>

<?= $this->section('css') ?>
<!-- ページ固有のcssはここに記載 -->
<link rel="stylesheet" href="<?=base_url()?>css/layouts/spgsinfo-menu.css?<?= date("YmdHis"); ?>">
<?= $this->endSection(); ?>

<?= $this->section('title') ?>お客様照会ページ<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="header">
    <div class="home-menu pure-menu pure-menu-open pure-menu-horizontal pure-menu-fixed">
        <a class="pure-menu-heading" href="<?=$header_url01?>"><img src="<?=base_url()?>images/spgs/<?=$header_img01?>"></a>

        <ul>
            <li><a href="<?=$header_url02?>"><img src="<?=base_url()?>images/spgs/<?=$header_img02?>"></a></li>

            <li>
				<?php echo form_open('spgsinfo/logout/', 'style="display:inline-block!important;"'); ?>
				<input type="submit" class="pure-button header-button" value="ログアウト" >
				<?=form_hidden('errmsg', 'ログアウトしました。');?>
				<?=form_close();?>
			</li>
        </ul>
    </div>
</div>

<div class="content-wrapper">
    <div class="content">
        <div class="pure-g" id="spgsinfo-kyotsu">
            <div class="l-box pure-u-1">
				ユーザーコード&nbsp;<br class="sp">
				<span class="usercode"><?=sprintf("%04d", $login_misecd);?>&nbsp;<?=$login_dspusercd?>&nbsp;</span><span class="kokyakumei"><br><?=$login_name?>&nbsp;様</span>
		    </div>
		</div>
		<div class="menu-space"></div>

<!-- [Mnt-008]------------------------------------------------------------------------------------------ Edit Start 25/09/08-->
<?php
	// oshirase_flg取得
	if($oshirase_flg == 1){
		echo '<div class="oshirase">';
		echo '<div class="border-waku syuti" style="text-align:center;">';
		echo '<h3>お　知　ら　せ</h3>';
		echo '<p style="letter-spacing:.1rem;">';
		// ファイル更新日取得：周知文書のファイル名や場所が変わった場合は修正する事
		// ファイルの指定先URLは大塚商会とsmilepaymentで違うので！！注意！！
		// 大塚照会
		//	$pdfdatetime = filemtime("/var/www/html/CodeIgniter/images/spgs/oshirase20241001.pdf");
		// smilepayment（最初の名前の部分は毎回変わります）
		$pdfdatetime = filemtime(base_url() ."images/spgs/lpgas-notice.pdf");
		$pdfYmd = date( "Y-m-d", $pdfdatetime);
		// 今日より30日前を基準日として算出する
		$hikakuYmd = date("Y-m-d", strtotime("-30 day")); 
		if($hikakuYmd < $pdfYmd){
			echo('<span style="color:red;font-weight:bold;">NEW</span>');
		}
		// echo $hikakuYmd . 'が基準日';
		echo $pdfYmd . '更新';
		echo '</p>';
		echo '<a href="'.base_url().'images/spgs/lpgas-notice.pdf" target="_blank"><img style="margin: 0 9px;" src="'.base_url().'images/spgs/lpgas-notice.jpg"></a>';
		echo '<a href="'.base_url().'images/spgs/lpgas-notice-en.pdf" target="_blank"><img style="margin: 0 9px;" src="'.base_url().'images/spgs/lpgas-notice-en.jpg"></a>';
		echo '<a href="'.base_url().'images/spgs/lpgas-notice-ch.pdf" target="_blank"><img style="margin: 0 9px;" src="'.base_url().'images/spgs/lpgas-notice-ch.jpg"></a>';
		echo '<a href="'.base_url().'images/spgs/lpgas-notice-business.pdf" target="_blank"><img style="margin: 0 9px;" src="'.base_url().'images/spgs/lpgas-notice-business.jpg"></a>';
		echo '<p style="letter-spacing: .1rem;">※画像をクリックするとPDFファイルが開きます</p>';
		echo '</div>';
		echo '</div>';
	}
?>

        <div class="pure-g" id="spgsinfo-topbutton">

<?php

	function func_btncolor($opt){
		if($opt == 1){
			return " kari1";
		}else{
			return "";
		}
	};

	$wki = 1;
	foreach ($buttons as $btn):
		$cfg_button = $btn;
		//var_dump($cfg_button);
		if($cfg_button["url"] == 'spgsinfo/seikyu_disp/' && $bill_flg != 1) continue;
		if($cfg_button["url"] == 'spgsinfo/seikyu_disp/' && count($files) == 0) continue;
		if(($wki % 2 ) == 1){
			//奇数
			if($btn === end($buttons)){
				// 最後のボタンが奇数の場合、ボタンを2倍長くする
				echo '<div class="l-box pure-u-1-4 spacer">';
				echo '</div>';
				echo '<div class="l-box pure-u-xl-1-2 pure-u-lg-1-2 pure-u-md-1-2 pure-u-sm-1-2 pure-u-1">';
				echo form_open($cfg_button["url"]);
				echo '<button type="submit" class="pure-button topbutton'.func_btncolor($cfg_button["option"]).'">'.$cfg_button["name"].'</button>';
				echo form_close();
				echo '</div>';
				echo '<div class="l-box pure-u-1-4 spacer">';
				echo '</div>';
			}else{
				echo '<div class="l-box pure-u-1-4 spacer">';
				echo '</div>';
				echo '<div class="l-box pure-u-xl-1-4 pure-u-lg-1-2 pure-u-md-1-2 pure-u-sm-1-2 pure-u-1">';
				echo form_open($cfg_button["url"]);
				echo '<button type="submit" class="pure-button topbutton'.func_btncolor($cfg_button["option"]).'">'.$cfg_button["name"].'</button>';
				echo form_close();
				echo '</div>';
			}
		}else{
			//偶数
			echo '<div class="l-box pure-u-xl-1-4 pure-u-lg-1-2 pure-u-md-1-2 pure-u-sm-1-2 pure-u-1">';
			echo form_open($cfg_button["url"]);
			echo '<button type="submit" class="pure-button topbutton'.func_btncolor($cfg_button["option"]).'">'.$cfg_button["name"].'</button>';
			echo form_close();
			echo '</div>';
			echo '<div class="l-box pure-u-1-4 spacer">';
			echo ' </div>';
		}
		$wki = $wki + 1;
	endforeach;
?>

<?php
	// oshirase_flg取得
	if($oshirase_flg == 2){
		echo '<div class="oshirase">';
		echo '<div class="border-waku syuti" style="text-align:center;">';
		echo '<h3>お　知　ら　せ</h3>';
		echo '<p style="letter-spacing:.1rem;">';
		// ファイル更新日取得：周知文書のファイル名や場所が変わった場合は修正する事
		// ファイルの指定先URLは大塚商会とsmilepaymentで違うので！！注意！！
		// 大塚照会
		//	$pdfdatetime = filemtime("/var/www/html/CodeIgniter/images/spgs/oshirase20241001.pdf");
		// smilepayment（最初の名前の部分は毎回変わります）
		$pdfdatetime = filemtime(FCPATH ."images/spgs/lpgas-notice.pdf");
		$pdfYmd = date( "Y-m-d", $pdfdatetime);
		// 今日より30日前を基準日として算出する
		$hikakuYmd = date("Y-m-d", strtotime("-30 day")); 
		if($hikakuYmd < $pdfYmd){
			echo('<span style="color:red;font-weight:bold;">NEW</span>');
		}
		// echo $hikakuYmd . 'が基準日';
		echo $pdfYmd . '更新';
		echo '</p>';
		echo '<a href="'.base_url().'images/spgs/lpgas-notice.pdf" target="_blank"><img style="margin: 0 9px;" src="'.base_url().'images/spgs/lpgas-notice.jpg"></a>';
		echo '<a href="'.base_url().'images/spgs/lpgas-notice-en.pdf" target="_blank"><img style="margin: 0 9px;" src="'.base_url().'images/spgs/lpgas-notice-en.jpg"></a>';
		echo '<a href="'.base_url().'images/spgs/lpgas-notice-ch.pdf" target="_blank"><img style="margin: 0 9px;" src="'.base_url().'images/spgs/lpgas-notice-ch.jpg"></a>';
		echo '<a href="'.base_url().'images/spgs/lpgas-notice-business.pdf" target="_blank"><img style="margin: 0 9px;" src="'.base_url().'images/spgs/lpgas-notice-business.jpg"></a>';
		echo '<p style="letter-spacing: .1rem;">※画像をクリックするとPDFファイルが開きます</p>';
		echo '</div>';
		echo '</div>';
	}
?>

	    </div>
	</div>

		<div class="space60"><!-- これはスペース用です --></div>

	<div class="footer l-box is-center">
<!--		SuperPGS for Web powered by ing corporation. -->
	</div>
</div>

<?= $this->endSection() ?>
