<?php
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　Modeule           Spgsinfo　Controller
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2025.12.05  kimura       Mnt-000  CI4対応
//**************************************************************************

// defined('BASEPATH') OR exit('No direct script access allowed');
namespace App\Libraries;

class Upload_seikyu
{

	// protected $CI;
	protected $spgskensinModel;

	public function __construct()
	{
		// $this->CI =& get_instance();
		$this->spgskensinModel = model('SpgskensinModel');
	}

	function unzip($zip_path, $unzip_dir, $file_mod = 0755)
	{
		log_message('info', 'UNZIP 開始');
		$zip = new \ZipArchive();
		$ret = $zip->open($zip_path);
		if ($ret !== TRUE) {
			log_message('warning', 'UNZIP ファイル存在エラー:' . $ret);
			return FALSE;
		}


		$unzip_dir = (substr($unzip_dir, -1) == '/') ? $unzip_dir : $unzip_dir . '/';
		for ($i = 0; $i < $zip->numFiles; $i++) {
			if (file_exists($unzip_dir . $zip->getNameIndex($i))) {
				unlink($unzip_dir . $zip->getNameIndex($i));
			}
		}

		if ($zip->extractTo($unzip_dir) !== TRUE) {
			$zip->close();
			log_message('warning', 'UNZIP 解凍エラー');
			return FALSE;
		}

		$files = [];
		for ($i = 0; $i < $zip->numFiles; $i++) {
			$files[] = $zip->getNameIndex($i);
			if (file_exists($unzip_dir . $zip->getNameIndex($i))) {
				chmod($unzip_dir . $zip->getNameIndex($i), $file_mod);
			}
		}
		$zip->close();
		log_message('info', 'UNZIP 終了');
		return $files;
	}

	//*************************************************************//
	//** logging                                                 **//
	//** ログファイルを出力する。(日付毎)                        **//
	//*************************************************************//
	function logging(string $lv, string $msg)
	{
		try {
			$glogin_id = '<test>';
			$date = date("Ymd");
			$mic = explode(".", (microtime(true) . ""));
			if (count($mic) > 1) {
				$mic = substr($mic[1], 0, 3);
			} else {
				$mic = "000";
			}
			$dtStr = date("Y-m-d H:i:s") . "." . $mic;
			$filepath = $this->get_log_path($date);
			if (($fp = fopen($filepath, 'a'))) {
				fprintf($fp, "%s %s {%s} %s\n", $lv, $dtStr, $glogin_id, $msg);
			}
		} catch (\Exception $ex) {
			//echo 'アクセスログ=更新失敗';echo('<br/>');
			return $ex->getMessage();
		}
	}
	//*************************************************************//
	//** set_msg_login                                           **//
	//** ログイン時のログメッセージを作成                        **//
	//*************************************************************//
	function set_msg_login()
	{
		try {
			// 開始宣言
			$line = "BEGIN site:【login】";
			log_message('debug', $line);
			// ログインユーザー情報取得
			$line = "HEADER ipaddress:" . $_SERVER['REMOTE_ADDR'];
			log_message('debug', $line);
			$line = "HEADER hostaddr:" . $_SERVER['REMOTE_HOST'];
			log_message('debug', $line);
			$line = "HEADER browser:" . $_SERVER['HTTP_USER_AGENT'];
			log_message('debug', $line);
			if (isset($_SERVER['HTTP_REFERER'])) {
				$line = "HEADER refferer:" . $_SERVER['HTTP_REFERER'];
			}
			log_message('debug', $line);

		} catch (\Exception $ex) {
			//echo 'アクセスログ=更新失敗';echo('<br/>');
			return $ex->getMessage();
		}
	}

	//*************************************************************//
	//** set_msg_site                                           **//
	//** サイト開始時のログメッセージを作成                     **//
	//*************************************************************//
	function set_msg_site($site_name, $_args = [])
	{

		log_message('debug', '★Upload_seikyuライブラリ set_msg_site開始');

		// 初期処理
		$keys = array_keys($_args);
		$cones = [];
		$cnt = 0;
		$login_id = "";
		// 引数の処理
		foreach ($keys as $key) {
			$cones[$cnt] = "引数　:" . substr("　　　　　　　　　　" . $key . ":", -10) . $_args[$key];
			$cnt += 1;
		}
		// 開始宣言
		$line = "BEGIN site: 【" . $site_name . "】";
		log_message('debug', $line);
		$line = "USER id:" . $login_id;
		log_message('debug', $line);

		// 引数出力
		for ($wki = 0; $wki < count($cones); $wki++) {
			log_message('debug', '$wki = ' . $wki . '  $cones[$wki] = ' . $cones[$wki]);
		}
	}

	//*************************************************************//
	//** set_msg_end                                            **//
	//** サイト終了時のログメッセージを作成                     **//
	//*************************************************************//
	function set_msg_end($site_name)
	{

		log_message('debug', '★Upload_seikyuライブラリ set_msg_end開始');

		// 開始宣言
		$line = "END site:" . $site_name;
		log_message('debug', $line);
		$line = "===========================================";
		log_message('debug', $line);
	}

