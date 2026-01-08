<div class="pure-g" id="spgsinfo-topmenu">
	<!-- 該当ページのボタンはthispageクラスを入れる -->
	<?php
	$uri = service('uri');
	$current_page = ($uri->getTotalSegments() >= 2) ? $uri->getSegment(2) : '';

	function get_active_class($url, $current_page)
	{
		$parts = explode('/', trim($url, '/'));
		$segment = isset($parts[1]) ? $parts[1] : '';
		return ($segment === $current_page) ? 'thispage' : '';
	}

	function func_btncolor($opt)
	{
		if ($opt == 1) {
			return " kari1";
		} else {
			return "";
		}
	}

	$del = 0;
	//総合ボタン数を取得
	// ログアウトを抜くため　マイナス1にする
	for ($cnt = 0; $cnt < $max_button - 1; $cnt++) {
		$cfg_button = $buttons[$cnt];
		if ($cfg_button["url"] == 'spgsinfo/seikyu_disp/' && $bill_flg != 1) {
			$del = 1;
		}
		//[Mnt-007]------------------------------------------------------------------------------------------>> Edit Start 25/02/04
		else if ($cfg_button["url"] == 'spgsinfo/seikyu_disp/' && count($files) == 0) {
			$del = 1;
		}
		//[Mnt-007]<<------------------------------------------------------------------------------------------ Edit E n d 25/02/04
	}
	// ログアウトを抜くため　マイナス1にする
	$btn_suu = $max_button - $del - 1;

	// ボタン作成
	// ログアウトを抜くため　マイナス1にする
	for ($cnt = 0; $cnt < $max_button - 1; $cnt++) {
		$cfg_button = $buttons[$cnt];
		if ($cfg_button["url"] == 'spgsinfo/seikyu_disp/' && $bill_flg != 1)
			continue;
		//[Mnt-007]------------------------------------------------------------------------------------------>> Edit Start 25/02/04
		if ($cfg_button["url"] == 'spgsinfo/seikyu_disp/' && count($files) == 0)
			continue;
		//[Mnt-007]<<------------------------------------------------------------------------------------------ Edit E n d 25/02/04
		if ($btn_suu <= 6) {
			echo '<div class="l-box pure-u-1-' . $btn_suu . '">';
			echo form_open($cfg_button["url"]);
			echo '<input type="submit" class="pure-button topbutton ' . get_active_class($cfg_button["url"], $current_page) . func_btncolor($cfg_button["option"]) . '" value="' . $cfg_button["name"] . '" >';
			echo form_close();
			echo '</div>';
		} else {
			if ($cnt == ($max_button - 2 - 1)) {
				//細いボタン2行にする
				echo '<div class="l-box pure-u-1-6">';
				echo form_open($cfg_button["url"]);
				echo '<input type="submit" class="pure-button topbutton half-button ' . get_active_class($cfg_button["url"], $current_page) . func_btncolor($cfg_button["option"]) . '" value="' . $cfg_button["name"] . '" >';
				echo form_close();
			} elseif ($cnt == ($max_button - 1 - 1)) {
				echo form_open($cfg_button["url"]);
				echo '<input type="submit" class="pure-button topbutton half-button ' . get_active_class($cfg_button["url"], $current_page) . func_btncolor($cfg_button["option"]) . '" value="' . $cfg_button["name"] . '" >';
				echo form_close();
				echo '</div>';
			} else {
				//通常のボタン５個まで
				echo '<div class="l-box pure-u-1-6">';
				echo form_open($cfg_button["url"]);
				echo '<input type="submit" class="pure-button topbutton ' . get_active_class($cfg_button["url"], $current_page) . func_btncolor($cfg_button["option"]) . '" value="' . $cfg_button["name"] . '" >';
				echo form_close();
				echo '</div>';
			}
		}
	}
	?>

</div>
<div class="pure-g" id="spgsinfo-topmenu-mobile">
	<!-- 該当ページのボタンはthispageクラスを入れる -->
	<?php
	// ボタン作成(スマホ用)
	for ($cnt = 0; $cnt < $max_button + 1 - 1; $cnt++) {
		$cfg_button = $buttons[$cnt];
		if ($cfg_button["url"] == 'spgsinfo/seikyu_disp/' && $bill_flg != 1)
			continue;
		//[Mnt-004]----------------pure-------------------------------------------------------------------------->> Edit Start 25/02/04
		if ($cfg_button["url"] == 'spgsinfo/seikyu_disp/' && count($files) == 0)
			continue;
		//[Mnt-004]<<------------------------------------------------------------------------------------------ Edit E n d 25/02/04
		echo form_open($cfg_button["url"]);
		echo '<button type="submit" class="mobile-menu ' . get_active_class($cfg_button["url"], $current_page) . func_btncolor($cfg_button["option"]) . '">' . mb_convert_kana($cfg_button["name"], "k") . '</button>';
		echo form_close();
	}
	?>
		</div>
