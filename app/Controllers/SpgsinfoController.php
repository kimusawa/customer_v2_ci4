<?php
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　Modeule           Spgsinfo　Controller
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2024.01.22  tanaka       Mnt-001  請求書と決済オプション追加
//2024.08.30  tanaka       Mnt-002  特商法のView追加
//2025.01.15  tanaka       Mnt-005  料金三部制（設備使用料対応）
//2025.01.30  tanaka       Mnt-006  決済処理不具合修正
//2025.02.04  tanaka       Mnt-007  請求書PDFファイルが存在する場合のみ、請求書ボタンを出力する。
//2025.12.04  kimura       Mnt-008  CI4対応
//**************************************************************************

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SpgsinfoController extends BaseController
{


	public function __construct()
	{
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	検針情報ページ
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function kensin_disp()
	{

		log_message('debug', '★Spgsinfoコントローラー kensin_disp が呼び出されました。');

		// 検索条件をセット
		$data = [

			'login_id' => session('login_id'),
			'login_pwd' => session('login_pwd'),
			'login_name' => session('login_name'),
			'login_misecd' => session('login_misecd'),
			'login_usercd' => session('login_usercd'),
			'dspusercd' => session('login_dspusercd'),
			'misecd' => session('login_misecd'),
			'usercd' => session('login_usercd'),
			'files' => session('files'),
			'buttons' => session('buttons'),

			//画像ファイル
			'header_img01' => $this->userConfig->header_img01,
			'header_url01' => $this->userConfig->header_url01,
			'header_img02' => $this->userConfig->header_img02,
			'header_url02' => $this->userConfig->header_url02,

			//販売店ごとの設定
			'oshirase_flg' => $this->userConfig->oshirase_flg,
			'bill_flg' => $this->userConfig->bill_flg,
			'max_button' => $this->userConfig->max_button,
			'dgf_flg' => $this->userConfig->dgf_flg,

			'spgskensin_result' => $this->spgskensinModel
				->where('misecd', session('login_misecd'))
				->where('usercd', session('login_usercd'))
				->first()

		];

		log_message('debug', '★Spgsinfoコントローラー kensin_disp spgskensin_result=' . print_r($data['spgskensin_result'], true));
		log_message('debug', '★Spgsinfoコントローラー kensin_disp files=' . print_r($data['files'], true));

		return view('authview/spgsinfo_kensin', $data);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	請求紹介ページ 20231226検針情報からコピーしています
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function seikyu_disp()
	{

		$data = [

			'login_id' => session('login_id'),
			'login_pwd' => session('login_pwd'),
			'login_name' => session('login_name'),
			'login_misecd' => session('login_misecd'),
			'login_usercd' => session('login_usercd'),
			'dspusercd' => session('login_dspusercd'),
			'misecd' => session('login_misecd'),
			'usercd' => session('login_usercd'),
			'files' => session('files'),
			'buttons' => session('buttons'),

			//画像ファイル
			'header_img01' => $this->userConfig->header_img01,
			'header_url01' => $this->userConfig->header_url01,
			'header_img02' => $this->userConfig->header_img02,
			'header_url02' => $this->userConfig->header_url02,

			//販売店ごとの設定
			'oshirase_flg' => $this->userConfig->oshirase_flg,
			'bill_flg' => $this->userConfig->bill_flg,
			'max_button' => $this->userConfig->max_button,
			'dgf_flg' => $this->userConfig->dgf_flg,

			'spgskensin_result' => $this->spgskensinModel
				->where('misecd', session('login_misecd'))
				->where('usercd', session('login_usercd'))
				->first()

		];

		log_message('debug', '★Spgsinfoコントローラー seikyu_disp spgskensin_result=' . print_r($data['spgskensin_result'], true));
		log_message('debug', '★Spgsinfoコントローラー seikyu_disp files=' . print_r($data['files'], true));


		return view('authview/spgsinfo_seikyu', $data);

	}

	////////////////////////////////////////////////////////////////////////////////////////
	//		請求書PDFダウンロード
	////////////////////////////////////////////////////////////////////////////////////////
	public function download_seikyu($filename)
	{
		log_message('debug', '★Spgsinfoコントローラー download_seikyu が呼び出されました。 filename=' . $filename);

		$usercd = session('login_usercd');

		// code_styleによる処理
		$cds = $this->userConfig->code_style;
		if ($cds == 0 && substr($usercd, -3, 3) == "000") {
			$usercd = substr($usercd, 0, 6);
		}

		$userhash = hash("md5", $usercd, false);

		// ファイルパス構築
		$filepath = WRITEPATH . 'seikyu' . DIRECTORY_SEPARATOR . $userhash . DIRECTORY_SEPARATOR . $filename;

		log_message('debug', 'Download filepath: ' . $filepath);
		log_message('debug', 'File exists: ' . (file_exists($filepath) ? 'YES' : 'NO'));

		// ファイル存在チェック
		if (!file_exists($filepath)) {
			log_message('error', 'File not found: ' . $filepath);
			return redirect()->back()->with('error', 'ファイルが見つかりません。');
		}

		// ダウンロード
		return $this->response->download($filepath, null);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	過去取引情報ページ
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function torihiki_disp($offset)
	{

		log_message('debug', '★Spgsinfoコントローラー torihiki_disp が呼び出されました。');
		log_message('debug', '★Spgsinfoコントローラー torihiki_disp $offset=' . $offset);

		$usercd = session('login_usercd');
		$misecd = session('login_misecd');

		// 検索条件をセット
		$data = [

			'login_id' => session('login_id'),
			'login_pwd' => session('login_pwd'),
			'login_name' => session('login_name'),
			'login_misecd' => session('login_misecd'),
			'login_usercd' => session('login_usercd'),
			'dspusercd' => session('login_dspusercd'),
			'buttons' => session('buttons'),

			//ファイル情報を取得する
			'misecd' => session('login_misecd'),
			'usercd' => session('login_usercd'),
			'files' => session('files'),

			//画像ファイル
			'header_img01' => $this->userConfig->header_img01,
			'header_url01' => $this->userConfig->header_url01,
			'header_img02' => $this->userConfig->header_img02,
			'header_url02' => $this->userConfig->header_url02,

			//販売店ごとの設定
			'oshirase_flg' => $this->userConfig->oshirase_flg,
			'bill_flg' => $this->userConfig->bill_flg,
			'max_button' => $this->userConfig->max_button,
			'dgf_flg' => $this->userConfig->dgf_flg,

			// 取引履歴取得
			'query' => $this->spgstoriModel
				->where('misecd', $misecd)
				->where('usercd', $usercd)
				->orderBy('ymd DESC, recno ASC')
				->findAll($this->userConfig->meisai_rec, (int) $offset),
			// ->paginate($this->userConfig->meisai_rec,'default',(int)$offset / $this->userConfig->meisai_rec + 1),

			// 'pager' => $this->spgstoriModel->pager,
			'pager' => null,
		];

		// 取引履歴カウント
		$results = $this->spgstoriModel
			->where('misecd', $misecd)
			->where('usercd', $usercd)
			->findAll();
		$total = count($results);

		$data['list_total'] = $total;
		$data['list_limit'] = $this->userConfig->meisai_rec;
		$data['list_offset'] = ((int) $offset / $this->userConfig->meisai_rec) * $this->userConfig->meisai_rec;

		log_message('debug', '★Spgsinfoコントローラー torihiki_disp files=' . print_r($data['files'], true));
		log_message('debug', '★Spgsinfoコントローラー torihiki_disp spgsinfo_torihiki が呼び出されました。');

		return view('authview/spgsinfo_torihiki', $data);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	料金情報ページ
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function ryokin_disp($offset)
	{
		log_message('debug', '★Spgsinfoコントローラー ryokin_disp が呼び出されました。');
		log_message('debug', '★Spgsinfoコントローラー ryokin_disp $offset=' . $offset);

		$usercd = session('login_usercd');
		$misecd = session('login_misecd');

		// 該当ユーザーの情報を取得
		$spgsuser = $this->spgsuserModel
			->where('misecd', $misecd)
			->where('usercd', $usercd)
			->first();

		$ryokinno = (int) $spgsuser['ryokinno'];
		$msgno = (int) $spgsuser['msgno'];

		// 検針メッセージを取得
		$kenmsgdata = $this->spgskenmsgModel
			->where('misecd', $misecd)
			->where('msgno', $msgno)
			->first();

		// 検針情報を取得
		$kensindata = $this->spgskensinModel
			->where('misecd', $misecd)
			->where('usercd', $usercd)
			->first();

		// 料金表データを取得
		$ryokindata = $this->spgsryokinModel
			->where('ryokinno', $ryokinno)
			->where('misecd', $misecd)
			->findall();
		// 料金表データ有無チェック用
		$ryokincnt = count($ryokindata);

		log_message('debug', '★Spgsinfoコントローラー ryokin_disp $ryokincnt=' . $ryokincnt);
		log_message('debug', '★Spgsinfoコントローラー ryokin_disp $ryokinno=' . $ryokinno);

		// Viewへ渡すデータをセット
		$data = [

			'login_id' => session('login_id'),
			'login_pwd' => session('login_pwd'),
			'login_name' => session('login_name'),
			'login_misecd' => session('login_misecd'),
			'login_usercd' => session('login_usercd'),
			'dspusercd' => session('login_dspusercd'),
			'buttons' => session('buttons'),

			//ファイル情報を取得する
			'misecd' => session('login_misecd'),
			'usercd' => session('login_usercd'),
			'files' => session('files'),

			//画像ファイル
			'header_img01' => $this->userConfig->header_img01,
			'header_url01' => $this->userConfig->header_url01,
			'header_img02' => $this->userConfig->header_img02,
			'header_url02' => $this->userConfig->header_url02,

			//販売店ごとの設定
			'oshirase_flg' => $this->userConfig->oshirase_flg,
			'bill_flg' => $this->userConfig->bill_flg,
			'max_button' => $this->userConfig->max_button,
			'dgf_flg' => $this->userConfig->dgf_flg,

			'query' => $this->spgsryokinModel
				->where('ryokinno', $ryokinno)
				->where('misecd', $misecd)
				->orderBy('kaisono')
				->findAll($this->userConfig->meisai_rec, (int) $offset),
			// ->paginate($this->userConfig->meisai_rec,'default',(int)$offset / $this->userConfig->meisai_rec + 1),

			// 'pager' => $this->spgsryokinModel->pager,
			'pager' => null,

			// 料金表データ
			'ryokinno' => $ryokinno,

			// 検針メッセージ data2
			'msg1' => $kenmsgdata['msg1'] ?? '',  // nullの場合は空文字
			'msg2' => $kenmsgdata['msg2'] ?? '',  // nullの場合は空文字
			'msg3' => $kenmsgdata['msg3'] ?? '',  // nullの場合は空文字
			'msg4' => $kenmsgdata['msg4'] ?? '',  // nullの場合は空文字
			'msg5' => $kenmsgdata['msg5'] ?? '',  // nullの場合は空文字
			'msg6' => $kenmsgdata['msg6'] ?? '',  // nullの場合は空文字

			// 得意先情報 data3
			'siyoryo' => $spgsuser['siyoryo'] ?? '',
			'kihon' => $spgsuser['kihon'] ?? '',
			'ryokin' => $spgsuser['ryokin'] ?? '',
			'zeikbn' => $spgsuser['zeikbn'] ?? '',

			// 検針情報 data4
			'setubiryokin' => $kensindata['setubiryokin'] ?? '',
			'setubiseigyo' => $kensindata['setubiseigyo'] ?? '',

			'ryokincnt' => $ryokincnt,

			// 分類を参照する 2022.08.23
			'bunrui' => $spgsryokinModel['bunrui'] ?? 0,

		];

		// 取引履歴カウント
		$results = $this->spgsryokinModel
			->where('ryokinno', $ryokinno)
			->where('misecd', $misecd)
			->findAll();
		$total = count($results);

		$data['list_total'] = $total;
		$data['list_limit'] = $this->userConfig->meisai_rec;
		$data['list_offset'] = ((int) $offset / $this->userConfig->meisai_rec) * $this->userConfig->meisai_rec;

		log_message('debug', '★Spgsinfoコントローラー ryokin_disp files=' . print_r($data['files'], true));
		log_message('debug', '★Spgsinfoコントローラー ryokin_disp spgsinfo_ryokin が呼び出されました。');

		return view('authview/spgsinfo_ryokin', $data);

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	器具情報ページ
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function kigu_disp($offset)
	{

		log_message('debug', '★Spgsinfoコントローラー ryokin_disp が呼び出されました。');
		log_message('debug', '★Spgsinfoコントローラー ryokin_disp $offset=' . $offset);

		$usercd = session('login_usercd');
		$misecd = session('login_misecd');

		// Viewへ渡すデータをセット
		$data = [

			'login_id' => session('login_id'),
			'login_pwd' => session('login_pwd'),
			'login_name' => session('login_name'),
			'login_misecd' => session('login_misecd'),
			'login_usercd' => session('login_usercd'),
			'dspusercd' => session('login_dspusercd'),
			'buttons' => session('buttons'),

			//ファイル情報を取得する
			'misecd' => session('login_misecd'),
			'usercd' => session('login_usercd'),
			'files' => session('files'),

			//画像ファイル
			'header_img01' => $this->userConfig->header_img01,
			'header_url01' => $this->userConfig->header_url01,
			'header_img02' => $this->userConfig->header_img02,
			'header_url02' => $this->userConfig->header_url02,

			//販売店ごとの設定
			'oshirase_flg' => $this->userConfig->oshirase_flg,
			'bill_flg' => $this->userConfig->bill_flg,
			'max_button' => $this->userConfig->max_button,
			'dgf_flg' => $this->userConfig->dgf_flg,

			'query' => $this->spgskiguModel
				->where('misecd', $misecd)
				->where('usercd', $usercd)
				->orderBy('recno')
				->findAll($this->userConfig->meisai_rec, (int) $offset),
			// ->paginate($this->userConfig->meisai_rec,'default',(int)$offset / $this->userConfig->meisai_rec + 1),

			// 'pager' => $this->spgskiguModel->pager,
			'pager' => null,

		];

		// 取引履歴カウント
		$results = $this->spgskiguModel
			->where('misecd', $misecd)
			->where('usercd', $usercd)
			->findAll();
		$total = count($results);

		$data['list_total'] = $total;
		$data['list_limit'] = $this->userConfig->meisai_rec;
		$data['list_offset'] = ((int) $offset / $this->userConfig->meisai_rec) * $this->userConfig->meisai_rec;

		log_message('debug', '★Spgsinfoコントローラー ryokin_disp files=' . print_r($data['files'], true));
		log_message('debug', '★Spgsinfoコントローラー ryokin_disp spgsinfo_ryokin が呼び出されました。');

		return view('authview/spgsinfo_kigu', $data);

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	メール変更入力
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function mail_change()
	{

		log_message('debug', '★Spgsinfoコントローラー mail_change が呼び出されました。');

		if (session('mail')) {
			$wk_mail = trim(session('mail'));
		} else {
			$wk_mail = '';
		}

		$data = [

			'login_id' => session('login_id'),
			'login_pwd' => session('login_pwd'),
			'login_name' => session('login_name'),
			'login_misecd' => session('login_misecd'),
			'login_usercd' => session('login_usercd'),
			'dspusercd' => session('login_dspusercd'),
			'buttons' => session('buttons'),

			//ファイル情報を取得する
			'misecd' => session('login_misecd'),
			'usercd' => session('login_usercd'),
			'files' => session('files'),

			//画像ファイル
			'header_img01' => $this->userConfig->header_img01,
			'header_url01' => $this->userConfig->header_url01,
			'header_img02' => $this->userConfig->header_img02,
			'header_url02' => $this->userConfig->header_url02,

			//販売店ごとの設定
			'oshirase_flg' => $this->userConfig->oshirase_flg,
			'bill_flg' => $this->userConfig->bill_flg,
			'max_button' => $this->userConfig->max_button,
			'dgf_flg' => $this->userConfig->dgf_flg,

			'oldmail' => $wk_mail,
			'newmail' => '',
			'newmail2' => '',

			'errmsg' => '',
			'login_dspusercd' => session('login_dspusercd'),

		];

		log_message('debug', '★Spgsinfoコントローラー mail_change $wk_mail:' . $wk_mail);
		log_message('debug', '★Spgsinfoコントローラー mail_change empty($wk_mail):' . (empty($wk_mail) ? 'true' : 'false'));

		if (empty($wk_mail)) {
			return view('authview/spgsinfo_mail_entry', $data);
		} else {
			return view('authview/spgsinfo_mail_change', $data);
		}

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	メール登録更新
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function mailentry()
	{

		log_message('debug', '★Spgsinfoコントローラー mailentry が呼び出されました。');

		$data = [

			//ファイル情報を取得する
			'misecd' => session('login_misecd'),
			'usercd' => session('login_usercd'),
			'name' => session('login_name'),

			'login_id' => session('login_id'),
			'login_pwd' => session('login_pwd'),
			'login_name' => session('login_name'),
			'login_misecd' => session('login_misecd'),
			'login_usercd' => session('login_usercd'),
			'dspusercd' => session('login_dspusercd'),
			'login_dspusercd' => session('login_dspusercd'),
			'buttons' => session('buttons'),

			'files' => session('files'),

			//画像ファイル
			'header_img01' => $this->userConfig->header_img01,
			'header_url01' => $this->userConfig->header_url01,
			'header_img02' => $this->userConfig->header_img02,
			'header_url02' => $this->userConfig->header_url02,

			//販売店ごとの設定
			'oshirase_flg' => $this->userConfig->oshirase_flg,
			'bill_flg' => $this->userConfig->bill_flg,
			'max_button' => $this->userConfig->max_button,
			'dgf_flg' => $this->userConfig->dgf_flg,

		];


		// バリデーションルールの設定
		$validationRules = [
			'newmail' => 'trim|required|max_length[50]|matches[newmail2]|valid_email',
			'newmail2' => 'trim|required|max_length[50]|valid_email',
		];

		// ルールで設定したパラメータのバリデーションを実行
		if (!$this->validate($validationRules)) {
			log_message('debug', '★Spgsinfoコントローラー mailentry バリデーションエラー');

			$data['errmsg'] = '<span class="red">更新に失敗しました。<br>[入力内容をご確認下さい。]</span>';
			return view('authview/spgsinfo_mail_entry', $data);

		} else {

			log_message('debug', '★Spgsinfoコントローラー mailentry バリデーション成功');
			if ($this->mailupdate_ex()) {
				log_message('debug', '★Spgsinfoコントローラー mailentry メールアドレス登録成功');

				$data['errmsg'] = '<span class="blue">メールアドレスが登録されました。<br>登録されたメール宛てに確認メールをお届けしています。</span>';
				$data['oldmail'] = $this->request->getpost('oldmail');
				$data['newmail'] = $this->request->getpost('newmail');
				$data['newmail2'] = $this->request->getpost('newmail2');
				$data['sndymd'] = 0;

				return view('authview/spgsinfo_mail_entry', $data);
			} else {
				log_message('debug', '★Spgsinfoコントローラー mailentry メールアドレス登録失敗');

				$data['errmsg'] = '<span class="red">更新に失敗しました。<br>[入力内容をご確認下さい。]</span>';
				return view('authview/spgsinfo_mail_entry', $data);
			}

		}

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	メール登録更新
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function mailupdate()
	{

		log_message('debug', '★Spgsinfoコントローラー mailupdate が呼び出されました。');

		$data = [

			//ファイル情報を取得する
			'misecd' => session('login_misecd'),
			'usercd' => session('login_usercd'),
			'name' => session('login_name'),

			'login_id' => session('login_id'),
			'login_pwd' => session('login_pwd'),
			'login_name' => session('login_name'),
			'login_misecd' => session('login_misecd'),
			'login_usercd' => session('login_usercd'),
			'dspusercd' => session('login_dspusercd'),
			'login_dspusercd' => session('login_dspusercd'),
			'buttons' => session('buttons'),

			'files' => session('files'),

			//画像ファイル
			'header_img01' => $this->userConfig->header_img01,
			'header_url01' => $this->userConfig->header_url01,
			'header_img02' => $this->userConfig->header_img02,
			'header_url02' => $this->userConfig->header_url02,

			//販売店ごとの設定
			'oshirase_flg' => $this->userConfig->oshirase_flg,
			'bill_flg' => $this->userConfig->bill_flg,
			'max_button' => $this->userConfig->max_button,
			'dgf_flg' => $this->userConfig->dgf_flg,

		];


		// バリデーションルールの設定
		$validationRules = [
			'newmail' => 'trim|required|max_length[50]|matches[newmail2]|valid_email',
			'newmail2' => 'trim|required|max_length[50]|valid_email',
		];

		// ルールで設定したパラメータのバリデーションを実行
		if (!$this->validate($validationRules)) {
			// バリデーションエラー
			log_message('debug', '★Spgsinfoコントローラー mailupdate バリデーションエラー');

			$data['oldmail'] = session('mail');
			$data['errmsg'] = '<span class="red">更新に失敗しました。<br>[入力内容をご確認下さい。]</span>';
			return view('authview/spgsinfo_mail_change', $data);

		} else {
			// バリデーション成功
			log_message('debug', '★Spgsinfoコントローラー mailupdate バリデーション成功');
			if ($this->mailupdate_ex()) {
				log_message('debug', '★Spgsinfoコントローラー mailupdate メールアドレス登録成功');

				$data['errmsg'] = '<span class="blue">メールアドレスが登録されました。<br>登録されたメール宛てに確認メールをお届けしています。</span>';
				$data['oldmail'] = $this->request->getpost('newmail');
				$data['newmail'] = '';
				$data['newmail2'] = '';
				$data['sndymd'] = 0;

				return view('authview/spgsinfo_mail_change', $data);
			} else {
				log_message('debug', '★Spgsinfoコントローラー mailupdate メールアドレス登録失敗');

				$data['oldmail'] = session('mail');
				$data['errmsg'] = '<span class="red">更新に失敗しました。<br>[入力内容をご確認下さい。]</span>';
				return view('authview/spgsinfo_mail_change', $data);
			}

		}

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	メール変更更新実行箇所
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function mailupdate_ex()
	{
		log_message('debug', '★Spgsinfoコントローラー mailupdate_ex が呼び出されました。');

		$db = \Config\Database::connect();
		$db->transStart();   // トランザクション開始

		$newmail = $this->request->getPost('newmail');
		$oldmail = session('mail');
		$misecd = session('login_misecd');
		$usercd = session('login_usercd');

		$result = false;

		try {
			// ①spgsuser テーブル更新
			$result1 = $this->spgsuserModel
				->where('misecd', $misecd)
				->where('usercd', $usercd)
				->set(['mail' => $newmail])
				->update();

			log_message('debug', '★Spgsinfoコントローラー mailupdate_ex spgsuserModel update result=' . print_r($result1, true));

			// ②spgsmail テーブルに履歴 INSERT
			$result2 = $this->spgsmailModel->insert([
				'misecd' => $misecd,
				'usercd' => $usercd,
				'oldmail' => $oldmail,
				'newmail' => $newmail,
				'entryymd' => date("Ymd"),
				'entrytime' => date("His"),
				'sndymd' => 0,
				'sndtime' => 0
			]);

			log_message('debug', '★Spgsinfoコントローラー mailupdate_ex spgsmailModel insert result=' . print_r($result2, true));

			if ($result1 && $result2) {
				$result = true;
			} else {
				$result = false;
			}

		} catch (\Exception $e) {
			log_message('error', '★Spgsinfoコントローラー mailupdate_ex 例外発生: ' . $e->getMessage());
			$result = false;
		}

		if (!$result) {
			log_message('debug', '★Spgsinfoコントローラー mailupdate_ex 更新処理に失敗しました。');
			$db->transRollback(); // トランザクションをロールバック

			// 失敗理由をログに出力
			$error = $db->error();
			log_message('error', '★Spgsinfoコントローラー mailupdate_ex 失敗のためロールバックしました。');
			log_message('error', '★DB Error Code: ' . $error['code']);
			log_message('error', '★DB Error Message: ' . $error['message']);

			// モデルのエラーも確認
			if ($this->spgsuserModel->errors()) {
				log_message('error', '★spgsuserModel validation errors: ' . print_r($this->spgsuserModel->errors(), true));
			}
			if ($this->spgsmailModel->errors()) {
				log_message('error', '★spgsmailModel validation errors: ' . print_r($this->spgsmailModel->errors(), true));
			}

			return false;
		}

		$mail_send_result = $this->set_message_mailupdate($newmail); // 確認メール送信

		if (!$mail_send_result) {
			log_message('error', '★Spgsinfoコントローラー mailupdate_ex 確認メールの送信に失敗しました。');
			$db->transRollback(); // トランザクションをロールバック
			return false;
		} else {
			log_message('debug', '★Spgsinfoコントローラー mailupdate_ex 確認メールの送信に成功しました。');
			$db->transComplete();   // トランザクション終了
		}

		// 成功時はセッションも更新
		session()->set('mail', $newmail);

		log_message('debug', '★Spgsinfoコントローラー mailupdate_ex メールアドレスを更新しました。');
		return true;

	}

	/**
	 * メールアドレス変更通知メールのデータを準備して送信
	 * 
	 * @param string $newmail 新しいメールアドレス
	 * @param string $name ユーザー名
	 * @return bool 送信成功/失敗
	 */
	public function set_message_mailupdate($newmail)
	{

		log_message('debug', '★Spgsinfoコントローラー set_message_mailupdate が呼び出されました。');

		# 変更したメールアドレス宛てに確認メールを送る。
		# メールのヘッダを設定します。Bccで同じメールを管理者にも送るようにします。
		$mail = [
			'from_name' => $this->userConfig->misename,
			'from' => $this->userConfig->mailfrom,
			'bcc' => $this->userConfig->mailfrom,
			'to' => $this->request->getPost('newmail'),
			'subject' => 'メール登録のご確認',
		];

		$name = session('login_name');

		# ヒアドキュメントでメール本文を作成します。
		$mail['body'] = <<<END
{$name}　様
いつもご利用いただき誠にありがとうございます。
この度、お客様よりメール登録の手続きがありましたので、ご確認のための
メール送信をさせていただいております。
\n
※万が一お心当たりのない場合は、他の方がメールアドレスの入力を誤られたものと考えられます。
登録されました情報を削除させていただきますので、お手数ではございますが下記のお問い合わせ先までご連絡ください。
―――――――――――――――――――
■お問い合わせは■
―――――――――――――――――――
MAIL: {$this->userConfig->mailfrom}
TEL : {$this->userConfig->misetel}
なにかご不明な点がありましたら、お気軽にご連絡ください。
──────────────────────────────────────
発行元：{$this->userConfig->misename}　{$this->userConfig->miseurl}
本メールの無断転載はご遠慮ください。
本メールへの返信には回答を差しあげておりません。ご了承ください。
──────────────────────────────────────
Copyright(C) 2017 {$this->userConfig->copywrite} All Rights Reserved.
END;

		# sendmail()メソッドを呼び出し、実際にメールを送信します。
		# メール送信に成功すれば、TRUEを返します。
		return $this->sendmail($mail);

	}

	/**
	 * メール送信処理
	 * 
	 * @param array $mail メール情報配列
	 *   - from_name: 送信者名
	 *   - from: 送信元メールアドレス
	 *   - to: 宛先メールアドレス
	 *   - bcc: BCC（オプション）
	 *   - subject: 件名
	 *   - body: 本文
	 * @return bool 送信成功/失敗
	 */
	public function sendmail(array $mail): bool
	{

		log_message('debug', '★Spgsinfoコントローラー sendmail が呼び出されました。');

		# Emailクラスを取得
		$email = \Config\Services::email();

		// smtp設定(CI4はキー名が大文字)
		$config = [
			'protocol' => $this->userConfig->smtp_protocol,
			'SMTPHost' => $this->userConfig->smtp_host,
			'SMTPPort' => (int) $this->userConfig->smtp_port,
			'SMTPUser' => $this->userConfig->smtp_user,
			'SMTPPass' => $this->userConfig->smtp_pass,
			'charset' => $this->userConfig->smtp_charset,
			'wordWrap' => false,
			'mailType' => 'text',
			// TLS認証を使用する場合は以下のコメントを外してください
			'SMTPCrypto' => '', // 明示的にクリアしないと、tlsがデフォルトで設定される
			// 'SMTPCrypto' => 'tls',
			// 'SMTPTimeout' => 30,  // タイムアウトを30秒に設定
			// 'newline'    => "\r\n",
			// 'CRLF'       => "\r\n",
		];

		$email->initialize($config);

		// メールの内容を変数に代入します。
		$from_name = $mail['from_name'];
		$from = $mail['from'];
		$to = $mail['to'];
		$bcc = $mail['bcc'];
		$subject = $mail['subject'];
		$body = $mail['body'];
		// 特殊文字の置換
		$body = str_replace('㈱', '（株）', $body);
		$body = str_replace('㈲', '（有）', $body);
		$body = str_replace('①', '（１）', $body);
		$body = str_replace('②', '（２）', $body);
		$body = str_replace('③', '（３）', $body);
		$body = str_replace('④', '（４）', $body);
		$body = str_replace('⑤', '（５）', $body);
		$body = str_replace('⑥', '（６）', $body);
		$body = str_replace('⑦', '（７）', $body);
		$body = str_replace('⑧', '（８）', $body);
		$body = str_replace('⑨', '（９）', $body);
		$body = str_replace('⑩', '（10）', $body);

		# メールヘッダのMIMEエンコードおよび文字エンコードの変換をします。
		$from_name = mb_encode_mimeheader($from_name, $config['charset']);
		# 本文の文字エンコードを変換します。
		$subject = mb_convert_encoding($subject, $config['charset'], 'UTF-8');
		$body = mb_convert_encoding($body, $config['charset'], 'UTF-8');

		# 差出人、あて先、Bcc、件名、本文を設定します。
		$email->setFrom($from, $from_name);
		$email->setTo($to);
		if (!empty($cc)) {
			$email->setBCC($cc);
		}
		if (!empty($bcc)) {
			$email->setBCC($bcc);
		}
		$email->setSubject($subject);
		$email->setMessage($body);

		# send()メソッドで実際にメールを送信します。
		if ($email->send()) {
			log_message('debug', '★Spgsinfoコントローラー sendmail メール送信に成功しました。');
			return TRUE;
		} else {
			log_message('error', '★Spgsinfoコントローラー sendmail メール送信に失敗しました。');
			log_message('error', '★Email Debug: ' . $email->printDebugger(['headers', 'subject', 'body']));
			return FALSE;
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	パスワード変更入力
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function pwd_change()
	{

		log_message('debug', '★Spgsinfoコントローラー pwd_change が呼び出されました。');

		$data = [

			//ファイル情報を取得する
			'misecd' => session('login_misecd'),
			'usercd' => session('login_usercd'),
			'name' => session('login_name'),

			'login_id' => session('login_id'),
			'login_pwd' => session('login_pwd'),
			'login_name' => session('login_name'),
			'login_misecd' => session('login_misecd'),
			'login_usercd' => session('login_usercd'),
			'dspusercd' => session('login_dspusercd'),
			'login_dspusercd' => session('login_dspusercd'),
			'buttons' => session('buttons'),

			'files' => session('files'),

			//画像ファイル
			'header_img01' => $this->userConfig->header_img01,
			'header_url01' => $this->userConfig->header_url01,
			'header_img02' => $this->userConfig->header_img02,
			'header_url02' => $this->userConfig->header_url02,

			//販売店ごとの設定
			'oshirase_flg' => $this->userConfig->oshirase_flg,
			'bill_flg' => $this->userConfig->bill_flg,
			'max_button' => $this->userConfig->max_button,
			'dgf_flg' => $this->userConfig->dgf_flg,

			'errmsg' => '',

			'oldpwd' => session('login_pwd'),
			'newpwd' => '',
			'newpwd2' => '',

		];

		return view('authview/spgsinfo_pwd_change', $data);

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	パスワード変更更新
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function pwdupdate()
	{
		$data = [

			//ファイル情報を取得する
			'misecd' => session('login_misecd'),
			'usercd' => session('login_usercd'),
			'name' => session('login_name'),

			'login_id' => session('login_id'),
			'login_pwd' => session('login_pwd'),
			'login_name' => session('login_name'),
			'login_misecd' => session('login_misecd'),
			'login_usercd' => session('login_usercd'),
			'dspusercd' => session('login_dspusercd'),
			'login_dspusercd' => session('login_dspusercd'),
			'buttons' => session('buttons'),

			'files' => session('files'),

			//画像ファイル
			'header_img01' => $this->userConfig->header_img01,
			'header_url01' => $this->userConfig->header_url01,
			'header_img02' => $this->userConfig->header_img02,
			'header_url02' => $this->userConfig->header_url02,

			//販売店ごとの設定
			'oshirase_flg' => $this->userConfig->oshirase_flg,
			'bill_flg' => $this->userConfig->bill_flg,
			'max_button' => $this->userConfig->max_button,
			'dgf_flg' => $this->userConfig->dgf_flg,

			'errmsg' => '',

			'oldpwd' => $this->request->getpost('oldpwd'),
			'newpwd' => $this->request->getpost('newpwd'),
			'newpwd2' => $this->request->getpost('newpwd2'),
			'sndymd' => 0,

		];

		log_message('debug', '★Spgsinfoコントローラー pwdupdate DBの現行パスワード:' . session('login_pwd'));
		log_message('debug', '★Spgsinfoコントローラー pwdupdate 入力された現行パスワード:' . $this->request->getpost('oldpwd'));
		log_message('debug', '★Spgsinfoコントローラー pwdupdate 入力された新パスワード:' . $this->request->getpost('newpwd'));
		log_message('debug', '★Spgsinfoコントローラー pwdupdate 入力された新パスワード2:' . $this->request->getpost('newpwd2'));

		// 現行のパスワードが合致するか確認
		if (trim($data['oldpwd']) != trim(session('login_pwd'))) {
			log_message('debug', '★Spgsinfoコントローラー pwdupdate 現行パスワード不一致');

			$data['errmsg'] = '<span class="red">更新に失敗しました。<br>[入力内容をご確認下さい。]</span>';
			return view('authview/spgsinfo_pwd_change', $data);
		}

		// バリデーションルールの設定
		$validationRules = [
			'oldpwd' => 'trim|required', // 現行パスワード
			'newpwd' => 'trim|required|min_length[6]|max_length[20]|matches[newpwd2]', // 新しいパスワード
			'newpwd2' => 'trim|required|min_length[6]|max_length[20]', // 新しいパスワードの確認
		];

		// ルールで設定したパラメータのバリデーションを実行
		if (!$this->validate($validationRules)) {
			// バリデーションエラー
			log_message('debug', '★Spgsinfoコントローラー pwdupdate バリデーションエラー');

			$data['oldpwd'] = session('pwd');
			$data['errmsg'] = '<span class="red">更新に失敗しました。<br>[入力内容をご確認下さい。]</span>';
			return view('authview/spgsinfo_pwd_change', $data);

		} else {
			// バリデーション成功
			log_message('debug', '★Spgsinfoコントローラー pwdupdate バリデーション成功');
			if ($this->pwdupdate_ex()) {
				log_message('debug', '★Spgsinfoコントローラー pwdupdate パスワード登録成功');

				$data['errmsg'] = '<span class="blue">パスワードが変更されました。</span>';
				$data['oldpwd'] = $this->request->getpost('newpwd');
				$data['newpwd'] = '';
				$data['newpwd2'] = '';
				$data['sndymd'] = 0;

				return view('authview/spgsinfo_pwd_change', $data);
			} else {
				log_message('debug', '★Spgsinfoコントローラー pwdupdate パスワード登録失敗');

				$data['oldpwd'] = session('pwd');
				$data['errmsg'] = '<span class="red">更新に失敗しました。<br>[入力内容をご確認下さい。]</span>';
				return view('authview/spgsinfo_pwd_change', $data);
			}

		}

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	////	パスワード変更更新実行
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	public function pwdupdate_ex()
	{
		log_message('debug', '★Spgsinfoコントローラー pwdupdate_ex が呼び出されました。');

		$db = \Config\Database::connect();
		$db->transStart();   // トランザクション開始

		$newpwd = $this->request->getPost('newpwd');
		$oldpwd = session('login_pwd');
		$misecd = session('login_misecd');
		$usercd = session('login_usercd');
		log_message('debug', '★Spgsinfoコントローラー pwdupdate_ex $newpwd:' . $newpwd);

		$result = false;

		try {
			log_message('debug', '★Spgsinfoコントローラー pwdupdate_ex misecd=' . $misecd);
			log_message('debug', '★Spgsinfoコントローラー pwdupdate_ex usercd=' . $usercd);

			// ①spgsuser テーブル更新
			$result1 = $this->spgsuserModel
				->where('misecd', $misecd)
				->where('usercd', $usercd)
				->set(['pwd' => $newpwd])
				->update();

			log_message('debug', '★Spgsinfoコントローラー pwdupdate_ex spgsuserModel update result=' . print_r($result1, true));

			// ②spgspwd テーブルに履歴 INSERT
			$result2 = $this->spgspwdModel->insert([
				'misecd' => $misecd,
				'usercd' => $usercd,
				'oldpwd' => $oldpwd,
				'newpwd' => $newpwd,
				'entryymd' => date("Ymd"),
				'entrytime' => date("His"),
				'sndymd' => 0,
				'sndtime' => 0
			]);

			log_message('debug', '★Spgsinfoコントローラー pwdupdate_ex spgspwdModel insert result=' . print_r($result2, true));

			if ($result1 && $result2) {
				$result = true;
			} else {
				$result = false;
			}

		} catch (\Exception $e) {
			log_message('error', '★Spgsinfoコントローラー pwdupdate_ex 例外発生: ' . $e->getMessage());
			$result = false;
		}

		if (!$result) {
			log_message('debug', '★Spgsinfoコントローラー pwdupdate_ex 更新処理に失敗しました。');
			$db->transRollback(); // トランザクションをロールバック

			// 失敗理由をログに出力
			$error = $db->error();
			log_message('error', '★Spgsinfoコントローラー pwdupdate_ex 失敗のためロールバックしました。');
			log_message('error', '★DB Error Code: ' . $error['code']);
			log_message('error', '★DB Error Message: ' . $error['message']);

			// モデルのエラーも確認
			if ($this->spgsuserModel->errors()) {
				log_message('error', '★spgsuserModel validation errors: ' . print_r($this->spgsuserModel->errors(), true));
			}
			if ($this->spgspwdModel->errors()) {
				log_message('error', '★spgspwdModel validation errors: ' . print_r($this->spgspwdModel->errors(), true));
			}

			return false;
		}

		// 成功時はセッションも更新
		session()->set('pwd', $newpwd);
		$db->transComplete();   // トランザクション終了

		log_message('debug', '★Spgsinfoコントローラー pwdupdate_ex メールアドレスを更新しました。');
		return true;

	}

	////////////////////////////////////////////////////////////////////////////////////////
	//		決済サイトに移動
	////////////////////////////////////////////////////////////////////////////////////////
	public function dgfpayment()
	{

		log_message('debug', '★Spgsinfoコントローラー dgfpayment が呼び出されました。');

		$misecd = session('login_misecd');
		$usercd = session('login_usercd');

		$ret = $this->spgskensinModel->readData($misecd, $usercd);

		if (isset($ret["seikyukin"])) {
			log_message('debug', '★Spgsinfoコントローラー dgfpayment　seikyukin = ' . $ret["seikyukin"]);
			if ($ret["seikyukin"] != "0") {

				$wkuser = $this->spgsuserModel->readData($misecd, $usercd);
				$seikin = trim($ret["seikyukin"]);
				$seikin = str_replace("￥", "", $seikin);
				$seikin = str_replace(",", "", $seikin);
				$return_url = $this->userConfig->return_url;
				$kekka_snd_url = $this->userConfig->kekka_snd_url;

				// return_tokenを生成してセッションに保存
				$return_token = md5(uniqid(mt_rand(), TRUE));
				session()->set('return_token', $return_token);

				$wkdat = [
					'misecd' => session('login_misecd'),
					'usercd' => session('login_usercd'),
					'order_id' => 'ing_' . substr("0000" . $misecd, -4) . '-' . $usercd . '_' . $seikin . '_' . date('YmdHis') . 'Z0',
					'stoc_name' => 'ＬＰガス',
					'kingaku' => str_replace(",", "", $ret["seikyukin"]),
					'type' => '31',
					'tel' => $wkuser["tel"],
					'name' => $wkuser["name"],
					'mail' => $wkuser["mail"],
					'return_url' => $return_url,
					'return_token' => $return_token,
					'kekka_snd_url' => $kekka_snd_url,
				];

				log_message('debug', '★Spgsinfoコントローラー dgfpayment $wkdat:' . print_r($wkdat, true));
				$ret2 = service('dgfpayment')->GetPaymentSiteAdress($wkdat);
				if ($ret2 === false) {
					// アドレス取得失敗
					log_message('debug', '★Spgsinfoコントローラー dgfpayment アドレス取得失敗　$ret2:' . print_r($ret2, true));
					return redirect()->to('spgsinfo/kensin_disp');
				} else {
					log_message('debug', '★Spgsinfoコントローラー dgfpayment アドレス取得成功　$ret2:' . print_r($ret2, true));
				}

				// return redirect()->to($ret2->url);　// 外部サイトにリダイレクトできない
				header('Location: ' . $ret2->url);
				exit;
			} else {
				// 請求金額0円の為、戻る
				log_message('debug', '★Spgsinfoコントローラー dgfpayment redirect 請求金額0円');
				// redirect(base_url()."spgsinfo/kensin_disp" );
				return redirect()->to('spgsinfo/kensin_disp');
			}
		} else {
			// データ抽出不具合の為戻る
			log_message('debug', '★Spgsinfoコントローラー dgfpayment redirect データ抽出不具合');
			return redirect()->to('spgsinfo/kensin_disp');
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////
	//		決済サイトから移動
	////////////////////////////////////////////////////////////////////////////////////////
	public function Redgfpayment(string $text)
	{

		log_message('debug', '★Spgsinfoコントローラー Redgfpayment が呼び出されました。');

		// 引数(getクエリ)取得
		$args = service('dgfpayment')->GetArgs($text);
		log_message('debug', '★Spgsinfoコントローラー Redgfpayment $args=' . print_r($args, true));

		// 処理開始 //
		$ret = service('dgfpayment')->UpdateResultForSpgsInfo($args);
		log_message('debug', '★Spgsinfoコントローラー Redgfpayment $ret=' . print_r($ret, true));

		return redirect()->to('spgsinfo/kensin_disp');

	}

	////////////////////////////////////////////////////////////////////////////////////////
	//		決済サイトから結果データ取得
	////////////////////////////////////////////////////////////////////////////////////////
	public function RcvKekka(string $text)
	{

		log_message('debug', '★Spgsinfoコントローラー Redgfpayment が呼び出されました。');

		// Basic認証
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			// header("WWW-Authenticate: Basic realm=\"My Realm\"");
			// header("HTTP/1.0 401 Unauthorized");
			// echo "ユーザーがない時に送信されるテキスト\n";
			// exit;
			return $this->response
				->setStatusCode(401)
				->setHeader('WWW-Authenticate', 'Basic realm="My Realm"')
				->setBody("ユーザーがない時に送信されるテキスト\n");
		} else {

			if ($_SERVER['PHP_AUTH_USER'] != 'U12345678901234567890' || $_SERVER['PHP_AUTH_PW'] != 'p98765432109876543210') {
				return $this->response
					->setStatusCode(401)
					->setHeader('WWW-Authenticate', 'Basic realm="My Realm"')
					->setBody("ユーザーが不一致の場合に送信されるテキスト\n");
			}
		}


		// 引数(getクエリ)取得
		$args = service('dgfpayment')->GetArgs($text);
		log_message('debug', '★Spgsinfoコントローラー Redgfpayment $args=' . print_r($args, true));


		// 処理開始 //
		$ret = service('dgfpayment')->UpdateResultForSpgsInfo($args);
		log_message('debug', '★Spgsinfoコントローラー Redgfpayment $ret=' . print_r($ret));

		return $this->response->setBody($ret['kekka']);
		//echo $_SERVER['PHP_AUTH_USER'];
		//echo $_SERVER['PHP_AUTH_PW'];
	}

	////////////////////////////////////////////////////////////////////////////////////////
	//		領収書取得
	////////////////////////////////////////////////////////////////////////////////////////
	public function getReceipt()
	{

		log_message('debug', '★Spgsinfoコントローラー Redgfpayment が呼び出されました。');

		// $usercd		= $this->session->userdata('login_usercd');
		// $misecd		= $this->session->userdata('login_misecd');
		$usercd = session('login_usercd');
		$misecd = session('login_misecd');

		// // ライブラリ宣言
		// $this->load->library('dgfpayment');

		// 検針情報取得
		// $ret = $this->spgsinfo_model->get_kensin_info($misecd, $usercd);
		$ret = $this->spgskensinModel->readData($misecd, $usercd);
		if (isset($ret["seikyukin"])) {
			// ハッシュ値　取得
			$wkdat['hash'] = $ret['hash'];
			// 入金日取得
			$nyukin_ymd = '';
			for ($wki = 1; $wki < 7; $wki++) {
				if ($ret['toriname' . $wki] == '御入金') {
					$nyukin_ymd = $ret['toriymd' . $wki];
				}
			}
			if ($nyukin_ymd == '') {
				$nyukin_ymd = date('Ymd');
			}
			$wkdat['order_id'] = 'ing_' . substr("0000" . $misecd, -4) . '-' . $usercd . '_' . str_replace(",", "", $ret["seikyukin"]) . '_' . $nyukin_ymd;
			// 処理開始 //
			service('dgfpayment')->GetReceipt($wkdat);
			exit;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////
	//		特商法
	////////////////////////////////////////////////////////////////////////////////////////
	public function toksho()
	{
		$data['misename'] = $this->userConfig->misename;
		$data['misetel'] = $this->userConfig->misetel;

		return view('public/spgsinfo_toksho', $data);
	}

}