	//*************************************************************//
	//** set_msg_site                                           **//
	//** サイト開始時のログメッセージを作成                     **//
	//*************************************************************//
	function set_msg_func($func_name, array $_args = [])
	{

		log_message('debug', '★Upload_seikyuライブラリ set_msg_func開始');

		// 開始宣言
		log_message('debug', "------------------------------------------>");
		$line = "BEGIN FUNC_NAME:" . $func_name;
		log_message('debug', $line);
		$this->put_log_array($_args);
	}

	//*************************************************************//
	//** set_msg_site                                           **//
	//** サイト開始時のログメッセージを作成                     **//
	//*************************************************************//
	function set_msg_func_end($func_name)
	{

		log_message('debug', '★Upload_seikyuライブラリ set_msg_func_end開始');

		// 開始宣言
		$line = "END FUNC_NAME:" . $func_name;
		log_message('debug', $line);
		log_message('debug', "<------------------------------------------");
	}

	//*************************************************************//
	//** set_msg_site                                           **//
	//** サイト開始時のログメッセージを作成                     **//
	//*************************************************************//
	function set_trace($args)
	{

		log_message('debug', '★Upload_seikyuライブラリ set_trace 開始');

		// 開始宣言
		$line = "FILE_NAME:" . $args[0]["file"];
		log_message("debug", $line);
		$line = "LINE:" . $args[0]["line"];
		log_message("debug", $line);
		$line = "FUNCTION:" . $args[0]["function"];
		log_message("debug", $line);
	}

	//*************************************************************//
	//** put_log_array                                           **//
	//** 連想配列の情報をログに出力する                          **//
	//*************************************************************//
	function put_log_array($_array)
	{

		log_message('debug', '★Upload_seikyuライブラリ put_log_array 開始');

		// 初期処理
		$keys = array_keys($_array);
		$login_id = "";

		// 引数の処理
		foreach ($keys as $key) {
			if (gettype($_array[$key]) != 'array' && gettype($_array[$key]) != 'object') {
				$cont = "引数　:" . substr("　　　　　　　　　　" . $key . ":", -10) . $_array[$key];
				log_message('debug', $cont);
			}

		}
	}

	//*************************************************************//
	//** put_log_exception                                       **//
	//** 連想配列の情報をログに出力する                          **//
	//*************************************************************//
	function put_log_exception($_site, $_code, $_msg, $_trace)
	{

		log_message('debug', '★Upload_seikyuライブラリ put_log_exception 開始');

		log_message('error', "エラー番号:" . $_code);
		log_message('error', "メッセージ:" . $_msg);
		$this->set_trace($_trace);
		$this->set_msg_end($_site);
	}

	//*************************************************************//
	//** put_log_func_exception                                       **//
	//** 連想配列の情報をログに出力する                          **//
	//*************************************************************//
	function put_log_func_exception($_site, $_code, $_msg, $_trace)
	{
		log_message('debug', '★Upload_seikyuライブラリ put_log_func_exception 開始');
		log_message('error', "エラー番号:" . $_code);
		log_message('error', "メッセージ:" . $_msg);
		$this->set_trace($_trace);
		$this->set_msg_func_end($_site);
	}

	//*************************************************************//
	//** get_log_path                                            **//
	//** ログパス取得                                            **//
	//*************************************************************//
	function get_log_path($_date, $kbn = 0)
	{
		log_message('debug', '★Upload_seikyuライブラリ get_log_path 開始');
		switch ($kbn) {
			case 0:
				// WEBログ
				return WRITEPATH . "logs/" . $_date . "_log.txt";
			default:
				return WRITEPATH . "logs/" . $_date . "_log.txt";
		}
	}

	//*************************************************************//
	//** put_parcent                                             **//
	//** ％ログ表示                                              **//
	//*************************************************************//
	function put_status($_pos, $_state)
	{
		log_message('debug', '★Upload_seikyuライブラリ put_status 開始');
		$state = array(
			"position" => $_pos,
			"status" => $_state
		);
		$json = json_encode($state);
		$filepath = WRITEPATH . "status.txt";
		if (($fp = fopen($filepath, 'w'))) {
			fprintf($fp, $json);
		}
	}

	//*************************************************************//
	//** put_parcent                                             **//
	//** ％ログ表示                                              **//
	//*************************************************************//
	function put_parcent($_name, $_cnt, $_max)
	{
		log_message('debug', '★Upload_seikyuライブラリ put_parcent 開始');
		$max_cnt = $_max;
		$max_cnt = round($max_cnt / 100) * 100;
		if ($max_cnt > 0 && $_cnt > 0) {
			switch ($_cnt / $max_cnt) {
				case 0.1:
					log_message('info', $_name . '10%');
					break;
				case 0.2:
					log_message('info', $_name . '20%');
					break;
				case 0.3:
					log_message('info', $_name . '30%');
					break;
				case 0.4:
					log_message('info', $_name . '40%');
					break;
				case 0.5:
					log_message('info', $_name . '50%');
					break;
				case 0.6:
					log_message('info', $_name . '60%');
					break;
				case 0.7:
					log_message('info', $_name . '70%');
					break;
				case 0.8:
					log_message('info', $_name . '80%');
					break;
				case 0.9:
					log_message('info', $_name . '90%');
					break;
			}
		}
	}
}
?>