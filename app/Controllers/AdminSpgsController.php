<?php
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　Modeule           Spgsadmin　Controller
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2024.01.22  tanaka       Mnt-001  請求書と決済オプション
//2024.01.29  tanaka       Mnt-002  ログイン履歴照会をCSVでダウンロード
//2024.08.20  H.Matsu      Mnt-003  1) ログインチェックに$login_misecd追加 + セッション項目追加
//                                  2) 部分アップロードを追加（全アップと書込箇所は統合
//                                  3) ユーザーパスワード更新を追加
//2024.10.09  H.Matsu      Mnt-004  ユーザー指定で関連テーブル初期化
//2025.01.15  tanaka       Mnt-005  料金三部制（設備使用料対応）
//2025.09.16  tanaka       Mnt-008  アップロード時のタイムアウトを10分に変更
//2025.11.04  tanaka       Mnt-009  転入前ユーザー分の請求書削除処理追加
//2025.12.10  kimura       Mnt-010  CI4対応
//**************************************************************************

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Upload_seikyu;
use CodeIgniter\HTTP\ResponseInterface;

class AdminSpgsController extends BaseController
{

	public function __construct()
	{

	}

	public function menu()
	{
		log_message('debug', '★AdminSpgsController menu 実行');

		// View に渡す
		$data = [
			'login_id' => session('login_id'),
			'login_name' => session('login_name'),
			'login_misecd' => session('login_misecd'),
			'login_usercd' => session('login_usercd'),
			'login_dspusercd' => session('login_dspusercd'),
			'misecd' => session('login_misecd'),
			'usercd' => session('login_usercd'),

			//画像ファイル
			'header_img01' => $this->userConfig->header_img01,
			'header_url01' => $this->userConfig->header_url01,
			'header_img02' => $this->userConfig->header_img02,
			'header_url02' => $this->userConfig->header_url02,

			//販売店ごとの設定
			'oshirase_flg' => $this->userConfig->oshirase_flg,
			'bill_flg' => $this->userConfig->bill_flg,
			'max_button' => $this->userConfig->max_button,
		];

		$data['buttons'] = session('buttons');
		$data['files'] = session('files');

		return view('adminview/spgsadmin_menu', $data);

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	照会履歴ページ
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function userlogin_disp()
	{
		log_message('debug', '★AdminSpgsController userlogin_disp実行');

		$csv_submit = $this->request->getGet('csv_submit');

		$ymd = $this->request->getVar('kensaku_ymd');
		$usercd = $this->request->getVar('kensaku_usercd');

		if ($csv_submit === 'csv') {
			return $this->download_seekhistory($ymd, $usercd);
		}

		if ($usercd != '') {
			$kensaku_usercd = preg_replace('/[^0-9]/', '', $usercd);
			// 9桁に満たない場合は右側にゼロ詰め
			$kensaku_usercd = str_pad($kensaku_usercd, 9, 0, STR_PAD_RIGHT);
		} else {
			$kensaku_usercd = '';
		}
		if ($ymd != '') {
			$kensaku_ymd = preg_replace('/[^0-9]/', '', $ymd);
		} else {
			$kensaku_ymd = '';
		}
		log_message('debug', '★AdminSpgsController userlogin_disp $kensaku_usercd=' . $kensaku_usercd);
		log_message('debug', '★AdminSpgsController userlogin_disp $kensaku_ymd=' . $kensaku_ymd);

		// $offset = (int) $this->uri->segment(3, 0);
		$uri = service('uri');
		$offset = (int) $uri->getSegment(3, 0);
		log_message('debug', '★AdminSpgsController userlogin_disp $offset=' . $offset);

		//販売店ごとの設定
		$data = [
			'buttons' => session('buttons'),
			'max_button' => $this->adminConfig->max_button,
		];

		# 顧客コードとoffset値と、1ページに表示するレコードの数を渡し、モデルより
		# ログイン一覧を取得します。
		// $data['query'] = $this->Spgs->get_login_list2($this->adminConfig->meisai_rec, $offset, $kensaku_ymd, $kensaku_usercd);
		$data['query'] = $this->get_login_list2($this->adminConfig->meisai_rec, $offset, $kensaku_ymd, $kensaku_usercd);

		// $total = $this->get_login_count2($kensaku_ymd, $kensaku_usercd);
		$total = $this->get_login_count2($kensaku_ymd, $kensaku_usercd);

		$data['login_id'] = session('login_id');
		$data['login_pwd'] = session('login_pwd');
		$data['login_name'] = session('login_name');
		$data['login_grant'] = session('login_grant');
		$data['list_total'] = $total;
		$data['list_limit'] = $this->adminConfig->meisai_rec;
		$data['list_offset'] = $offset;

		// ポストバックされた店とコードを使用する
		$data['kensaku_usercd'] = $kensaku_usercd;
		$data['kensaku_ymd'] = $kensaku_ymd;

		$data['header_img01'] = $this->adminConfig->header_img01;
		$data['header_url01'] = $this->adminConfig->header_url01;
		$data['header_img02'] = $this->adminConfig->header_img02;
		$data['header_url02'] = $this->adminConfig->header_url02;
		$data['buttons'] = session('buttons');
		$data['max_button'] = $this->adminConfig->max_button;

		log_message('debug', '★AdminSpgsController kensin_disp spgskensin_result=' . print_r($data['header_img01'], true));

		return view('adminview/spgsadmin_userlogin_disp', $data);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	ファイルアップロード
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function upload()
	{
		log_message('debug', '★AdminSpgsController upload を実行しました');

		//販売店ごとの設定
		$data['login_id'] = session('login_id');
		$data['login_pwd'] = session('login_pwd');
		$data['login_name'] = session('login_name');
		$data['login_grant'] = session('login_grant');
		$data['error'] = '';
		$data['header_img01'] = $this->adminConfig->header_img01;
		$data['header_url01'] = $this->adminConfig->header_url01;
		$data['header_img02'] = $this->adminConfig->header_img02;
		$data['header_url02'] = $this->adminConfig->header_url02;
		$data['buttons'] = session('buttons');
		$data['max_button'] = $this->adminConfig->max_button;

		log_message('debug', '★AdminSpgsController upload spgsadmin_upload View表示します');
		log_message('debug', '★AdminSpgsController upload upload View表示します');

		return view('adminview/spgsadmin_upload', $data);

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	ファイルアップロード実行（$queryをポストバックしてそのまま使用する）
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function upload_exec()
	{
		log_message('debug', '★AdminSpgsController upload_exec を実行しました');

		//////////////////////////////////////////////////////////////////////////////////////////////
		//	出力用ワークファイル作成
		//////////////////////////////////////////////////////////////////////////////////////////////
		$config['upload_path'] = WRITEPATH . 'fileup/';
		$config['allowed_types'] = '*';
		$config['overwrite'] = true;

		//「0」は無制限
		$config['max_size'] = '0';
		$config['max_width'] = '0';
		$config['max_height'] = '0';

		$file = $this->request->getFile('userfile'); // viewで指定したファイル名

		if (!$file->isValid()) {

			// アップロードエラー
			log_message('debug', '★AdminSpgsController upload_exec ファイルが取得できませんでした');
			// $msg = $this->upload->display_errors(' <p style="color:blue;">', '</p>');
			$msg = $file->getErrorString();
			//販売店ごとの設定
			$data['login_id'] = session('login_id');
			$data['login_pwd'] = session('login_pwd');
			$data['login_name'] = session('login_name');
			$data['login_grant'] = session('login_grant');
			$data['error'] = $msg;
			$data['header_img01'] = $this->adminConfig->header_img01;
			$data['header_url01'] = $this->adminConfig->header_url01;
			$data['header_img02'] = $this->adminConfig->header_img02;
			$data['header_url02'] = $this->adminConfig->header_url02;
			$data['buttons'] = session('buttons');
			$data['max_button'] = $this->adminConfig->max_button;
			return view('adminview/spgsadmin_upload', $data);
		}

		log_message('debug', '★AdminSpgsController upload_exec ファイル取得成功');

		$filetype = $file->getMimeType();
		$filename1 = $file->getName();

		// ファイルを移動
		$file->move(WRITEPATH . 'fileup/', $filename1, true);  // 第3引数 true で上書き

		$fullpath = WRITEPATH . 'fileup/' . $filename1;
		$filepath = WRITEPATH . 'fileup/';

		$data['error'] = '';

		// ファイルを読んでDBに書き出し
		//	$msg = $this->upload_read($filename );
		if ($filetype == 'text/plain') {

			$msg = $this->upload2_read($filename1, 0);

		} else if ($filetype == 'application/x-zip-compressed' || $filetype == 'application/zip') {
			$zip = new \ZipArchive();
			if (!$zip->open($fullpath)) {
				$zip->close();
				echo 'zipファイルのオープンに失敗しました。' . PHP_EOL;
				exit(1);
			} else {
				$filename2 = $zip->getNameIndex(0);
				$zip->extractTo($filepath);
				$zip->close();

				//[Mnt-003]------------------------------------------------------------------------------------------>> Edit Start 24/08/20
//					$msg = $this->upload_read($filename2);
				$msg = $this->upload2_read($filename2, 0);
				//[Mnt-003]------------------------------------------------------------------------------------------>> Edit End   24/08/20

			}
		} else {
			echo 'ファイル形式が違います。';
			exit(1);
		}

		$data['login_id'] = session('login_id');
		$data['login_pwd'] = session('login_pwd');
		$data['login_name'] = session('login_name');
		$data['login_grant'] = session('login_grant');
		$data['header_img01'] = $this->adminConfig->header_img01;
		$data['header_url01'] = $this->adminConfig->header_url01;
		$data['header_img02'] = $this->adminConfig->header_img02;
		$data['header_url02'] = $this->adminConfig->header_url02;
		$data['buttons'] = session('buttons');
		$data['max_button'] = $this->adminConfig->max_button;

		//echo '$msg=' . $msg;
		if ($msg != '') {
			$data['error'] = $msg;
			return view('adminview/spgsadmin_upload', $data);
		} else {
			session()->setFlashdata('msg', 'アップロードが完了しました。');
			return redirect()->to('spgsadmin/upload_complete');
		}

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	アップロードされたファイルを読み、処理を実行
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function upload_read($filename = '')
	{

		log_message('debug', '★AdminSpgsController upload_read　実行');

		$nowymd = date("Ymd");	#現在日
		$nowtime = date("His");	#現在時刻

		if (!($fp = fopen(WRITEPATH . 'fileup/' . $filename, "r"))) {
			fclose($fp);
			$msg = 'ファイルが開けません。';
			return $msg;
		} else {
			#トランザクションスタート
			$this->db->transStart();
			$this->table_del();
			$count = 0;

			//ファイルの読み込みと表示
			//１行ずつファイルを読み込んで、表示する。
			while (!feof($fp)) {
				$count++;
				$buffer = fgets($fp);	// そのままSJISでデータを取得する。（このままだと内部で使用できない）
				//echo 'buffer= '	. mb_convert_encoding($buffer, 'UTF-8', 'SJIS'); echo('<br/>');

				if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '1')
				/////  「SPGSUSER」の処理     //////////////////////////////////////////////////
				{
					// 機種依存文字（全角文字）を変換するときはSJIS-WINで
					$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
					$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
					$usercd = mb_convert_encoding(substr($buffer, 5, 9), 'UTF-8', 'SJIS');
					$dspusercd = mb_convert_encoding(substr($buffer, 14, 11), 'UTF-8', 'SJIS');
					$name = mb_convert_encoding(substr($buffer, 25, 40), 'UTF-8', 'SJIS-WIN');
					$id = mb_convert_encoding(substr($buffer, 65, 20), 'UTF-8', 'SJIS');
					$pwd = mb_convert_encoding(substr($buffer, 85, 20), 'UTF-8', 'SJIS');
					$mail = mb_convert_encoding(substr($buffer, 105, 50), 'UTF-8', 'SJIS');
					$ryokinno = intval(mb_convert_encoding(substr($buffer, 155, 3), 'UTF-8', 'SJIS'));
					$siyoryo = mb_convert_encoding(substr($buffer, 158, 5), 'UTF-8', 'SJIS');
					$kihon = mb_convert_encoding(substr($buffer, 163, 6), 'UTF-8', 'SJIS');
					$ryokin = mb_convert_encoding(substr($buffer, 169, 7), 'UTF-8', 'SJIS');
					$zeikbn = intval(mb_convert_encoding(substr($buffer, 176, 1), 'UTF-8', 'SJIS'));
					$msgno = mb_convert_encoding(substr($buffer, 177, 2), 'UTF-8', 'SJIS');
					/*
										echo 'spgsuser';		echo('<br/>');
										echo '$status = ' . $status;	echo('<br/>');
										echo '$misecd = ' . $misecd;	echo('<br/>');
										echo '$usercd = ' . $usercd;	echo('<br/>');
										echo '$dspusercd = ' . $dspusercd;	echo('<br/>');
										echo '$name = ' . $name;	echo('<br/>');
										echo '$id = ' . $id;	echo('<br/>');
										echo '$pwd = ' . $pwd;	echo('<br/>');
										echo '$mail = ' . $mail;	echo('<br/>');
										echo '$ryokinno = ' . $ryokinno;	echo('<br/>');
										echo '$siyoryo = ' . $siyoryo;	echo('<br/>');
										echo '$kihon = ' . $kihon;	echo('<br/>');
										echo '$ryokin = ' . $ryokin;	echo('<br/>');
										echo '$nowymd = ' . $nowymd;	echo('<br/>');
										echo '$nowtime = ' . $nowtime;	echo('<br/>');
					*/
					$wkdt = array(
						'misecd' => $misecd,
						'usercd' => $usercd,
						'dspusercd' => $dspusercd,
						'name' => $name,
						'id' => $id,
						'pwd' => $pwd,
						'mail' => $mail,
						'ryokinno' => $ryokinno,
						'siyoryo' => $siyoryo,
						'kihon' => $kihon,
						'ryokin' => $ryokin,
						'status' => $status,
						'entryymd' => $nowymd,
						'entrytime' => $nowtime,
						'zeikbn' => $zeikbn,	// 税区分
						'msgno' => $msgno
					);
					$this->db->table('spgsuser')->insert($wkdt);

					// 直前に実行したSQL文表示
					//	echo $this->db->last_query(); echo('<br/>?');
					//	echo "SPGSUSER更新" ; echo('<br/>?');
				}

				//////////////////////////////////////////////////////////////////////////////////
				else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '2')
				/////  「SPGSKENSIN」の処理     //////////////////////////////////////////////////
				{
					// 機種依存文字（全角文字）を変換するときはSJIS-WINで
					// メーター交換時取付指針追加 2018.01.17
					$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
					$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
					$usercd = mb_convert_encoding(substr($buffer, 5, 9), 'UTF-8', 'SJIS');
					$konkensinymd = mb_convert_encoding(substr($buffer, 14, 10), 'UTF-8', 'SJIS');
					$konkensinsisin = mb_convert_encoding(substr($buffer, 24, 9), 'UTF-8', 'SJIS');
					$konkensinsiyoryo = mb_convert_encoding(substr($buffer, 33, 9), 'UTF-8', 'SJIS');
					$zenkensinymd = mb_convert_encoding(substr($buffer, 42, 10), 'UTF-8', 'SJIS');
					$zenkensinsisin = mb_convert_encoding(substr($buffer, 52, 9), 'UTF-8', 'SJIS');
					$zensiyoryo = mb_convert_encoding(substr($buffer, 61, 9), 'UTF-8', 'SJIS');
					$kihonryokin = mb_convert_encoding(substr($buffer, 70, 8), 'UTF-8', 'SJIS-WIN');
					$jyuryoryokin = mb_convert_encoding(substr($buffer, 78, 8), 'UTF-8', 'SJIS-WIN');
					$shouhizei = mb_convert_encoding(substr($buffer, 86, 8), 'UTF-8', 'SJIS-WIN');
					$gasryokin = mb_convert_encoding(substr($buffer, 94, 8), 'UTF-8', 'SJIS-WIN');

					$toriymd1 = mb_convert_encoding(substr($buffer, 102, 10), 'UTF-8', 'SJIS');
					$toriname1 = mb_convert_encoding(substr($buffer, 112, 40), 'UTF-8', 'SJIS-WIN');
					$torisuu1 = mb_convert_encoding(substr($buffer, 152, 8), 'UTF-8', 'SJIS');
					$torikin1 = mb_convert_encoding(substr($buffer, 160, 9), 'UTF-8', 'SJIS');

					$toriymd2 = mb_convert_encoding(substr($buffer, 169, 10), 'UTF-8', 'SJIS');
					$toriname2 = mb_convert_encoding(substr($buffer, 179, 40), 'UTF-8', 'SJIS-WIN');
					$torisuu2 = mb_convert_encoding(substr($buffer, 219, 8), 'UTF-8', 'SJIS');
					$torikin2 = mb_convert_encoding(substr($buffer, 227, 9), 'UTF-8', 'SJIS');

					$toriymd3 = mb_convert_encoding(substr($buffer, 236, 10), 'UTF-8', 'SJIS');
					$toriname3 = mb_convert_encoding(substr($buffer, 246, 40), 'UTF-8', 'SJIS-WIN');
					$torisuu3 = mb_convert_encoding(substr($buffer, 286, 8), 'UTF-8', 'SJIS');
					$torikin3 = mb_convert_encoding(substr($buffer, 294, 9), 'UTF-8', 'SJIS');

					$toriymd4 = mb_convert_encoding(substr($buffer, 303, 10), 'UTF-8', 'SJIS');
					$toriname4 = mb_convert_encoding(substr($buffer, 313, 40), 'UTF-8', 'SJIS-WIN');
					$torisuu4 = mb_convert_encoding(substr($buffer, 353, 8), 'UTF-8', 'SJIS');
					$torikin4 = mb_convert_encoding(substr($buffer, 361, 9), 'UTF-8', 'SJIS');

					$toriymd5 = mb_convert_encoding(substr($buffer, 370, 10), 'UTF-8', 'SJIS');
					$toriname5 = mb_convert_encoding(substr($buffer, 380, 40), 'UTF-8', 'SJIS-WIN');
					$torisuu5 = mb_convert_encoding(substr($buffer, 420, 8), 'UTF-8', 'SJIS');
					$torikin5 = mb_convert_encoding(substr($buffer, 428, 9), 'UTF-8', 'SJIS');

					$toriymd6 = mb_convert_encoding(substr($buffer, 437, 10), 'UTF-8', 'SJIS');
					$toriname6 = mb_convert_encoding(substr($buffer, 447, 40), 'UTF-8', 'SJIS-WIN');
					$torisuu6 = mb_convert_encoding(substr($buffer, 487, 8), 'UTF-8', 'SJIS');
					$torikin6 = mb_convert_encoding(substr($buffer, 495, 9), 'UTF-8', 'SJIS');

					$torigokeikin = mb_convert_encoding(substr($buffer, 504, 13), 'UTF-8', 'SJIS-WIN');
					$seikyukin = mb_convert_encoding(substr($buffer, 517, 13), 'UTF-8', 'SJIS-WIN');
					$siharai = mb_convert_encoding(substr($buffer, 530, 20), 'UTF-8', 'SJIS-WIN');
					$furiymd = mb_convert_encoding(substr($buffer, 550, 8), 'UTF-8', 'SJIS-WIN');

					$meterkoukanymd = mb_convert_encoding(substr($buffer, 558, 10), 'UTF-8', 'SJIS');
					$meterkoukanzenkai = mb_convert_encoding(substr($buffer, 568, 9), 'UTF-8', 'SJIS');
					$meterkoukanhikitori = mb_convert_encoding(substr($buffer, 577, 9), 'UTF-8', 'SJIS');
					$meterkoukantorituke = mb_convert_encoding(substr($buffer, 586, 9), 'UTF-8', 'SJIS');
					$meterkoukankyusiyo = mb_convert_encoding(substr($buffer, 595, 9), 'UTF-8', 'SJIS');

					$tanka1 = mb_convert_encoding(substr($buffer, 604, 11), 'UTF-8', 'SJIS');
					$tanka2 = mb_convert_encoding(substr($buffer, 615, 11), 'UTF-8', 'SJIS');
					$tanka3 = mb_convert_encoding(substr($buffer, 626, 11), 'UTF-8', 'SJIS');
					$tanka4 = mb_convert_encoding(substr($buffer, 637, 11), 'UTF-8', 'SJIS');
					$tanka5 = mb_convert_encoding(substr($buffer, 648, 11), 'UTF-8', 'SJIS');
					$tanka6 = mb_convert_encoding(substr($buffer, 659, 11), 'UTF-8', 'SJIS');
					$gasnebiki = mb_convert_encoding(substr($buffer, 670, 12), 'UTF-8', 'SJIS');
					/*
										echo 'spgskensin';	echo('<br/>');
										echo '$status = ' . $status;	echo('<br/>');
										echo '$misecd = ' . $misecd;	echo('<br/>');
										echo '$usercd = ' . $usercd;	echo('<br/>');
										echo '$konkensinymd = ' . $konkensinymd;	echo('<br/>');
										echo '$konkensinsisin = ' . $konkensinsisin;	echo('<br/>');
										echo '$konkensinsiyoryo = ' . $konkensinsiyoryo;	echo('<br/>');
										echo '$zenkensinymd = ' . $zenkensinymd;	echo('<br/>');
										echo '$zenkensinsisin = ' . $zenkensinsisin;	echo('<br/>');
										echo '$zensiyoryo = ' . $zensiyoryo;	echo('<br/>');
										echo '$kihonryokin = ' . $kihonryokin;	echo('<br/>');
										echo '$jyuryoryokin = ' . $jyuryoryokin;	echo('<br/>');
										echo '$shouhizei = ' . $shouhizei;	echo('<br/>');
										echo '$gasuryokin = ' . $gasryokin;	echo('<br/>');

										echo '$toriymd1 = ' . $toriymd1;	echo('<br/>');
										echo '$toriname1 = ' . $toriname1;	echo('<br/>');
										echo '$torisuu1 = ' . $torisuu1;	echo('<br/>');
										echo '$torikin1 = ' . $torikin1;	echo('<br/>');

										echo '$toriymd2 = ' . $toriymd2;	echo('<br/>');
										echo '$toriname2 = ' . $toriname2;	echo('<br/>');
										echo '$torisuu2 = ' . $torisuu2;	echo('<br/>');
										echo '$torikin2 = ' . $torikin2;	echo('<br/>');

										echo '$toriymd3 = ' . $toriymd3;	echo('<br/>');
										echo '$toriname3 = ' . $toriname3;	echo('<br/>');
										echo '$torisuu3 = ' . $torisuu3;	echo('<br/>');
										echo '$torikin3 = ' . $torikin3;	echo('<br/>');

										echo '$toriymd4 = ' . $toriymd4;	echo('<br/>');
										echo '$toriname4 = ' . $toriname4;	echo('<br/>');
										echo '$torisuu4 = ' . $torisuu4;	echo('<br/>');
										echo '$torikin4 = ' . $torikin4;	echo('<br/>');

										echo '$toriymd5 = ' . $toriymd5;	echo('<br/>');
										echo '$toriname5 = ' . $toriname5;	echo('<br/>');
										echo '$torisuu5 = ' . $torisuu5;	echo('<br/>');
										echo '$torikin5 = ' . $torikin5;	echo('<br/>');

										echo '$toriymd6 = ' . $toriymd6;	echo('<br/>');
										echo '$toriname6 = ' . $toriname6;	echo('<br/>');
										echo '$torisuu6 = ' . $torisuu6;	echo('<br/>');
										echo '$torikin6 = ' . $torikin6;	echo('<br/>');

										echo '$torigokeikin = ' . $torigokeikin;	echo('<br/>');
										echo '$sekyukin = ' . $seikyukin;	echo('<br/>');
										echo '$siharai = ' . $siharai;	echo('<br/>');
										echo '$furiymd = ' . $furiymd;	echo('<br/>');

										echo 'meterkoukanymd = ' . $meterkoukanymd;	echo('<br/>');
										echo 'meterkoukanzenkai = ' . $meterkoukanzenkai;	echo('<br/>');
										echo 'meterkoukanhikitori = ' . $meterkoukanhikitori;	echo('<br/>');
										echo 'meterkoukanotorituke = ' . $meterkoukantorituke;	echo('<br/>');
										echo 'meterkoukankyusiyo = ' . $meterkoukankyusiyo;	echo('<br/>');
					*/
					$wkdt = array(
						'misecd' => $misecd,
						'usercd' => $usercd,
						'konkensinymd' => $konkensinymd,
						'konkensinsisin' => $konkensinsisin,
						'konkensinsiyoryo' => $konkensinsiyoryo,
						'zenkensinymd' => $zenkensinymd,
						'zenkensinsisin' => $zenkensinsisin,
						'zensiyoryo' => $zensiyoryo,
						'kihonryokin' => $kihonryokin,
						'jyuryoryokin' => $jyuryoryokin,
						'shouhizei' => $shouhizei,
						'gasryokin' => $gasryokin,
						'toriymd1' => $toriymd1,
						'toriname1' => $toriname1,
						'torisuu1' => $torisuu1,
						'tanka1' => $tanka1,
						'torikin1' => $torikin1,
						'toriymd2' => $toriymd2,
						'toriname2' => $toriname2,
						'torisuu2' => $torisuu2,
						'tanka2' => $tanka2,
						'torikin2' => $torikin2,
						'toriymd3' => $toriymd3,
						'toriname3' => $toriname3,
						'torisuu3' => $torisuu3,
						'tanka3' => $tanka3,
						'torikin3' => $torikin3,
						'toriymd4' => $toriymd4,
						'toriname4' => $toriname4,
						'torisuu4' => $torisuu4,
						'tanka4' => $tanka4,
						'torikin4' => $torikin4,
						'toriymd5' => $toriymd5,
						'toriname5' => $toriname5,
						'torisuu5' => $torisuu5,
						'tanka5' => $tanka5,
						'torikin5' => $torikin5,
						'toriymd6' => $toriymd6,
						'toriname6' => $toriname6,
						'torisuu6' => $torisuu6,
						'tanka6' => $tanka6,
						'torikin6' => $torikin6,
						'torigokeikin' => $torigokeikin,
						'seikyukin' => $seikyukin,
						'siharai' => $siharai,
						'furiymd' => $furiymd,
						'meterkoukanymd' => $meterkoukanymd,
						'meterkoukanzenkai' => $meterkoukanzenkai,
						'meterkoukanhikitori' => $meterkoukanhikitori,
						'meterkoukantorituke' => $meterkoukantorituke,
						'meterkoukankyusiyo' => $meterkoukankyusiyo,
						'status' => $status,
						'gasnebiki' => $gasnebiki,
						'entryymd' => $nowymd,
						'entrytime' => $nowtime
					);
					$this->db->table('spgskensin')->insert($wkdt);
					//echo "SPGSKENSIN更新" ; echo('<br/>?');

				}
				//////////////////////////////////////////////////////////////////////////////////
				else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '3')
				/////  「SPGSTORI」の処理     //////////////////////////////////////////////////
				{
					// 機種依存文字（全角文字）を変換するときはSJIS-WINで
					$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
					$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
					$usercd = mb_convert_encoding(substr($buffer, 5, 9), 'UTF-8', 'SJIS');
					$denshuname = mb_convert_encoding(substr($buffer, 14, 6), 'UTF-8', 'SJIS-WIN');
					$ymd = mb_convert_encoding(substr($buffer, 20, 10), 'UTF-8', 'SJIS');
					$hin = mb_convert_encoding(substr($buffer, 30, 40), 'UTF-8', 'SJIS-WIN');
					$kata = mb_convert_encoding(substr($buffer, 70, 20), 'UTF-8', 'SJIS-WIN');
					$suu = mb_convert_encoding(substr($buffer, 90, 9), 'UTF-8', 'SJIS');
					$tanka = mb_convert_encoding(substr($buffer, 99, 11), 'UTF-8', 'SJIS');
					$kin = mb_convert_encoding(substr($buffer, 110, 11), 'UTF-8', 'SJIS');

					/*
					echo 'spgstori'; echo('<br/>');
					echo '$status = ' . $status;	echo('<br/>');
					echo '$misecd = ' . $misecd;	echo('<br/>');
					echo '$usercd = ' . $usercd;	echo('<br/>');
					echo '$denshuname = ' . $denshuname;	echo('<br/>');
					echo '$ymd = ' . $ymd;	echo('<br/>');
					echo '$hin = ' . $hin;	echo('<br/>');
					echo '$kata = ' . $kata;	echo('<br/>');
					echo '$suu = ' . $suu;	echo('<br/>');
					echo '$tanka = ' . $tanka;	echo('<br/>');
					echo '$kin = ' . $kin;	echo('<br/>');
					*/

					$wkdt = array(
						'misecd' => $misecd,
						'usercd' => $usercd,
						'denshuname' => $denshuname,
						'ymd' => $ymd,
						'hin' => $hin,
						'kata' => $kata,
						'suu' => $suu,
						'tanka' => $tanka,
						'kin' => $kin,
						'status' => $status,
						'entryymd' => $nowymd,
						'entrytime' => $nowtime
					);
					$this->db->table('spgstori')->insert($wkdt);
					//echo "SPGSTORI更新" ; echo('<br/>?');
				}
				//////////////////////////////////////////////////////////////////////////////////
				else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '4')
				/////  「SPGSRYOKIN」の処理     //////////////////////////////////////////////////
				{
					// 機種依存文字（全角文字）を変換するときはSJIS-WINで
					$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
					$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
					$ryokinno = mb_convert_encoding(substr($buffer, 5, 3), 'UTF-8', 'SJIS');
					$kaisono = mb_convert_encoding(substr($buffer, 8, 3), 'UTF-8', 'SJIS');
					$siyoryo = mb_convert_encoding(substr($buffer, 11, 9), 'UTF-8', 'SJIS-WIN');
					$kihon = mb_convert_encoding(substr($buffer, 20, 5), 'UTF-8', 'SJIS');
					$ryokin = mb_convert_encoding(substr($buffer, 25, 7), 'UTF-8', 'SJIS');
					$zeikbn = intval(mb_convert_encoding(substr($buffer, 32, 1), 'UTF-8', 'SJIS'));
					// 簡易ガス表示用追加 2022/08/23
					$bunrui = intval(mb_convert_encoding(substr($buffer, 33, 1), 'UTF-8', 'SJIS'));
					$kanikihon = mb_convert_encoding(substr($buffer, 34, 10), 'UTF-8', 'SJIS');
					$kaniryokin = mb_convert_encoding(substr($buffer, 44, 10), 'UTF-8', 'SJIS');

					/*
					echo 'spgsryokin'; echo('<br/>');
					echo '$status = ' . $status;	echo('<br/>');
					echo '$misecd = ' . $misecd;	echo('<br/>');
					echo '$ryokinno = ' . $ryokinno;	echo('<br/>');
					echo '$kaisono = ' . $kaisono;	echo('<br/>');
					echo '$siyoryo = ' . $siyoryo;	echo('<br/>');
					echo '$kihon = ' . $kihon;	echo('<br/>');
					echo '$ryokin = ' . $ryokin;	echo('<br/>');
					*/

					$wkdt = array(
						'misecd' => $misecd,
						'ryokinno' => $ryokinno,
						'kaisono' => $kaisono,
						'siyoryo' => $siyoryo,
						'kihon' => $kihon,
						'ryokin' => $ryokin,
						'zeikbn' => $zeikbn,
						'status' => $status,
						// 簡易ガス表示用追加 2022/08/23
						'bunrui' => $bunrui,
						'kkihon' => $kanikihon,
						'kryokin' => $kaniryokin,

						'entryymd' => $nowymd,
						'entrytime' => $nowtime
					);
					$this->db->table('spgsryokin')->insert($wkdt);
					//echo "SPGSRYOKIN更新" ; echo('<br/>?');
				}
				//////////////////////////////////////////////////////////////////////////////////
				else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '5')
				/////  「SPGSKIGU」の処理     //////////////////////////////////////////////////
				{
					// 機種依存文字（全角文字）を変換するときはSJIS-WINで
					$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
					$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
					$usercd = mb_convert_encoding(substr($buffer, 5, 9), 'UTF-8', 'SJIS');
					$kigurenban = mb_convert_encoding(substr($buffer, 14, 3), 'UTF-8', 'SJIS');
					$kigu = mb_convert_encoding(substr($buffer, 17, 40), 'UTF-8', 'SJIS-WIN');
					$kata = mb_convert_encoding(substr($buffer, 57, 20), 'UTF-8', 'SJIS-WIN');
					$suu = mb_convert_encoding(substr($buffer, 77, 3), 'UTF-8', 'SJIS');
					$ym = mb_convert_encoding(substr($buffer, 80, 7), 'UTF-8', 'SJIS');
					$anzen1 = mb_convert_encoding(substr($buffer, 87, 10), 'UTF-8', 'SJIS-WIN');
					$anzen2 = mb_convert_encoding(substr($buffer, 97, 10), 'UTF-8', 'SJIS-WIN');
					$anzen3 = mb_convert_encoding(substr($buffer, 107, 10), 'UTF-8', 'SJIS-WIN');

					/*
					 echo 'spgskigu';	echo('<br/>');
					echo '$status = ' . $status;	echo('<br/>');
					echo '$misecd = ' . $misecd;	echo('<br/>');
					echo '$usercd = ' . $usercd;	echo('<br/>');
					echo '$kigurenban = ' . $kigurenban;	echo('<br/>');
					echo '$kigu = ' . $kigu;	echo('<br/>');
					echo '$kata = ' . $kata;	echo('<br/>');
					echo '$suu = ' . $suu;	echo('<br/>');
					echo '$ym = ' . $ym;	echo('<br/>');
					echo '$anzen1 = ' . $anzen1;	echo('<br/>');
					echo '$anzen2 = ' . $anzen2;	echo('<br/>');
					echo '$anzen3 = ' . $anzen3;	echo('<br/>');
					*/

					$wkdt = array(
						'misecd' => $misecd,
						'usercd' => $usercd,
						'kigurenban' => $kigurenban,
						'kigu' => $kigu,
						'kata' => $kata,
						'suu' => $suu,
						'ym' => $ym,
						'anzen1' => $anzen1,
						'anzen2' => $anzen2,
						'anzen3' => $anzen3,
						'status' => $status,
						'entryymd' => $nowymd,
						'entrytime' => $nowtime
					);
					$this->db->table('spgskigu')->insert($wkdt);
					//echo "SPGSKIGU更新" ; echo('<br/>?');


				}
				//////////////////////////////////////////////////////////////////////////////////
				else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '6')
				/////  「SPGSKENMSG」の処理     //////////////////////////////////////////////////
				{
					// 機種依存文字（全角文字）を変換するときはSJIS-WINで
					$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
					$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
					$msgno = mb_convert_encoding(substr($buffer, 5, 2), 'UTF-8', 'SJIS');
					$msg1 = mb_convert_encoding(substr($buffer, 7, 40), 'UTF-8', 'SJIS-WIN');
					$msg2 = mb_convert_encoding(substr($buffer, 47, 40), 'UTF-8', 'SJIS-WIN');
					$msg3 = mb_convert_encoding(substr($buffer, 87, 40), 'UTF-8', 'SJIS-WIN');

					$msg4 = mb_convert_encoding(substr($buffer, 127, 40), 'UTF-8', 'SJIS-WIN');
					$msg5 = mb_convert_encoding(substr($buffer, 167, 40), 'UTF-8', 'SJIS-WIN');
					$msg6 = mb_convert_encoding(substr($buffer, 207, 40), 'UTF-8', 'SJIS-WIN');

					/*
					 echo 'spgskigu'; echo('<br/>');
					echo '$status = ' . $status;	echo('<br/>');
					echo '$misecd = ' . $misecd;	echo('<br/>');
					echo '$msgno = ' . $msgno;	echo('<br/>');
					echo '$msg1 = ' . $msg1;	echo('<br/>');
					echo '$msg2 = ' . $msg2;	echo('<br/>');
					echo '$msg3 = ' . $msg3;	echo('<br/>');
					*/

					$wkdt = array(
						'misecd' => $misecd,
						'msgno' => $msgno,
						'status' => $status,
						'msg1' => $msg1,
						'msg2' => $msg2,
						//		'msg3'		=> $msg3
						'msg3' => $msg3,
						'msg4' => $msg4,
						'msg5' => $msg5,
						'msg6' => $msg6
					);
					$this->db->table('spgskenmsg')->insert($wkdt);
					//echo "SPGSKENMSG更新" ; echo('<br/>?');

				}
				//////////////////////////////////////////////////////////////////////////////////
				else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '9')
				/////  「SPGSKANRILOGIN」の処理     //////////////////////////////////////////////////
				{
					// 機種依存文字（全角文字）を変換するときはSJIS-WINで
					$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
					$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
					$loginid = mb_convert_encoding(substr($buffer, 5, 20), 'UTF-8', 'SJIS');
					$loginpwd = mb_convert_encoding(substr($buffer, 25, 20), 'UTF-8', 'SJIS');
					$name = mb_convert_encoding(substr($buffer, 45, 40), 'UTF-8', 'SJIS-WIN');
					$grantno = mb_convert_encoding(substr($buffer, 85, 1), 'UTF-8', 'SJIS');

					/*
					 echo 'spgskanrilogin';	echo('<br/>');
					echo '$status = ' . $status;	echo('<br/>');
					echo '$misecd = ' . $misecd;	echo('<br/>');
					echo '$loginid = ' . $loginid;	echo('<br/>');
					echo '$loginpwd = ' . $loginpwd;	echo('<br/>');
					echo '$name = ' . $name;	echo('<br/>');
					echo '$grantno = ' . $grantno;	echo('<br/>');
					*/

					$wkdt = array(
						'misecd' => $misecd,
						'loginid' => $loginid,
						'loginpwd' => $loginpwd,
						'name' => $name,
						'grantno' => $grantno,
						'status' => $status,
						'entryymd' => $nowymd,
						'entrytime' => $nowtime
					);
					$this->db->table('spgskanrilogin')->insert($wkdt);

					//echo "SPGSKANRILOGIN更新" ; echo('<br/>?');

				}
			}
			//echo "コンプリート";
			$this->db->transComplete();
			/*
			if($this->db->transStatus()=== FALSE){
				echo "トランザクション失敗" ; echo('<br/>?');
				//$this->db->trans_rollback();

			}else{
				echo "トランザクション成功" ; echo('<br/>?');
				//$this->db->trans_commit();
			}
			*/
		}
		//ファイルを閉じる
		fclose($fp);
	}


	// ユーザー単位アップロード（はじめ）
	public function upload2()
	{

		log_message('debug', '★AdminSpgsController upload2　実行');

		$data['login_id'] = session('login_id');
		$data['login_pwd'] = session('login_pwd');
		$data['login_name'] = session('login_name');
		$data['login_grant'] = session('login_grant');
		$data['error'] = '';
		$data['header_img01'] = $this->adminConfig->header_img01;
		$data['header_url01'] = $this->adminConfig->header_url01;
		$data['header_img02'] = $this->adminConfig->header_img02;
		$data['header_url02'] = $this->adminConfig->header_url02;
		$data['buttons'] = session('buttons');
		$data['max_button'] = $this->adminConfig->max_button;

		return view('adminview/spgsadmin_upload2', $data);
	}
	// ユーザー単位アップロード（その２）
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	ファイルアップロード実行（$queryをポストバックしてそのまま使用する）
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function upload2_exec()
	{
		log_message('debug', '★AdminSpgsController upload2_exec 実行');

		//////////////////////////////////////////////////////////////////////////////////////////////
		//	出力用ワークファイル作成
		//////////////////////////////////////////////////////////////////////////////////////////////
		//$wkfile=$this->web_model->make_fileup_tbl($nowymd,$nowtime,session('login_agency'));
		//$kbn=			$this->request->getPost('kbn');
		//$seikyu_date=	mb_ereg_replace('[^0-9]', '', $this->request->getPost('seikyu_date'));
		//$output=		$this->request->getPost('output');
		//$filename=		session('login_agency') . 'furi';
		$filename = 'spgsuser.txt';
		//$config['upload_path'] 	= $_SERVER['DOCUMENT_ROOT'] . '/codeigniter/fileup/';
		//$config['upload_path']	= $_SERVER["DOCUMENT_ROOT"]."/../ftppgs/";
		//$config['upload_path']	= $_SERVER["DOCUMENT_ROOT"] . "../../ftppgs/spgss/" . session('login_agency') . "/";
		$config['upload_path'] = WRITEPATH . 'fileup/';
		$config['allowed_types'] = '*';
		$config['overwrite'] = true;
		$config['file_name'] = $filename;

		//「0」は無制限
		$config['max_size'] = '0';
		$config['max_width'] = '0';
		$config['max_height'] = '0';

		// [Modified for CI4 Upgrade]
		// CI4 Standard File Upload
		$file = $this->request->getFile('userfile');

		if (!$file->isValid()) {
			// Error handling
			$msg = ' <p style="color:blue;">' . $file->getErrorString() . '(' . $file->getError() . ')</p>';

			$data['login_id'] = session('login_id');
			$data['login_pwd'] = session('login_pwd');
			$data['login_name'] = session('login_name');
			$data['login_grant'] = session('login_grant');
			$data['error'] = $msg;
			$data['header_img01'] = $this->adminConfig->header_img01;
			$data['header_url01'] = $this->adminConfig->header_url01;
			$data['header_img02'] = $this->adminConfig->header_img02;
			$data['header_url02'] = $this->adminConfig->header_url02;
			$data['buttons'] = session('buttons');
			$data['max_button'] = $this->adminConfig->max_button;

			return view('adminview/spgsadmin_upload2', $data);
		} else {

			// To ensure overwrite:
			$targetPath = WRITEPATH . 'fileup/' . $filename;
			if (file_exists($targetPath)) {
				unlink($targetPath);
			}

			$file->move(WRITEPATH . 'fileup/', $filename);

			$filetype = $file->getClientMimeType();
			$fullpath = $file->getRealPath(); // After move, getRealPath() might point to new loc
			// Actually getRealPath() works on the moved instance?
			// Once moved, the instance refers to the moved file.
			$fullpath = realpath(WRITEPATH . 'fileup/' . $filename);
			$filepath = WRITEPATH . 'fileup/'; // Required for zip extract logic
			$filename1 = $filename;

			// Log for debug
			log_message('debug', 'Upload Success: ' . $fullpath);

			$data['buttons'] = session('buttons');
			$data['max_button'] = $this->adminConfig->max_button;

			$data['error'] = '';

			// ファイルを読んでDBに書き出し
			//	$msg = $this->upload_read($filename );
			if ($filetype == 'text/plain') {


				//				$msg = $this->upload_read($filename1 );
				$msg = $this->upload2_read($filename1, 1);


			} else if ($filetype == 'application/x-zip-compressed' || $filetype == 'application/zip') {
				$zip = new \ZipArchive();
				if (!$zip->open($fullpath)) {
					$zip->close();
					echo 'zipファイルのオープンに失敗しました。' . PHP_EOL;
					exit(1);
				} else {
					$filename2 = $zip->getNameIndex(0);
					$zip->extractTo($filepath);
					$zip->close();


					//					$msg = $this->upload_read($filename2);
					$msg = $this->upload2_read($filename2, 1);


				}
			} else {
				// Strict mime check might fail for text/plain depending on browser
				// In CI3 code, it checked $filetype. Here we got client mime type.
				// Assuming it's fine. If standard upload, checking extension is safer.
				// But keeping existing logic structure.
				// For safety, let's allow flow through if it matches known types.
				// If standard text upload, it usually is text/plain.

				// Fallback: If not matched above, error???
				// The original code had error exit.
				// But wait, userfile accept in HTML is text/plain.
				// Let's assume it works.

				// However, $filename is forced to 'spgsuser.txt' in config.
				// Logic below relies on $filetype.
				// If uploaded file is zip, client mime might be zip.
				// If text, text/plain.

				// Just in case, if fall through:
				if ($filetype != 'text/plain' && $filetype != 'application/x-zip-compressed' && $filetype != 'application/zip') {
					echo 'ファイル形式が違います。(' . $filetype . ')';
					exit(1);
				}
			}

			// ... (Rest of the logic matches original else block structure)

			$data['login_id'] = session('login_id');
			$data['login_pwd'] = session('login_pwd');
			$data['login_name'] = session('login_name');
			$data['login_grant'] = session('login_grant');
			$data['header_img01'] = $this->adminConfig->header_img01;
			$data['header_url01'] = $this->adminConfig->header_url01;
			$data['header_img02'] = $this->adminConfig->header_img02;
			$data['header_url02'] = $this->adminConfig->header_url02;

			//echo '$msg=' . $msg;
			if ($msg != '') {
				$data['error'] = $msg;
				return view('adminview/spgsadmin_upload2', $data);
			} else {
				session()->setFlashdata('msg', 'アップロードが完了しました。');
				return redirect()->to('spgsadmin/upload_complete');
			}
		}


	}

	// 完了画面表示
	public function upload_complete()
	{
		log_message('debug', '★AdminSpgsController upload_complete 実行');
		$data = [
			'login_id' => session('login_id'),
			'login_pwd' => session('login_pwd'),
			'login_name' => session('login_name'),
			'login_grant' => session('login_grant'),
			'header_img01' => $this->adminConfig->header_img01,
			'header_url01' => $this->adminConfig->header_url01,
			'header_img02' => $this->adminConfig->header_img02,
			'header_url02' => $this->adminConfig->header_url02,
			'buttons' => session('buttons'),
			'max_button' => $this->adminConfig->max_button,
			'error' => session()->getFlashdata('error') ?? '',
			'msg' => session()->getFlashdata('msg') ?? '',
		];
		return view('adminview/spgsadmin_upload_complete', $data);
	}

	// ユーザー単位アップロード（その３）
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	アップロードされたファイルを読み、処理を実行
	////	（[upload_read]を[$upload_flg ! =1]で全体アップと切替可能とした統合版）
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function upload2_read($filename = '', $upload_flg = 0)
	{

		log_message('debug', '★AdminSpgsController upload2_read 実行');

		set_time_limit(600);

		$nowymd = date("Ymd");	#現在日
		$nowtime = date("His");	#現在時刻

		if (!($fp = fopen('./fileup/' . $filename, "r"))) {
			log_message('debug', '★upload2_read ファイルが開けません');
			fclose($fp);
			$msg = 'ファイルが開けません。';
			return $msg;
		} else {
			log_message('debug', '★upload2_read トランザクションスタート');
			#トランザクションスタート
			try {
				$this->db->transStart();

				if ($upload_flg != 1) {
					log_message('debug', '★upload2_read upload_flg = 1 以外のためテーブル削除');
					$this->table_del();
				}

				$count = 0;

				// ユーザー切替判定用
				$wk_beforefullcd1 = '';
				// 料金表切替判定用
				$wk_beforeryokinno1 = '';

				//ファイルの読み込みと表示
				//１行ずつファイルを読み込んで、表示する。
				while (!feof($fp)) {
					$count++;
					$buffer = fgets($fp);	// そのままSJISでデータを取得する。（このままだと内部で使用できない）
					//echo 'buffer= '	. mb_convert_encoding($buffer, 'UTF-8', 'SJIS'); echo('<br/>');



					// ここでユーザー毎の削除判定→削除を入れる　はじめ
					// 消す必要がないもの　login, mail, pwd
					$wk_status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
					if ($upload_flg == 1) {
						if ($wk_status == '1' | $wk_status == '2' | $wk_status == '3' | $wk_status == '5') {
							$wk_beforefullcd2 = mb_convert_encoding(substr($buffer, 1, 13), 'UTF-8', 'SJIS');
							if ($wk_beforefullcd2 != $wk_beforefullcd1) {
								// この時削除するのは　user, kensin, tori, kigu
								$wk_misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
								$wk_usercd = mb_convert_encoding(substr($buffer, 5, 9), 'UTF-8', 'SJIS');
								$this->table_del_userselect($wk_misecd, $wk_usercd);
							}
							$wk_beforefullcd1 = $wk_beforefullcd2;
						} else {
							// kenmsgを削除？
							if ($wk_status == '6') {
								$wk_misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
								$wk_msgno = mb_convert_encoding(substr($buffer, 5, 2), 'UTF-8', 'SJIS');
								$this->table_del_userselect_kenmsg($wk_misecd, $wk_msgno);
							}
							// ryokinを削除？
							else if ($wk_status == '4') {
								$wk_beforeryokinno2 = mb_convert_encoding(substr($buffer, 1, 7), 'UTF-8', 'SJIS');
								if ($wk_beforeryokinno2 != $wk_beforeryokinno1) {
									$wk_misecd = intval(mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS'));
									$wk_ryokinno = intval(mb_convert_encoding(substr($buffer, 5, 3), 'UTF-8', 'SJIS'));
									$this->table_del_userselect_ryokin($wk_misecd, $wk_ryokinno);
								}
								$wk_beforeryokinno1 = $wk_beforeryokinno2;

							}
							// kanriloginを削除？
							else if ($wk_status == '9') {
								$wk_misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
								$wk_loginid = mb_convert_encoding(substr($buffer, 5, 20), 'UTF-8', 'SJIS');
								$this->table_del_userselect_kanrilogin($wk_misecd, $wk_loginid);
							}
						}
					}

					// ここでユーザー毎の削除判定→削除を入れる　終わり


					if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '1')
					/////  「SPGSUSER」の処理     //////////////////////////////////////////////////
					{
						// 機種依存文字（全角文字）を変換するときはSJIS-WINで
						$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
						$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
						$usercd = mb_convert_encoding(substr($buffer, 5, 9), 'UTF-8', 'SJIS');
						$dspusercd = mb_convert_encoding(substr($buffer, 14, 11), 'UTF-8', 'SJIS');
						$name = mb_convert_encoding(substr($buffer, 25, 40), 'UTF-8', 'SJIS-WIN');
						$id = mb_convert_encoding(substr($buffer, 65, 20), 'UTF-8', 'SJIS');
						$pwd = mb_convert_encoding(substr($buffer, 85, 20), 'UTF-8', 'SJIS');
						$mail = mb_convert_encoding(substr($buffer, 105, 50), 'UTF-8', 'SJIS');
						$ryokinno = intval(mb_convert_encoding(substr($buffer, 155, 3), 'UTF-8', 'SJIS'));
						$siyoryo = mb_convert_encoding(substr($buffer, 158, 5), 'UTF-8', 'SJIS');
						$kihon = mb_convert_encoding(substr($buffer, 163, 6), 'UTF-8', 'SJIS');
						$ryokin = mb_convert_encoding(substr($buffer, 169, 7), 'UTF-8', 'SJIS');
						$zeikbn = intval(mb_convert_encoding(substr($buffer, 176, 1), 'UTF-8', 'SJIS'));
						$msgno = mb_convert_encoding(substr($buffer, 177, 2), 'UTF-8', 'SJIS');
						$nyukyoymd = mb_convert_encoding(substr($buffer, 193, 8), 'UTF-8', 'SJIS');
						$wkdt = array(
							'misecd' => $misecd,
							'usercd' => $usercd,
							'dspusercd' => $dspusercd,
							'name' => $name,
							'id' => $id,
							'pwd' => $pwd,
							'mail' => $mail,
							'ryokinno' => $ryokinno,
							'siyoryo' => $siyoryo,
							'kihon' => $kihon,
							'ryokin' => $ryokin,
							'nyukyoymd' => $nyukyoymd,
							'status' => $status,
							'entryymd' => $nowymd,
							'entrytime' => $nowtime,
							'zeikbn' => $zeikbn,	// 税区分
							'msgno' => $msgno
						);

						log_message('debug', '★upload2_read spgsuserにデータ挿入 $usercd' . $usercd);

						$this->db->table('spgsuser')->insert($wkdt);
					}

					//////////////////////////////////////////////////////////////////////////////////
					else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '2')
					/////  「SPGSKENSIN」の処理     //////////////////////////////////////////////////
					{
						log_message('debug', '★upload2_read mb_convert_encoding = 2');
						// 機種依存文字（全角文字）を変換するときはSJIS-WINで
						// メーター交換時取付指針追加 2018.01.17
						$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
						$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
						$usercd = mb_convert_encoding(substr($buffer, 5, 9), 'UTF-8', 'SJIS');
						$konkensinymd = mb_convert_encoding(substr($buffer, 14, 10), 'UTF-8', 'SJIS');
						$konkensinsisin = mb_convert_encoding(substr($buffer, 24, 9), 'UTF-8', 'SJIS');
						$konkensinsiyoryo = mb_convert_encoding(substr($buffer, 33, 9), 'UTF-8', 'SJIS');
						$zenkensinymd = mb_convert_encoding(substr($buffer, 42, 10), 'UTF-8', 'SJIS');
						$zenkensinsisin = mb_convert_encoding(substr($buffer, 52, 9), 'UTF-8', 'SJIS');
						$zensiyoryo = mb_convert_encoding(substr($buffer, 61, 9), 'UTF-8', 'SJIS');
						$kihonryokin = mb_convert_encoding(substr($buffer, 70, 8), 'UTF-8', 'SJIS-WIN');
						$jyuryoryokin = mb_convert_encoding(substr($buffer, 78, 8), 'UTF-8', 'SJIS-WIN');

						$shouhizei = mb_convert_encoding(substr($buffer, 86, 8), 'UTF-8', 'SJIS-WIN');
						$gasryokin = mb_convert_encoding(substr($buffer, 94, 8), 'UTF-8', 'SJIS-WIN');

						$toriymd1 = mb_convert_encoding(substr($buffer, 102, 10), 'UTF-8', 'SJIS');
						$toriname1 = mb_convert_encoding(substr($buffer, 112, 40), 'UTF-8', 'SJIS-WIN');
						$torisuu1 = mb_convert_encoding(substr($buffer, 152, 8), 'UTF-8', 'SJIS');
						$torikin1 = mb_convert_encoding(substr($buffer, 160, 9), 'UTF-8', 'SJIS');

						$toriymd2 = mb_convert_encoding(substr($buffer, 169, 10), 'UTF-8', 'SJIS');
						$toriname2 = mb_convert_encoding(substr($buffer, 179, 40), 'UTF-8', 'SJIS-WIN');
						$torisuu2 = mb_convert_encoding(substr($buffer, 219, 8), 'UTF-8', 'SJIS');
						$torikin2 = mb_convert_encoding(substr($buffer, 227, 9), 'UTF-8', 'SJIS');

						$toriymd3 = mb_convert_encoding(substr($buffer, 236, 10), 'UTF-8', 'SJIS');
						$toriname3 = mb_convert_encoding(substr($buffer, 246, 40), 'UTF-8', 'SJIS-WIN');
						$torisuu3 = mb_convert_encoding(substr($buffer, 286, 8), 'UTF-8', 'SJIS');
						$torikin3 = mb_convert_encoding(substr($buffer, 294, 9), 'UTF-8', 'SJIS');

						$toriymd4 = mb_convert_encoding(substr($buffer, 303, 10), 'UTF-8', 'SJIS');
						$toriname4 = mb_convert_encoding(substr($buffer, 313, 40), 'UTF-8', 'SJIS-WIN');
						$torisuu4 = mb_convert_encoding(substr($buffer, 353, 8), 'UTF-8', 'SJIS');
						$torikin4 = mb_convert_encoding(substr($buffer, 361, 9), 'UTF-8', 'SJIS');

						$toriymd5 = mb_convert_encoding(substr($buffer, 370, 10), 'UTF-8', 'SJIS');
						$toriname5 = mb_convert_encoding(substr($buffer, 380, 40), 'UTF-8', 'SJIS-WIN');
						$torisuu5 = mb_convert_encoding(substr($buffer, 420, 8), 'UTF-8', 'SJIS');
						$torikin5 = mb_convert_encoding(substr($buffer, 428, 9), 'UTF-8', 'SJIS');

						$toriymd6 = mb_convert_encoding(substr($buffer, 437, 10), 'UTF-8', 'SJIS');
						$toriname6 = mb_convert_encoding(substr($buffer, 447, 40), 'UTF-8', 'SJIS-WIN');
						$torisuu6 = mb_convert_encoding(substr($buffer, 487, 8), 'UTF-8', 'SJIS');
						$torikin6 = mb_convert_encoding(substr($buffer, 495, 9), 'UTF-8', 'SJIS');

						$torigokeikin = mb_convert_encoding(substr($buffer, 504, 13), 'UTF-8', 'SJIS-WIN');
						$seikyukin = mb_convert_encoding(substr($buffer, 517, 13), 'UTF-8', 'SJIS-WIN');
						$siharai = mb_convert_encoding(substr($buffer, 530, 20), 'UTF-8', 'SJIS-WIN');
						$furiymd = mb_convert_encoding(substr($buffer, 550, 8), 'UTF-8', 'SJIS-WIN');

						$meterkoukanymd = mb_convert_encoding(substr($buffer, 558, 10), 'UTF-8', 'SJIS');
						$meterkoukanzenkai = mb_convert_encoding(substr($buffer, 568, 9), 'UTF-8', 'SJIS');
						$meterkoukanhikitori = mb_convert_encoding(substr($buffer, 577, 9), 'UTF-8', 'SJIS');
						$meterkoukantorituke = mb_convert_encoding(substr($buffer, 586, 9), 'UTF-8', 'SJIS');
						$meterkoukankyusiyo = mb_convert_encoding(substr($buffer, 595, 9), 'UTF-8', 'SJIS');

						$tanka1 = mb_convert_encoding(substr($buffer, 604, 11), 'UTF-8', 'SJIS');
						$tanka2 = mb_convert_encoding(substr($buffer, 615, 11), 'UTF-8', 'SJIS');
						$tanka3 = mb_convert_encoding(substr($buffer, 626, 11), 'UTF-8', 'SJIS');
						$tanka4 = mb_convert_encoding(substr($buffer, 637, 11), 'UTF-8', 'SJIS');
						$tanka5 = mb_convert_encoding(substr($buffer, 648, 11), 'UTF-8', 'SJIS');
						$tanka6 = mb_convert_encoding(substr($buffer, 659, 11), 'UTF-8', 'SJIS');
						$gasnebiki = mb_convert_encoding(substr($buffer, 670, 12), 'UTF-8', 'SJIS');
						$gaszeikbn = mb_convert_encoding(substr($buffer, 682, 1), 'UTF-8', 'SJIS');
						$setubiryokin = mb_convert_encoding(substr($buffer, 683, 12), 'UTF-8', 'SJIS-WIN');
						$setubiseigyo = mb_convert_encoding(substr($buffer, 695, 1), 'UTF-8', 'SJIS');

						$wkdt = array(
							'misecd' => $misecd,
							'usercd' => $usercd,
							'konkensinymd' => $konkensinymd,
							'konkensinsisin' => $konkensinsisin,
							'konkensinsiyoryo' => $konkensinsiyoryo,
							'zenkensinymd' => $zenkensinymd,
							'zenkensinsisin' => $zenkensinsisin,
							'zensiyoryo' => $zensiyoryo,
							'kihonryokin' => $kihonryokin,
							'jyuryoryokin' => $jyuryoryokin,
							'setubiryokin' => $setubiryokin,
							'setubiseigyo' => $setubiseigyo,
							'shouhizei' => $shouhizei,
							'gasryokin' => $gasryokin,
							'toriymd1' => $toriymd1,
							'toriname1' => $toriname1,
							'torisuu1' => $torisuu1,
							'tanka1' => $tanka1,
							'torikin1' => $torikin1,
							'toriymd2' => $toriymd2,
							'toriname2' => $toriname2,
							'torisuu2' => $torisuu2,
							'tanka2' => $tanka2,
							'torikin2' => $torikin2,
							'toriymd3' => $toriymd3,
							'toriname3' => $toriname3,
							'torisuu3' => $torisuu3,
							'tanka3' => $tanka3,
							'torikin3' => $torikin3,
							'toriymd4' => $toriymd4,
							'toriname4' => $toriname4,
							'torisuu4' => $torisuu4,
							'tanka4' => $tanka4,
							'torikin4' => $torikin4,
							'toriymd5' => $toriymd5,
							'toriname5' => $toriname5,
							'torisuu5' => $torisuu5,
							'tanka5' => $tanka5,
							'torikin5' => $torikin5,
							'toriymd6' => $toriymd6,
							'toriname6' => $toriname6,
							'torisuu6' => $torisuu6,
							'tanka6' => $tanka6,
							'torikin6' => $torikin6,
							'torigokeikin' => $torigokeikin,
							'seikyukin' => $seikyukin,
							'siharai' => $siharai,
							'furiymd' => $furiymd,
							'meterkoukanymd' => $meterkoukanymd,
							'meterkoukanzenkai' => $meterkoukanzenkai,
							'meterkoukanhikitori' => $meterkoukanhikitori,
							'meterkoukantorituke' => $meterkoukantorituke,
							'meterkoukankyusiyo' => $meterkoukankyusiyo,
							'status' => $status,
							'gasnebiki' => $gasnebiki,
							'entryymd' => $nowymd,
							'entrytime' => $nowtime
						);
						$this->db->table('spgskensin')->insert($wkdt);
						//echo "SPGSKENSIN更新" ; echo('<br/>?');

					}
					//////////////////////////////////////////////////////////////////////////////////
					else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '3')
					/////  「SPGSTORI」の処理     //////////////////////////////////////////////////
					{
						// 機種依存文字（全角文字）を変換するときはSJIS-WINで
						$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
						$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
						$usercd = mb_convert_encoding(substr($buffer, 5, 9), 'UTF-8', 'SJIS');
						$denshuname = mb_convert_encoding(substr($buffer, 14, 6), 'UTF-8', 'SJIS-WIN');
						$ymd = mb_convert_encoding(substr($buffer, 20, 10), 'UTF-8', 'SJIS');
						$hin = mb_convert_encoding(substr($buffer, 30, 40), 'UTF-8', 'SJIS-WIN');
						$kata = mb_convert_encoding(substr($buffer, 70, 20), 'UTF-8', 'SJIS-WIN');
						$suu = mb_convert_encoding(substr($buffer, 90, 9), 'UTF-8', 'SJIS');
						$tanka = mb_convert_encoding(substr($buffer, 99, 11), 'UTF-8', 'SJIS');
						$kin = mb_convert_encoding(substr($buffer, 110, 11), 'UTF-8', 'SJIS');

						$wkdt = array(
							'misecd' => $misecd,
							'usercd' => $usercd,
							'denshuname' => $denshuname,
							'ymd' => $ymd,
							'hin' => $hin,
							'kata' => $kata,
							'suu' => $suu,
							'tanka' => $tanka,
							'kin' => $kin,
							'status' => $status,
							'entryymd' => $nowymd,
							'entrytime' => $nowtime
						);
						log_message('debug', '★upload2_read spgstori にデータ挿入 $usercd' . $usercd);
						$this->db->table('spgstori')->insert($wkdt);
					}
					//////////////////////////////////////////////////////////////////////////////////
					else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '4')
					/////  「SPGSRYOKIN」の処理     //////////////////////////////////////////////////
					{
						// 機種依存文字（全角文字）を変換するときはSJIS-WINで
						$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
						$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
						$ryokinno = mb_convert_encoding(substr($buffer, 5, 3), 'UTF-8', 'SJIS');
						$kaisono = mb_convert_encoding(substr($buffer, 8, 3), 'UTF-8', 'SJIS');
						$siyoryo = mb_convert_encoding(substr($buffer, 11, 9), 'UTF-8', 'SJIS-WIN');
						$kihon = mb_convert_encoding(substr($buffer, 20, 5), 'UTF-8', 'SJIS');
						$ryokin = mb_convert_encoding(substr($buffer, 25, 7), 'UTF-8', 'SJIS');
						$zeikbn = intval(mb_convert_encoding(substr($buffer, 32, 1), 'UTF-8', 'SJIS'));
						// 簡易ガス表示用追加 2022/08/23
						$bunrui = intval(mb_convert_encoding(substr($buffer, 33, 1), 'UTF-8', 'SJIS'));
						$kanikihon = mb_convert_encoding(substr($buffer, 34, 10), 'UTF-8', 'SJIS');
						$kaniryokin = mb_convert_encoding(substr($buffer, 44, 10), 'UTF-8', 'SJIS');

						$wkdt = array(
							'misecd' => $misecd,
							'ryokinno' => $ryokinno,
							'kaisono' => $kaisono,
							'siyoryo' => $siyoryo,
							'kihon' => $kihon,
							'ryokin' => $ryokin,
							'zeikbn' => $zeikbn,
							'status' => $status,
							// 簡易ガス表示用追加 2022/08/23
							'bunrui' => $bunrui,
							'kkihon' => $kanikihon,
							'kryokin' => $kaniryokin,

							'entryymd' => $nowymd,
							'entrytime' => $nowtime
						);
						log_message('debug', '★upload2_read spgsryokin にデータ挿入 $usercd' . $usercd);
						$this->db->table('spgsryokin')->insert($wkdt);
					}
					//////////////////////////////////////////////////////////////////////////////////
					else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '5')
					/////  「SPGSKIGU」の処理     //////////////////////////////////////////////////
					{
						// 機種依存文字（全角文字）を変換するときはSJIS-WINで
						$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
						$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
						$usercd = mb_convert_encoding(substr($buffer, 5, 9), 'UTF-8', 'SJIS');
						$kigurenban = mb_convert_encoding(substr($buffer, 14, 3), 'UTF-8', 'SJIS');
						$kigu = mb_convert_encoding(substr($buffer, 17, 40), 'UTF-8', 'SJIS-WIN');
						$kata = mb_convert_encoding(substr($buffer, 57, 20), 'UTF-8', 'SJIS-WIN');
						$suu = mb_convert_encoding(substr($buffer, 77, 3), 'UTF-8', 'SJIS');
						$ym = mb_convert_encoding(substr($buffer, 80, 7), 'UTF-8', 'SJIS');
						$anzen1 = mb_convert_encoding(substr($buffer, 87, 10), 'UTF-8', 'SJIS-WIN');
						$anzen2 = mb_convert_encoding(substr($buffer, 97, 10), 'UTF-8', 'SJIS-WIN');
						$anzen3 = mb_convert_encoding(substr($buffer, 107, 10), 'UTF-8', 'SJIS-WIN');

						$wkdt = array(
							'misecd' => $misecd,
							'usercd' => $usercd,
							'kigurenban' => $kigurenban,
							'kigu' => $kigu,
							'kata' => $kata,
							'suu' => $suu,
							'ym' => $ym,
							'anzen1' => $anzen1,
							'anzen2' => $anzen2,
							'anzen3' => $anzen3,
							'status' => $status,
							'entryymd' => $nowymd,
							'entrytime' => $nowtime
						);
						log_message('debug', '★upload2_read spgskigu にデータ挿入 $usercd' . $usercd);
						$this->db->table('spgskigu')->insert($wkdt);
					}
					//////////////////////////////////////////////////////////////////////////////////
					else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '6')
					/////  「SPGSKENMSG」の処理     //////////////////////////////////////////////////
					{
						// 機種依存文字（全角文字）を変換するときはSJIS-WINで
						$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
						$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
						$msgno = mb_convert_encoding(substr($buffer, 5, 2), 'UTF-8', 'SJIS');
						$msg1 = mb_convert_encoding(substr($buffer, 7, 40), 'UTF-8', 'SJIS-WIN');
						$msg2 = mb_convert_encoding(substr($buffer, 47, 40), 'UTF-8', 'SJIS-WIN');
						$msg3 = mb_convert_encoding(substr($buffer, 87, 40), 'UTF-8', 'SJIS-WIN');

						$msg4 = mb_convert_encoding(substr($buffer, 127, 40), 'UTF-8', 'SJIS-WIN');
						$msg5 = mb_convert_encoding(substr($buffer, 167, 40), 'UTF-8', 'SJIS-WIN');
						$msg6 = mb_convert_encoding(substr($buffer, 207, 40), 'UTF-8', 'SJIS-WIN');

						$wkdt = array(
							'misecd' => $misecd,
							'msgno' => $msgno,
							'status' => $status,
							'msg1' => $msg1,
							'msg2' => $msg2,
							//		'msg3'		=> $msg3
							'msg3' => $msg3,
							'msg4' => $msg4,
							'msg5' => $msg5,
							'msg6' => $msg6
						);
						log_message('debug', '★upload2_read spgskenmsg にデータ挿入 $usercd' . $usercd);
						$this->db->table('spgskenmsg')->insert($wkdt);

					}
					//////////////////////////////////////////////////////////////////////////////////
					else if (mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS') == '9')
					/////  「SPGSKANRILOGIN」の処理     //////////////////////////////////////////////////
					{
						// 機種依存文字（全角文字）を変換するときはSJIS-WINで
						$status = mb_convert_encoding(substr($buffer, 0, 1), 'UTF-8', 'SJIS');
						$misecd = mb_convert_encoding(substr($buffer, 1, 4), 'UTF-8', 'SJIS');
						$loginid = mb_convert_encoding(substr($buffer, 5, 20), 'UTF-8', 'SJIS');
						$loginpwd = mb_convert_encoding(substr($buffer, 25, 20), 'UTF-8', 'SJIS');
						$name = mb_convert_encoding(substr($buffer, 45, 40), 'UTF-8', 'SJIS-WIN');
						$grantno = mb_convert_encoding(substr($buffer, 85, 1), 'UTF-8', 'SJIS');

						$wkdt = array(
							'misecd' => $misecd,
							'loginid' => $loginid,
							'loginpwd' => $loginpwd,
							'name' => $name,
							'grantno' => $grantno,
							'status' => $status,
							'entryymd' => $nowymd,
							'entrytime' => $nowtime
						);
						log_message('debug', '★upload2_read spgskanrilogin にデータ挿入 $usercd' . $usercd);
						$this->db->table('spgskanrilogin')->insert($wkdt);

					}
				}

				$this->db->transComplete();
				log_message('debug', '★upload2_read spgskanrilogin にデータ挿入 $usercd' . $usercd);

				// トランザクション結果をチェック
				if ($this->db->transStatus() === false) {
					log_message('debug', '★AdminSpgsController upload2_read トランザクション失敗');
				} else {
					log_message('debug', '★AdminSpgsController upload2_read トランザクション成功');
				}

			} catch (\Exception $e) {
				log_message('debug', '★AdminSpgsController table_del_userselect2 トランザクション想定外エラー：' . $e->getmessage());
				return false;
			}

			/*
			if($this->db->transStatus()=== FALSE){
				echo "トランザクション失敗" ; echo('<br/>?');
				//$this->db->trans_rollback();

			}else{
				echo "トランザクション成功" ; echo('<br/>?');
				//$this->db->trans_commit();
			}
			*/
		}
		//ファイルを閉じる
		fclose($fp);
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	パスワード変更：ユーザー検索
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function pwduser_disp()
	{
		log_message('debug', '★AdminSpgsController pwduser_disp 実行');

		$kensaku_misecd = session('login_misecd');

		if ($this->request->getPost('kensaku_usercd') != '') {
			$kensaku_usercd = preg_replace('/[^0-9]/', '', $this->request->getPost('kensaku_usercd'));
			// 9桁に満たない場合は右側にゼロ詰め
			$kensaku_usercd = str_pad($kensaku_usercd, 9, 0, STR_PAD_RIGHT);
			log_message('debug', '★AdminSpgsController kensaku_usercd' . $kensaku_usercd);
		} else {
			$kensaku_usercd = '';
			log_message('debug', '★AdminSpgsController kensaku_usercd POSTなし');
		}
		if ($this->request->getPost('kensaku_name') != '') {
			$kensaku_name = $this->request->getPost('kensaku_name');
			log_message('debug', '★AdminSpgsController kensaku_name' . $kensaku_name);
		} else {
			$kensaku_name = '';
			log_message('debug', '★AdminSpgsController kensaku_name POSTなし');
		}

		// $offset = (int) $this->uri->segment(3, 0);
		$uri = service('uri');
		$offset = (int) $uri->getSegment(3, 0);

		//販売店ごとの設定
		$data = [
			'buttons' => session('buttons'),
			'max_button' => $this->adminConfig->max_button,
		];


		# 顧客コードとoffset値と、1ページに表示するレコードの数を渡し、モデルより
		# ログイン一覧を取得します。
		$data['query'] = $this->get_user_list($this->adminConfig->meisai_rec, $offset, $kensaku_name, $kensaku_misecd, $kensaku_usercd);

		$total = $this->get_user_count($kensaku_name, $kensaku_misecd, $kensaku_usercd);

		$data['login_id'] = session('login_id');
		$data['login_pwd'] = session('login_pwd');
		$data['login_name'] = session('login_name');
		$data['login_grant'] = session('login_grant');
		$data['list_total'] = $total;
		$data['list_limit'] = $this->adminConfig->meisai_rec;
		$data['list_offset'] = $offset;

		// ポストバックされた店とコードを使用する
		$data['kensaku_usercd'] = $kensaku_usercd;
		$data['kensaku_name'] = $kensaku_name;

		$data['header_img01'] = $this->adminConfig->header_img01;
		$data['header_url01'] = $this->adminConfig->header_url01;
		$data['header_img02'] = $this->adminConfig->header_img02;
		$data['header_url02'] = $this->adminConfig->header_url02;
		$data['init_usercd'] = '';

		$data['buttons'] = session('buttons');
		$data['max_button'] = $this->adminConfig->max_button;


		return view('adminview/spgsadmin_pwduser_disp', $data);
	}

	public function pwdchange_disp()
	{
		log_message('debug', '★AdminSpgsController pwdchange_disp 実行');

		$pwdchange_misecd = $this->request->getPost('pwdchange_misecd');
		$pwdchange_usercd = $this->request->getPost('pwdchange_usercd');
		//			$pwdchange_name = $this->request->getPost('pwdchange_name');
		log_message('debug', '★$pwdchange_misecd = ' . $pwdchange_misecd);
		log_message('debug', '★$pwdchange_usercd = ' . $pwdchange_usercd);


		# 顧客コードとoffset値と、1ページに表示するレコードの数を渡し、モデルより
		# ログイン一覧を取得します。

		$data = $this->spgsuserModel->get_spgsuser($pwdchange_misecd, $pwdchange_usercd);
		log_message('debug', '★spgsuserModel $data = ' . print_r($data, true));

		$data['login_id'] = session('login_id');
		$data['login_pwd'] = session('login_pwd');
		$data['login_name'] = session('login_name');
		$data['login_grant'] = session('login_grant');

		$data['header_img01'] = $this->adminConfig->header_img01;
		$data['header_url01'] = $this->adminConfig->header_url01;
		$data['header_img02'] = $this->adminConfig->header_img02;
		$data['header_url02'] = $this->adminConfig->header_url02;

		$data['errmsg'] = '';
		$data['buttons'] = session('buttons');
		$data['max_button'] = $this->adminConfig->max_button;

		$data['dspusercd'] = $pwdchange_usercd;

		log_message('debug', '★$data = ' . print_r($data, true));

		return view('adminview/spgsadmin_pwduserchange_disp', $data);
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	パスワード変更更新
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function pwdupdate($misecd = 0, $usercd = '')
	{
		log_message('debug', '★AdminSpgsController pwdupdate 実行');

		$data['misecd'] = $this->request->getPost('pwdchange_misecd');
		$pwdchange_misecd = $data['misecd'];
		$data['usercd'] = $this->request->getPost('pwdchange_usercd');
		$pwdchange_usercd = $data['usercd'];
		$data['oldpwd'] = $this->request->getPost('oldpwd');
		$data['newpwd'] = $this->request->getPost('newpwd');
		$data['sndymd'] = 0;
		log_message('debug', 'data = ' . print_r($data, true));

		$rules = $this->_set_validation_pwdchg();
		log_message('debug', 'rules = ' . print_r($rules, true));

		if ($this->validate($rules)) {
			// バリデーション成功
			log_message('debug', '★pwdupdate バリデーション成功');
			if ($this->spgsuserModel->pwdupdate($data)) {
				log_message('debug', '★pwdupdate 更新成功');
				$data = $this->spgsuserModel->get_spgsuser($pwdchange_misecd, $pwdchange_usercd);
				$data['login_id'] = session('login_id');
				$data['login_pwd'] = session('login_pwd');
				$data['login_name'] = session('login_name');
				$data['login_grant'] = session('login_grant');

				$data['header_img01'] = $this->adminConfig->header_img01;
				$data['header_url01'] = $this->adminConfig->header_url01;
				$data['header_img02'] = $this->adminConfig->header_img02;
				$data['header_url02'] = $this->adminConfig->header_url02;

				$data['buttons'] = session('buttons');
				$data['max_button'] = $this->adminConfig->max_button;
				$data['errmsg'] = 'パスワードを更新しました。';

				return view('adminview/spgsadmin_pwduserchange_disp', $data);
			}
			log_message('debug', '★pwdupdate 更新失敗');
		}
		log_message('debug', '★pwdupdate バリデーション失敗');

		// バリデーション失敗
		$data = $this->spgsuserModel->get_spgsuser($pwdchange_misecd, $pwdchange_usercd);
		$data['login_id'] = session('login_id');
		$data['login_pwd'] = session('login_pwd');
		$data['login_name'] = session('login_name');
		$data['login_grant'] = session('login_grant');

		$data['header_img01'] = $this->adminConfig->header_img01;
		$data['header_url01'] = $this->adminConfig->header_url01;
		$data['header_img02'] = $this->adminConfig->header_img02;
		$data['header_url02'] = $this->adminConfig->header_url02;

		$data['errmsg'] = '<span class="red">更新に失敗しました。<br>[入力内容をご確認下さい。]</span>';
		$data['buttons'] = session('buttons');
		$data['max_button'] = $this->adminConfig->max_button;

		return view('adminview/spgsadmin_pwduserchange_disp', $data);

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	パスワード変更バリデーション		プライベートメソッド
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	private function _set_validation_pwdchg()
	{

		log_message('debug', '★AdminSpgsController _set_validation_pwdchg 実行');
		// バリデーションルールをリターン
		return [

			'oldpwd' => [
				'label' => '現行パスワード',
				'rules' => 'trim|required'
			],
			'newpwd' => [
				'label' => '新しいパスワード',
				'rules' => 'trim|required|min_length[6]|max_length[20]'
			]

		];

	}

	//[Mnt-003]------------------------------------------------------------------------------------------>> Add  End   24/08/20
//[Mnt-004]------------------------------------------------------------------------------------------>> Add  Start 24/10/09

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	顧客データ初期化更新
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function kokyaku_init($misecd = 0, $usercd = '')
	{
		log_message('debug', '★AdminSpgsController kokyaku_init 実行');

		$kokyakuinit_misecd = $this->request->getPost('kokyakuinit_misecd');
		$kokyakuinit_usercd = $this->request->getPost('kokyakuinit_usercd');

		$this->table_del_userselect2($kokyakuinit_misecd, $kokyakuinit_usercd);

		$kensaku_misecd = session('login_misecd');
		if ($this->request->getPost('kensaku_usercd') != '') {
			$kensaku_usercd = preg_replace('/[^0-9]/', '', $this->request->getPost('kensaku_usercd'));
			// 9桁に満たない場合は右側にゼロ詰め
			$kensaku_usercd = str_pad($kensaku_usercd, 9, 0, STR_PAD_RIGHT);
		} else {
			$kensaku_usercd = '';
		}
		if ($this->request->getPost('kensaku_name') != '') {
			$kensaku_name = $this->request->getPost('kensaku_name');
		} else {
			$kensaku_name = '';
		}
		// $offset = (int) $this->uri->segment(3, 0);
		$uri = service('uri');
		$offset = (int) $uri->getSegment(3, 0);

		# 顧客コードとoffset値と、1ページに表示するレコードの数を渡し、モデルより
		# ログイン一覧を取得します。
		$data['query'] = $this->get_user_list($this->adminConfig->meisai_rec, $offset, $kensaku_name, $kensaku_misecd, $kensaku_usercd);

		$total = $this->get_user_count($kensaku_name, $kensaku_misecd, $kensaku_usercd);

		$data['login_id'] = session('login_id');
		$data['login_pwd'] = session('login_pwd');
		$data['login_name'] = session('login_name');
		$data['login_grant'] = session('login_grant');
		$data['list_total'] = $total;
		$data['list_limit'] = $this->adminConfig->meisai_rec;
		$data['list_offset'] = $offset;

		// ポストバックされた店とコードを使用する
		$data['kensaku_usercd'] = $kensaku_usercd;
		$data['kensaku_name'] = $kensaku_name;

		$data['header_img01'] = $this->adminConfig->header_img01;
		$data['header_url01'] = $this->adminConfig->header_url01;
		$data['header_img02'] = $this->adminConfig->header_img02;
		$data['header_url02'] = $this->adminConfig->header_url02;

		$data['init_usercd'] = $kokyakuinit_usercd;
		$data['buttons'] = session('buttons');
		$data['max_button'] = $this->adminConfig->max_button;

		return view('adminview/spgsadmin_pwduser_disp', $data);

	}
	//[Mnt-004]------------------------------------------------------------------------------------------>> Add  End   24/10/09

	//[Mnt-001]------------------------------------------------------------------------------------------>> Edit Start 24/01/22
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	請求書アップロード処理
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function upload_seikyu()
	{

		log_message('debug', '★AdminSpgsController upload_seikyu 実行');

		try {
			// ライブラリ宣言
			$uploadSeikyu = new Upload_seikyu();
			$uploadSeikyu->set_msg_site('upload_seikyu');

			if (!isset($_SERVER['PHP_AUTH_USER'])) {
				header("WWW-Authenticate: Basic realm=\"My Realm\"");
				header("HTTP/1.0 401 Unauthorized");
				echo "ユーザーがない時に送信されるテキスト\n";
				exit;
			} else {

				if ($_SERVER['PHP_AUTH_USER'] != 'U12345678901234567890' || $_SERVER['PHP_AUTH_PW'] != 'p98765432109876543210') {
					header("WWW-Authenticate: Basic realm=\"My Realm\"");
					header("HTTP/1.0 401 Unauthorized");
					echo "ユーザーが不一致の場合に送信されるテキスト\n";
					exit;
				}
			}

			$uploadSeikyu->set_msg_site('upload_seikyu');

			log_message('debug', 'ポイント　UNZIP　START');
			if ($uploadSeikyu->unzip(WRITEPATH . "fileup/seikyu.zip", WRITEPATH . "fileup/seikyu") != []) {
				log_message('debug', 'ポイント　UNZIP　END');
				//seikyuフォルダチェック
				$folder = WRITEPATH . "seikyu";
				if (!is_dir($folder)) {
					mkdir($folder, 0777, TRUE);
				}
				//uploadファイルを読込
				$uploadSeikyu->put_status("PDFコピー", "start");
				log_message('debug', 'ポイント　PDFコピー　START');
				$ary_files = glob(APPPATH . '../fileup/seikyu/*.PDF');
				for ($wki = 0; $wki < count($ary_files); $wki++) {
					$file_name = basename($ary_files[$wki]);
					$files = explode("-", $file_name);
					$ucd = $files[1];
					log_message('debug', 'UCD:' . $ucd);
					log_message('debug', 'hash:' . hash('md5', $ucd, false));
					$ym = substr($files[2], 0, 6);

					//logging('debug', 'ポイント　ファイル読込:'.$ary_files[$wki]);
					$con = file_get_contents($ary_files[$wki]);
					$folder_path = $folder . "/" . hash('md5', $ucd, false);
					if (!is_dir($folder_path)) {
						mkdir($folder_path, 0777, TRUE);
					}

					//[Mnt-009]------------------------------------------------------------------------------------------>> Edit Start 25/11/04
					$config_spgsadmin = config(\Config\Info\Spgsadmin::class);
					$autoSeikyuKill = $config_spgsadmin->autoSeikyuKill;
					log_message('debug', 'ポイント　自動削除:' . $autoSeikyuKill);
					if ($autoSeikyuKill != 1) {
						// 以前のお客様の請求書を削除する
						$wkstr = $files[0];
						$wkstrs = explode("_", $wkstr);
						$mise = intval($wkstrs[1]);
						$NyukyoBi = $this->getNyukyoYmd($mise, $ucd);
						log_message('debug', 'ポイント　入居日:' . $NyukyoBi);
						log_message('debug', 'ポイント　フォルダ:' . $folder_path);
						$files = glob($folder_path . '/*.PDF');
						for ($wkj = 0; $wkj < count($files); $wkj++) {
							$lasttime = date('Ymd', filemtime($files[$wkj]));
							//log_message('debug', 'ポイント　検索ファイル:'.$files[$wkj]);
							log_message('debug', 'ポイント　最終更新日:' . $lasttime);
							if ($lasttime <= $NyukyoBi) {
								log_message('debug', 'ポイント　転入時ファイル削除:' . $files[$wkj]);
								unlink($files[$wkj]);
							}
						}
					}

					//同じ年月のファイルがあれば削除する

					$wkstr = explode("-", $file_name);
					$wkfileName = $wkstr[0] . '-' . $wkstr[1] . '-' . $wkstr[2];
					log_message('debug', 'ポイント　同年月：' . $wkfileName);
					$files = glob($folder_path . '/' . $wkfileName . '*.PDF');
					for ($wkj = 0; $wkj < count($files); $wkj++) {
						log_message('debug', 'ポイント　同年月ファイル削除');
						unlink($files[$wkj]);
					}

					// ファイルコピー
					$file_path = $folder_path . "/" . $file_name;
					file_put_contents($file_path, $con, LOCK_EX);

					//三か月前のファイルを削除する
					//logging('debug', 'ポイント　ファイル削除 開始');
					$files = glob($folder_path . '/*.PDF');
					for ($wkj = 0; $wkj < count($files); $wkj++) {
						$lasttime = date('Ymd', filemtime($files[$wkj]));
						$month3 = date('Ymd', strtotime('-3 month'));
						if ($lasttime < $month3) {
							log_message('debug', 'ポイント　三か月前ファイル削除');
							unlink($files[$wkj]);
						}
					}

					$uploadSeikyu->put_parcent('PDFファイルコピー', $wki, count($ary_files));
				}
				$uploadSeikyu->put_status("PDFコピー", "end");
				log_message('debug', 'ポイント　PDFコピー　END');

				//WorkPDF削除
				$uploadSeikyu->put_status("WorkPDF削除", "start");
				log_message('debug', 'ポイント　WorkPDF削除　START');
				$files = glob(WRITEPATH . 'fileup/seikyu/*.PDF');
				log_message('debug', 'ポイント　WorkPDF削除件数:' . count($files));
				for ($wkj = 0; $wkj < count($files); $wkj++) {
					unlink($files[$wkj]);
				}

				$uploadSeikyu->put_status("WorkPDF削除", "end");
				log_message('debug', 'ポイント　WorkPDF削除　END');


				echo "true";
				$uploadSeikyu->set_msg_end('upload_seikyu');
				die();
				exit(0);
				log_message('debug', 'まだ？');
			} else {
				log_message('debug', 'ポイント　UNZIP　ERROR');
				header('Content-Type: text/plain; charset=UTF-8');
				echo "false";
				$uploadSeikyu->set_msg_end('upload_seikyu');
				die();
				exit(1);
				log_message('debug', 'まだですか？');
			}
		} catch (\Exception $ex) {

			echo $ex->getMessage();
			$uploadSeikyu->set_msg_end('upload_seikyu');

		} catch (\error $ex) {

			echo $ex->getMessage();
			$uploadSeikyu->set_msg_end('upload_seikyu');

		}

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	ログイン履歴照会ダウンロード
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function download_seekhistory($ymd, $usercd)
	{

		log_message('debug', '★AdminSpgsController download_seekhistory 実行');

		$uploadSeikyu = service('uploadSeikyu');

		// ライブラリ利用
		log_message('debug', '★AdminSpgsController download_seekhistory内 set_msg_site　実行');
		$uploadSeikyu->set_msg_site('download_seekhistory');

		if ($usercd != '') {
			$kensaku_usercd = preg_replace('/[^0-9]/', '', $usercd);
			// 9桁に満たない場合は右側にゼロ詰め
			$kensaku_usercd = str_pad($kensaku_usercd, 9, 0, STR_PAD_RIGHT);
		} else {
			$kensaku_usercd = '';
		}

		if ($ymd != '') {
			$kensaku_ymd = preg_replace('/[^0-9]/', '', $ymd);
		} else {
			$kensaku_ymd = '';
		}
		$offset = 0;

		log_message('debug', 'データ取得開始');
		# 顧客コードとoffset値と、1ページに表示するレコードの数を渡し、モデルより
		# ログイン一覧を取得します。
		$query = $this->get_login_list2(10000, $offset, $kensaku_ymd, $kensaku_usercd);
		$csv = "";

		log_message('debug', 'データ読込');
		foreach ($query as $row) {
			log_message('debug', '$row[name] = ' . $row['name']);
			$entryy = substr($row['entryymd'], 0, 4);
			$entrym = substr($row['entryymd'], 4, 2);
			$entryd = substr($row['entryymd'], 6, 2);
			$dspentryymd = $entryy . '/' . $entrym . '/' . $entryd;
			if ($row['entrytime'] != 0) {
				if ($row['entrytime'] > 99999)
					$entryh = substr($row['entrytime'], -6, 2);
				if ($row['entrytime'] <= 99999)
					$entryh = substr($row['entrytime'], -5, 1);
				$entrymm = substr($row['entrytime'], -4, 2);
				$entrys = substr($row['entrytime'], -2, 2);
				$dspentrytime = $entryh . ':' . $entrymm;
			} else {
				$dspentrytime = '';
			}
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


			$csv .= $dspentryymd . "," . $dspentrytime . "," . $dspusercd . "," . trim($row['name']) . "\n";
		}
		// $csv = mb_convert_encoding($csv,"sjis", "utf-8");
		$csv = mb_convert_encoding($csv, "SJIS-win", "utf-8");

		$uploadSeikyu->set_msg_end('upload_seikyu');

		// CI4用に変更
		// header("Content-Type: application/force-download");
		// header('Content-Disposition: attachment; filename="ログイン履歴.csv"');
		// echo $csv;
		// 日本語ファイル名対応
		$filename = 'ログイン履歴.csv';
		$encoded_filename = mb_convert_encoding($filename, 'UTF-8', 'auto');

		// RFC 2231形式でエンコード（日本語ファイル名対応）
		$disposition = sprintf(
			"attachment; filename=\"%s\"; filename*=UTF-8''%s",
			rawurlencode($filename),  // 古いブラウザ用
			rawurlencode($encoded_filename)  // 新しいブラウザ用
		);
		return $this->response
			->setHeader('Content-Type', 'text/csv; charset=UTF-8')
			->setHeader('Content-Disposition', $disposition)
			->setBody($csv);

	}
	//[Mnt-002]<<------------------------------------------------------------------------------------------ Edit E n d 24/01/29

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	ファイルダウンロード
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function pwdmail()
	{
		log_message('debug', '★AdminSpgsController pwdmail 実行');

		$data['pwdlist'] = '';
		$data['pwdcount'] = '';
		$data['maillist'] = '';
		$data['mailcount'] = '';
		$data['login_id'] = session('login_id');
		$data['login_pwd'] = session('login_pwd');
		$data['login_name'] = session('login_name');
		$data['login_grant'] = session('login_grant');
		$data['error'] = '';
		$data['header_img01'] = $this->adminConfig->header_img01;
		$data['header_url01'] = $this->adminConfig->header_url01;
		$data['header_img02'] = $this->adminConfig->header_img02;
		$data['header_url02'] = $this->adminConfig->header_url02;
		$data['buttons'] = session('buttons');
		$data['max_button'] = $this->adminConfig->max_button;

		log_message('debug', '★AdminSpgsController adminview/spgsadmin_pwdmail_disp View実行');
		log_message('debug', '★AdminSpgsController $data = ' . print_r($data, true));

		return view('adminview/spgsadmin_pwdmail_disp', $data);

	}

	public function pwdmail_disp()
	{
		log_message('debug', '★AdminSpgsController pwdmail_disp 実行');

		# sndymd=0の情報があれば、モデルから取得します。
		$data['pwdcount'] = $this->get_pwd_count();
		$data['pwdlist'] = $this->get_pwd_list();

		$data['mailcount'] = $this->get_mail_count();
		$data['maillist'] = $this->get_mail_list();

		$data['login_id'] = session('login_id');
		$data['login_pwd'] = session('login_pwd');
		$data['login_name'] = session('login_name');
		$data['login_grant'] = session('login_grant');
		$data['error'] = '';
		$data['header_img01'] = $this->adminConfig->header_img01;
		$data['header_url01'] = $this->adminConfig->header_url01;
		$data['header_img02'] = $this->adminConfig->header_img02;
		$data['header_url02'] = $this->adminConfig->header_url02;
		$data['buttons'] = session('buttons');
		$data['max_button'] = $this->adminConfig->max_button;

		return view('adminview/spgsadmin_pwdmail_disp', $data);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//		パスワード及びメールの送信日付・時刻の更新
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function pwdmail_update()
	{

		log_message('debug', '★AdminSpgsController pwdmail_update 開始');

		// #トランザクションスタート
		// $this->db->transStart();
		// $nowymd=date("Ymd");	#現在日
		// $nowtime=date("His");	#現在時刻

		// $data = array(
		// 'sndymd'	=> $nowymd ,
		// 'sndtime'	=> $nowtime
		// 	);
		// $this->db->where('sndymd'		, 0);
		// $this->db->update('spgsmail'	, $data);
		// $this->db->update('spgspwd'		, $data);
		// $this->db->transComplete();
		// //echo '更新しました。';
		// return TRUE;


		$data = [
			'sndymd' => date("Ymd"),    // 現在日
			'sndtime' => date("His")      // 現在時刻
		];

		#トランザクションスタート
		$db = \Config\Database::connect();
		$db->transStart();   // トランザクション開始

		try {
			// spgsmail更新
			$this->spgsmailModel
				->where('sndymd', 0)
				->set($data)
				->update();

			// spgspwd更新
			$this->spgspwdModel
				->where('sndymd', 0)
				->set($data)
				->update();

			$db->transComplete();

			// トランザクション結果をチェック
			if ($db->transStatus() === false) {
				log_message('debug', '★AdminSpgsController pwdmail_update トランザクション失敗');
				return false;
			}

		} catch (\Exception $e) {
			log_message('debug', '★AdminSpgsController pwdmail_update トランザクション想定外エラー：' . $e->getmessage());
			return false;
		}

		return TRUE;

	}


	////////////////////////////////////////////////////////////////////////////////////////
	//		PWD・MAILファイル作成・ダウンロード処理
	////////////////////////////////////////////////////////////////////////////////////////
	public function pwdmail_download()
	{
		log_message('debug', '★AdminSpgsController pwdmail_download 実行');

		# sndymd=0の情報があれば、モデルから取得します。
		$data['pwdlist'] = $this->get_pwd_list();

		$data['maillist'] = $this->get_mail_list();

		$data['login_id'] = session('login_id');
		$data['login_pwd'] = session('login_pwd');
		$data['login_name'] = session('login_name');
		$data['login_grant'] = session('login_grant');
		$data['error'] = '';
		$data['header_img01'] = $this->adminConfig->header_img01;
		$data['header_url01'] = $this->adminConfig->header_url01;
		$data['header_img02'] = $this->adminConfig->header_img02;
		$data['header_url02'] = $this->adminConfig->header_url02;

		log_message('debug', '★AdminSpgsController pwdmail_download 処理前');
		log_message('debug', '★AdminSpgsController pwdmail_download $pwdlist = ' . print_r($data['pwdlist'], true));
		log_message('debug', '★AdminSpgsController pwdmail_download $maillist = ' . print_r($data['maillist'], true));

		// 正常にファイル作成が出来た場合、sndymd、sndtimeを更新する。
		if (!$this->pwdmail_update()) {
			log_message('debug', '★AdminSpgsController pwdmail_download パスワード及びメール情報の更新に失敗しました。');
			echo 'パスワード及びメール情報の更新に失敗しました。';
			exit;
		} else {
			log_message('debug', '★AdminSpgsController pwdmail_download パスワード及びメール情報の更新に成功しました。');
			log_message('debug', '★AdminSpgsController pwdmail_download $pwdlist = ' . print_r($data['pwdlist'], true));
			log_message('debug', '★AdminSpgsController pwdmail_download $maillist = ' . print_r($data['maillist'], true));

			// パスワード・メールの更新用ファイルの作成
			if (!$this->pwdmail_makefile($data)) {
				log_message('debug', '★AdminSpgsController pwdmail_download パスワード及びメールファイルの作成に失敗しました。');
				echo 'パスワード及びメールファイルの作成に失敗しました。';
				exit;
			}

			log_message('debug', '★AdminSpgsController pwdmail_download パスワード及びメールファイルの作成に成功しました。');

		}

		log_message('debug', '★AdminSpgsController pwdmail_download 完了');

	}

	public function get_login_list2($limit, $offset, $kensaku_ymd, $kensaku_usercd)
	{

		log_message('debug', '★AdminSpgsController get_login_list2 開始');

		$builder = $this->spgsloginModel;

		if ($kensaku_ymd != '') {
			$builder = $builder->where('entryymd', $kensaku_ymd);
		}

		if ($kensaku_usercd != '') {
			$builder = $builder->where('usercd', $kensaku_usercd);
		}

		$query = $builder
			->orderBy('entryymd', 'DESC')
			->orderBy('entrytime', 'DESC')
			->findAll($limit, $offset);

		return $query;
	}

	public function get_login_count2($kensaku_ymd, $kensaku_usercd)
	{

		log_message('debug', '★AdminSpgsController get_login_count2 開始');

		$builder = $this->spgsloginModel;

		if ($kensaku_ymd != '') {
			$builder = $builder->where('entryymd', $kensaku_ymd);
		}
		if ($kensaku_usercd != '') {
			$builder = $builder->where('usercd', $kensaku_usercd);
		}

		return $builder->countAllResults();
	}

	// ユーザー検索
	public function get_user_list($limit, $offset, $kensaku_name, $kensaku_misecd, $kensaku_usercd)
	{
		log_message('debug', '★AdminSpgsController get_user_list 開始');
		$start_time = microtime(true);

		$builder = $this->spgsuserModel;

		$builder = $builder->where('misecd', $kensaku_misecd);
		if ($kensaku_name != '') {
			$builder = $builder->like('name', $kensaku_name);
		}
		if ($kensaku_usercd != '') {
			$builder = $builder->where('usercd', $kensaku_usercd);
		}
		$builder = $builder->orderBy('usercd asc');

		log_message('debug', '★AdminSpgsController get_user_list findAll 直前');
		$query = $builder->findAll($limit, $offset);
		$end_time = microtime(true);

		log_message('debug', '★AdminSpgsController get_user_list 終了 (Time: ' . ($end_time - $start_time) . 's)');

		return $query;

	}

	// ユーザーリストのカウント
	public function get_user_count($kensaku_name, $kensaku_misecd, $kensaku_usercd)
	{
		log_message('debug', '★AdminSpgsController get_user_count 開始');
		$start_time = microtime(true);

		$builder = $this->spgsuserModel;
		$builder = $builder->where('misecd', $kensaku_misecd);
		if ($kensaku_name != '') {
			$builder = $builder->like('name', $kensaku_name);
		}
		if ($kensaku_usercd != '') {
			$builder = $builder->where('usercd', $kensaku_usercd);
		}

		log_message('debug', '★AdminSpgsController get_user_count countAllResults 直前');
		$count = $builder->countAllResults();
		$end_time = microtime(true);

		log_message('debug', '★AdminSpgsController get_user_count 終了 (Time: ' . ($end_time - $start_time) . 's)');

		return $count;

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//		PWDの情報を取得
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function get_pwd_list()
	{
		log_message('debug', '★AdminSpgsController get_pwd_list 開始');

		// //	$this->db->where('sndymd', 0);
		// //	$query = $this->db->get('spgspwd');
		// //	return $query;

		// 	$sq = "select * from spgspwd where sndymd = 0";
		// 	$query2 = $this->db->query($sq);
		// 	return $query2;

		$query = $this->spgspwdModel
			->where('sndymd', 0)
			->findAll();
		return $query;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//		PWDのカウント情報を取得
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function get_pwd_count()
	{
		log_message('debug', '★AdminSpgsController get_pwd_count 開始');

		// $this->db->where('sndymd'	, 0);
		// $query = $this->db->get('spgspwd');
		// return $query->num_rows();

		$count = $this->spgspwdModel
			->where('sndymd', 0)
			->countAllResults();
		return $count;
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//		MAILの情報を取得
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function get_mail_list()
	{
		log_message('debug', '★AdminSpgsController get_mail_list 開始');
		// $this->db->where('sndymd'	, 0);
		// $query = $this->db->get('spgsmail');
		// return $query;
		$query = $this->spgsmailModel
			->where('sndymd', 0)
			->findAll();

		return $query;

	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//		MAILのカウント情報を取得
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function get_mail_count()
	{
		log_message('debug', '★AdminSpgsController get_mail_count 開始');

		// $this->db->where('sndymd'	, 0);
		// $query = $this->db->get('spgsmail');
		// return $query->num_rows();
		$count = $this->spgsmailModel
			->where('sndymd', 0)
			->countAllResults();
		return $count;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//		パスワード及びメールのダウンロード用ファイルの作成 旧pwdmail_download（モデル）
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function pwdmail_makefile($data)
	{
		log_message('debug', '★AdminSpgsController pwdmail_makefile 開始');

		// //UTF-8→SJISへ変換しながら、テキスト作成
		// $wkstr='';
		// foreach($data['pwdlist']->result() as $row):
		// 	$wkstr.=mb_convert_encoding( 1											, 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->misecd, 4, 0, STR_PAD_LEFT )	, 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->usercd, 9, 0, STR_PAD_RIGHT ), 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->oldpwd,20 )					, 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->newpwd,20 )					, 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->entryymd,8 )					, 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->entrytime,6 ). "\r\n"		, 'SJIS', 'UTF-8');
		// endforeach;
		// foreach($data['maillist']->result() as $row):
		// 	$wkstr.=mb_convert_encoding( 2											, 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->misecd, 4, 0, STR_PAD_LEFT )	, 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->usercd, 9, 0, STR_PAD_RIGHT ), 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->oldmail,50 )					, 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->newmail,50  )				, 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->entryymd,8 )					, 'SJIS', 'UTF-8');
		// 	$wkstr.=mb_convert_encoding( str_pad($row->entrytime,6 ). "\r\n"		, 'SJIS', 'UTF-8');
		// endforeach;
		// $filename='spgsmail.txt';
		// //echo 'filepath＝' . $wkstr;
		// if ( !write_file( './fileout/' . $filename, $wkstr,'w'))
		// {
		// 	//echo 'write_fileでエラーです。（書けない）';
		// 	return false;
		// }
		// else
		// {
		// 	$filedt = file_get_contents('./fileout/' . $filename); // ファイルの内容を読み取る
		// 	force_download($filename, $filedt);
		// }
		// return true;

		$wkstr = '';

		// パスワードリスト
		if ($data['pwdlist'] !== '') {
			foreach ($data['pwdlist'] as $row) {
				$line =
					"1" .
					str_pad($row['misecd'], 4, 0, STR_PAD_LEFT) .
					str_pad($row['usercd'], 9, 0, STR_PAD_RIGHT) .
					str_pad($row['oldpwd'], 20) .
					str_pad($row['newpwd'], 20) .
					str_pad($row['entryymd'], 8) .
					str_pad($row['entrytime'], 6);

				log_message('debug', '★AdminSpgsController pwdmail_makefile pwdlist $line = ' . $line);
				$wkstr .= mb_convert_encoding($line, "SJIS-win", "utf-8") . "\n";
			}
		}

		// メールリスト
		if ($data['maillist'] !== '') {
			foreach ($data['maillist'] as $row) {
				$line =
					"2" .
					str_pad($row['misecd'], 4, 0, STR_PAD_LEFT) .
					str_pad($row['usercd'], 9, 0, STR_PAD_RIGHT) .
					str_pad($row['oldmail'], 50) .
					str_pad($row['newmail'], 50) .
					str_pad($row['entryymd'], 8) .
					str_pad($row['entrytime'], 6);

				log_message('debug', '★AdminSpgsController pwdmail_makefile  maillist $line = ' . $line);
				$wkstr .= mb_convert_encoding($line, "SJIS-win", "utf-8") . "\n";

			}
		}

		log_message('debug', '★AdminSpgsController pwdmail_makefile ファイルに保存せず、直接ダウンロード');
		log_message('debug', '★AdminSpgsController pwdmail_makefile $wkstr = ' . print_r($wkstr, true));

		// 日本語ファイル名対応
		$filename = 'spgsmail.txt';
		$encoded_filename = mb_convert_encoding($filename, 'UTF-8', 'auto');

		// RFC 2231形式でエンコード（日本語ファイル名対応）
		$disposition = sprintf(
			"attachment; filename=\"%s\"; filename*=UTF-8''%s",
			rawurlencode($filename),  // 古いブラウザ用
			rawurlencode($encoded_filename)  // 新しいブラウザ用
		);

		log_message('debug', '★AdminSpgsController pwdmail_makefile $wkstr = ' . print_r($wkstr));

		return $this->response
			->setHeader('Content-Type', 'text/csv; charset=UTF-8')
			->setHeader('Content-Disposition', $disposition)
			->setBody($wkstr);


	}

	public function table_del()
	{

		log_message('debug', '★AdminSpgsController table_del 開始');
		log_message('debug', '★AdminSpgsController spgsuser 削除 start');

		// $this->db->where_not_in('misecd', 0);
		// $this->db->delete('spgsuser');
		$this->spgsuserModel
			->whereNotIn('misecd', [0])
			->delete();

		log_message('debug', '★AdminSpgsController spgskensin 削除 start');
		// $this->db->where_not_in('misecd', 0);
		// $this->db->delete('spgskensin');
		$this->spgskensinModel
			->whereNotIn('misecd', [0])
			->delete();

		log_message('debug', '★AdminSpgsController spgstori 削除 start');

		$this->spgstoriModel
			->whereNotIn('recno', [-1])
			->delete();

		log_message('debug', '★AdminSpgsController spgsryokin 削除 start');
		// $this->db->where_not_in('recno', -1);
		// $this->db->delete('spgsryokin');
		$this->spgsryokinModel
			->whereNotIn('recno', [-1])
			->delete();

		log_message('debug', '★AdminSpgsController spgskigu 削除 start');
		// $this->db->where_not_in('recno', -1);
		// $this->db->delete('spgskigu');
		$this->spgskiguModel
			->whereNotIn('recno', [-1])
			->delete();

		log_message('debug', '★AdminSpgsController spgskenmsg 削除 start');
		// $this->db->where_not_in('recno', -1);
		// $this->db->delete('spgskenmsg');
		$this->spgskenmsgModel
			->whereNotIn('recno', [-1])
			->delete();

		log_message('debug', '★AdminSpgsController spgskanrilogin 削除 start');
		// $this->db->where_not_in('misecd', 0);
		// $this->db->delete('spgskanrilogin');
		$this->spgskanriloginModel
			->whereNotIn('misecd', [0])
			->delete();

		return TRUE;

	}

	// ユーザー単位でテーブルのレコードを消す（user、kensin、tori、kigu）
	public function table_del_userselect($misecd, $usercd)
	{
		log_message('debug', '★AdminSpgsController table_del_userselect開始');

		log_message('debug', '★AdminSpgsController spgsuser 削除 start');

		$this->spgsuserModel
			->whereNotIn('misecd', [0])
			->whereNotIn('misecd', [0])
			->where('misecd', $misecd)
			->where('usercd', $usercd)
			->delete();

		log_message('debug', '★AdminSpgsController spgskensin 削除 start');

		$this->spgskensinModel
			->whereNotIn('misecd', [0])
			->where('misecd', $misecd)
			->where('usercd', $usercd)
			->delete();

		log_message('debug', '★AdminSpgsController spgstori 削除 start');

		$this->spgstoriModel
			->whereNotIn('recno', [0])
			->where('misecd', $misecd)
			->where('usercd', $usercd)
			->delete();

		log_message('debug', '★AdminSpgsController spgskigu 削除 start');
		$this->spgskiguModel
			->whereNotIn('recno', [0])
			->where('misecd', $misecd)
			->where('usercd', $usercd)
			->delete();

		return TRUE;

	}
	// テーブルの特定レコードを消す（検針メッセージテーブル
	public function table_del_userselect_kenmsg($misecd, $msgno)
	{

		log_message('debug', '★AdminSpgsController spgskenmsg 削除 start');

		$this->spgskenmsgModel
			->where('misecd', $misecd)
			->where('msgno', $msgno)
			->delete();

		return TRUE;

	}
	// テーブルの特定レコードを消す（料金表テーブル
	public function table_del_userselect_ryokin($misecd, $ryokinno)
	{

		log_message('debug', '★AdminSpgsController spgsryokin 削除 start');
		$this->spgsryokinModel
			->where('misecd', $misecd)
			->where('ryokinno', $ryokinno)
			->delete();

		return TRUE;

	}
	// テーブルの特定レコードを消す（管理者ログインテーブル
	public function table_del_userselect_kanrilogin($misecd, $loginid)
	{
		log_message('debug', '★AdminSpgsController spgskanrilogin 削除 start');
		$this->spgskanriloginModel
			->whereNotIn('misecd', [0])
			->where('misecd', $misecd)
			->where('loginid', $loginid)
			->delete();

		return TRUE;

	}

	// ユーザー単位でテーブルのレコードを消す（kensin、tori、kigu）+ userは一部項目クリア
	public function table_del_userselect2($misecd, $usercd)
	{

		log_message('debug', '★AdminSpgsController table_del_userselect2 開始');
		log_message('debug', '★$misecd= ' . $misecd);
		log_message('debug', '★$usercd= ' . $usercd);


		// $this->db->where_not_in('misecd', 0);
		// $this->db->where('misecd', $misecd);
		// $this->db->where('usercd', $usercd);
		// $this->db->delete('spgskensin');

		// $this->db->where_not_in('recno', 0);
		// $this->db->where('misecd', $misecd);
		// $this->db->where('usercd', $usercd);
		// $this->db->delete('spgstori');

		// $this->db->where_not_in('recno', 0);
		// $this->db->where('misecd', $misecd);
		// $this->db->where('usercd', $usercd);
		// $this->db->delete('spgskigu');

		$data = array(
			'name' => '',
			'mail' => '',
			'ryokinno' => 0,
			'siyoryo' => '',
			'kihon' => '',
			'ryokin' => '',
			'status' => 0,
			'entryymd' => date("Ymd"),
			'entrytime' => date("His"),
			'zeikbn' => 0,	// 税区分
			'msgno' => 0
		);

		#トランザクションスタート
		$db = \Config\Database::connect();
		$db->transStart();   // トランザクション開始

		try {
			//  spgskensin更新
			log_message('debug', '★AdminSpgsController spgskensin 削除 start');

			$this->spgskensinModel
				->whereNotIn('misecd', [0])
				->where('misecd', $misecd)
				->where('usercd', $usercd)
				->delete();

			log_message('debug', '★AdminSpgsController spgstori 削除 start');

			$this->spgstoriModel
				->whereNotIn('recno', [0])
				->where('misecd', $misecd)
				->where('usercd', $usercd)
				->delete();

			log_message('debug', '★AdminSpgsController spgskigu 削除 start');
			$this->spgskiguModel
				->whereNotIn('recno', [0])
				->where('misecd', $misecd)
				->where('usercd', $usercd)
				->delete();


			$this->spgsuserModel
				->whereNotIn('misecd', [0])
				->where('misecd', $misecd)
				->where('usercd', $usercd)
				->set($data)
				->update();


			$db->transComplete();

			// トランザクション結果をチェック
			if ($db->transStatus() === false) {
				log_message('debug', '★AdminSpgsController table_del_userselect2 トランザクション失敗');
				return false;
			}

		} catch (\Exception $e) {
			log_message('debug', '★AdminSpgsController table_del_userselect2 トランザクション想定外エラー：' . $e->getmessage());
			return false;
		}

		// *** 請求書ファイルとフォルダを削除する。***
		log_message('debug', '★AdminSpgsController 請求書PDF削除 start');

		// ユーザー固有の設定情報を読み込む
		$this->adminConfig = config(\Config\Info\Spgsinfo::class);
		log_message('debug', '★AdminSpgsController table_del_userselect2 ユーザー固有の設定情報を読み込む');

		// ユーザーコードの設定
		$cds = $this->adminConfig->code_style;

		$ucd = trim($usercd);
		log_message("info", "削除ユーザー:" . $ucd);

		if ($cds == 0 && substr($ucd, -3, 3) == "000") {
			$ucd = substr($ucd, 0, 6);
		}
		// フォルダを設定する
		$folder = WRITEPATH . "seikyu";
		$folder_path = $folder . "/" . hash('md5', $ucd, false);
		//[Mnt-007]------------------------------------------------------------------------------------------>> Edit Start 25/04/21
		log_message("info", "請求書PDFフォルダパス:" . $folder_path);
		//[Mnt-007]<<------------------------------------------------------------------------------------------ Edit E n d 25/04/21
		if (!is_dir($folder)) {
			mkdir($folder, 0777, TRUE);
		}
		if (!is_dir($folder_path)) {
			mkdir($folder_path, 0777, TRUE);
		}
		// フォルダ内のファイルを削除する
		$files = glob($folder_path . '/*');
		for ($wkj = 0; $wkj < count($files); $wkj++) {
			unlink($files[$wkj]);
		}
		// フォルダを削除する
		rmdir($folder_path);
		log_message("info", "請求書PDF削除 E n d");

		return TRUE;

	}
	public function getNyukyoYmd($misecd, $usercd)
	{
		$query = $this->spgsuserModel
			->where('misecd', $misecd)
			->where('usercd', $usercd)
			->first();
		if ($query->countAllResults() > 0) {
			$row = $query->row();
			return $query['nyukyoymd'];
		} else {
			return '';
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////                                                            ///////////////////////////////////////////////////////////////////////////
	/////////////////////                                                            ///////////////////////////////////////////////////////////////////////////
	/////////////////////          ここより、プライベートメソッド領域です。                 ///////////////////////////////////////////////////////////////////////////
	/////////////////////          アドレスでは、指定できません。                         ///////////////////////////////////////////////////////////////////////////
	/////////////////////                                                            ///////////////////////////////////////////////////////////////////////////
	/////////////////////                                                            ///////////////////////////////////////////////////////////////////////////
	/////////////////////                                                            ///////////////////////////////////////////////////////////////////////////
	/////////////////////                                                            ///////////////////////////////////////////////////////////////////////////
	/////////////////////                                                            ///////////////////////////////////////////////////////////////////////////
	/////////////////////                                                            ///////////////////////////////////////////////////////////////////////////
	/////////////////////                                                            ///////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	ページネーションの生成		プライベートメソッド
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	// ページネーションの生成<<<　共通　>>>
	private function _generate_pagination($path, $total, $rec, $uri_segment)
	{
	}
}
