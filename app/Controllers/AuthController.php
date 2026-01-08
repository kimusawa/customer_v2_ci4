<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Security;

class AuthController extends BaseController
{

    public function __construct()
    {
    }

    public function index()
    {
        log_message('debug', 'AuthController index method called');
        // バリデーションOKなら認証処理
        return $this->login();
    }

    public function login()
    {
        log_message('debug', '★Authコントローラー Login が呼び出されました。');

        $data['header_img01'] = $this->userConfig->header_img01;
        $data['header_url01'] = $this->userConfig->header_url01;
        $data['header_img02'] = $this->userConfig->header_img02;
        $data['header_url02'] = $this->userConfig->header_url02;
    	$data['dgf_flg']	= $this->userConfig->dgf_flg;

        return view('authview/spgsinfo_login', $data);
    }

    public function loginCheck()
    {
        log_message('debug', '★Authコントローラー loginCheck が呼び出されました。');
        $rules = [
            'login_id' => 'required',
            'login_pwd' => 'required',
        ];

        // バリデーションチェック
        if (! $this->validate($rules)) {
            return view('authview/spgsinfo_login', [
                'validation' => $this->validator
            ]);
        }

        // バリデーションOKなら認証処理
        return $this->loginAuth();
    
    }

    public function loginAuth()
    {
        log_message('debug', '★Authコントローラー loginAuth が呼び出されました。');

        $security = new Security();
        $errorMsg='登録がありません';
        $result = false;

        $login_id = $this->request->getPost('login_id');
        $pwd = $this->request->getPost('login_pwd');
        $login_id = trim($login_id);
        $pwd = trim($pwd);

        log_message('debug', '★Authコントローラー loginAuth login_id =' . $login_id);
        log_message('debug', '★Authコントローラー loginAuth login_pwd =' . $pwd);

        $spgsuser = $this->spgsuserModel
            ->where('id', $login_id)
            ->first();

        if ($spgsuser) {
            log_message('debug', '★Authコントローラー idがヒットしました。');
            if ($this->userConfig->pwd_hash_flg == 1) {
            // パスワードのハッシュを使用する場合
                $pepper = $security->pepper;
                $pepperedInput = $pwd . $pepper;

                if (password_verify($pepperedInput, $spgsuser['pwd'])) {

                    $result = true;
                }
            } else {
                // パスワードのハッシュを使用しない場合
                log_message('debug', '★Authコントローラー pwd=' . $spgsuser['pwd']);
                if (trim($spgsuser['pwd']) === $pwd) {
                    $result = true;
                } 

            }

            if ($result === true) {

                // 認証OK
                // セッションIDを再生成
                session()->regenerate();

                // 設定をセッションに保存
                session()->set('userConfig', $this->userConfig);
                log_message('debug', '★Authコントローラー userConfig=' . print_r($this->userConfig, true));

                // 1〜8のボタンをループで取得
                for ($i = 1; $i <= $this->userConfig->max_button; $i++) {
                    $this->buttons[] = $this->userConfig->{'button'.$i}; // ボタン設定読込
                }

                // View に渡す
                $data = [
                    'login_name' => trim($spgsuser['name']),
                    'login_misecd' => trim($spgsuser['misecd']),
                    'login_usercd' => trim($spgsuser['usercd']),
                    'login_dspusercd'	=> trim($spgsuser['dspusercd']),
                    'misecd' => trim($spgsuser['misecd']),
                    'usercd' => trim($spgsuser['usercd']),

                    //画像ファイル
                    'header_img01'	=> $this->userConfig->header_img01,
                    'header_url01'	=> $this->userConfig->header_url01,
                    'header_img02'	=> $this->userConfig->header_img02,
                    'header_url02'	=> $this->userConfig->header_url02,

                    //販売店ごとの設定
                	'oshirase_flg'=> $this->userConfig->oshirase_flg,
                	'bill_flg'=> $this->userConfig->bill_flg,
                	'max_button'=> $this->userConfig->max_button,
                ];

                $data['buttons'] = $this->buttons;                
                session()->set('buttons', $this->buttons);

                $file_data = $this->get_file_data($data);
                log_message('debug', 'AuthController $file_data=' . print_r($file_data, true));
                $data['files'] = $file_data;                

                // セッションデータを設定
                $userdata = [
                    'user_logged_in' => true, // authguardで使用
                    'login_id' => $spgsuser['id'],
                    'login_pwd' => $spgsuser['pwd'],
                    'login_name' => $spgsuser['name'],
                    'login_dspusercd'	=> $spgsuser['dspusercd'],
                    'mail' => $spgsuser['mail'],
                    'login_misecd' => $spgsuser['misecd'],
                    'login_usercd' => $spgsuser['usercd'],
                    'files' => $file_data,
                ];
                session()->set($userdata);
                
                // // 初期ログイン（メールアドレス無）はメール登録へ
				// $mail = trim($spgsuser['mail']);

//				if(empty($mail))
//				{
//					$this->session->set_userdata('ticket', $this->ticket);
//					$data['login_id']	= $this->session->userdata('login_id');
//					$data['login_pwd']	= $this->session->userdata('login_pwd');
//					$data['header_img01']=$this->header_img01;
//					$data['header_url01']=$this->header_url01;
//					$data['header_img02']=$this->header_img02;
//					$data['header_url02']=$this->header_url02;
//					$this->load->view('spgsinfo_mail_entry',$data);
//				}
//				else
//				{
                return view('authview/spgsinfo_menu', $data); 
//				}
            }else{
                log_message('debug', '★Authコントローラー loginAuthでパスワードがヒットしませんでした。');
                return redirect()->to('spgsinfo')->withInput()->with('error', $errorMsg);
            }

        } else {
            log_message('debug', '★Authコントローラー loginAuthでIDがヒットしませんでした。');
            return redirect()->to('spgsinfo')->withInput()->with('error', $errorMsg);
        }
    }

    public function get_file_data($data){

        log_message('debug', 'AuthController get_file_data関数 が呼び出されました。');

        $cds = $this->userConfig->code_style;

        $misecd		= $data['misecd'];
        $usercd		= $data['usercd'];
        if($cds == 0 && substr($usercd,-3,3) == "000"){
            $usercd = substr($usercd,0,6);
        }
        $ret = [];
        $filepath = WRITEPATH . "seikyu/" . hash("md5", $usercd, false);
        $filepath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filepath);
        log_message('debug', 'AuthController get_file_data関数 $filepath=' . $filepath);
        $ary_files = glob($filepath.'/*.PDF');
        rsort($ary_files);
        $maxsuu = count($ary_files);
        if($maxsuu >6){
            $maxsuu = 6;
        }
        log_message('debug', 'AuthController get_file_data関数 $maxsuu=' . $maxsuu);
        for($wki=0;$wki<$maxsuu;$wki++){
            // ファイル名から年月取得
            $filename = basename($ary_files[$wki]);
            $names = explode("-",$filename);
            $ymd = $names[2];
            $y = substr($ymd,0,4);
            $m = substr($ymd,4,2);
            $po = strpos($ary_files[$wki],'/seikyu/');
            $file_path = substr($ary_files[$wki],$po + 8);
            $ret[$wki] = array('path'=>$file_path,'y'=>$y, 'm'=>$m);
        }
        return $ret;
    }

    public function logout()
    {
        log_message('debug', 'AuthController logoutが呼ばれました。');
        session()->destroy();
        return redirect()->to('spgsinfo')->withInput()->with('error', 'ログアウトしました。');
    }

}
