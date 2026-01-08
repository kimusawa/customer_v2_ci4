<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Security;

class AdminAuthController extends BaseController
{

    public function __construct()
    {
    }

    public function index()
    {
        log_message('debug', 'AdminAuthController index method called');
        return $this->login();
    }

    public function login()
    {

        log_message('debug', '★AdminAuthコントローラー Login が呼び出されました。');

        $data['header_img01'] = $this->adminConfig->header_img01;
        $data['header_url01'] = $this->adminConfig->header_url01;
        $data['header_img02'] = $this->adminConfig->header_img02;
        $data['header_url02'] = $this->adminConfig->header_url02;

        return view('adminview/spgsadmin_login', $data);
    }

    public function loginCheck()
    {
        log_message('debug', '★AdminAuthコントローラー loginCheck が呼び出されました。');

        session()->destroy();

        $rules = [
            'login_id' => 'required',
            'login_pwd' => 'required',
        ];

        // バリデーションチェック
        if (!$this->validate($rules)) {
            return view('adminview/spgsadmin_login', [
                'validation' => $this->validator
            ]);
        }

        // バリデーションOKなら認証処理
        return $this->loginAuth();

    }

    public function loginAuth()
    {
        log_message('debug', '★AdminAuthコントローラー loginAuth が呼び出されました。');

        $security = new Security();
        $errorMsg = '登録がありません';
        $result = false;

        $login_id = $this->request->getPost('login_id');
        $pwd = $this->request->getPost('login_pwd');
        $login_id = trim($login_id);
        $pwd = trim($pwd);

        log_message('debug', '★AdminAuthコントローラー loginAuth login_id =' . $login_id);
        log_message('debug', '★AdminAuthコントローラー loginAuth login_pwd =' . $pwd);

        $spgsuser = $this->spgskanriloginModel
            ->where('loginid', $login_id)
            ->first();

        if ($spgsuser && trim($spgsuser['loginid']) === trim($login_id)) { // 大文字小文字も区別するため
            log_message('debug', '★AdminAuthコントローラー idがヒットしました。');
            if ($this->adminConfig->pwd_hash_flg == 1) {
                // パスワードのハッシュを使用する場合
                $pepper = $security->pepper;
                $pepperedInput = $pwd . $pepper;

                if (password_verify($pepperedInput, $spgsuser['loginpwd'])) {

                    $result = true;
                }
            } else {
                // パスワードのハッシュを使用しない場合
                log_message('debug', '★AdminAuthコントローラー pwd=' . $spgsuser['loginpwd']);
                if (trim($spgsuser['loginpwd']) === $pwd) {
                    $result = true;
                }

            }

            if ($result === true) {

                // 認証OK
                // セッションIDを再生成
                session()->regenerate();

                // 設定をセッションに保存
                session()->set('adminConfig', $this->adminConfig);
                log_message('debug', '★AdminAuthコントローラー adminConfig=' . print_r($this->adminConfig, true));

                // 1〜8のボタンをループで取得
                for ($i = 1; $i <= $this->adminConfig->max_button; $i++) {
                    $this->buttons[] = $this->adminConfig->{'button' . $i}; // ボタン設定読込
                }

                // セッションデータを設定
                $userdata = [
                    'admin_logged_in' => true, // authguardで使用
                    'login_id' => $spgsuser['loginid'],
                    'login_pwd' => $spgsuser['loginpwd'],
                    'login_name' => $spgsuser['name'],
                    'login_grant' => $spgsuser['grantno'],
                    'login_misecd' => $spgsuser['misecd'],
                ];
                session()->set($userdata);

                log_message('debug', '★AdminAuthコントローラー loginAuth 認証成功。セッションデータ=' . print_r($userdata, true));

            } else {
                log_message('debug', '★AdminAuthコントローラー loginAuthでパスワードがヒットしませんでした。');
                return redirect()->to('spgsadmin')->withInput()->with('error', $errorMsg);
            }

        } else {
            log_message('debug', '★AdminAuthコントローラー loginAuthでIDがヒットしませんでした。');
            return redirect()->to('spgsadmin')->withInput()->with('error', $errorMsg);
        }

        // View に渡す
        $data = [
            'login_name' => session('login_name'),
            'login_misecd' => session('login_misecd'),
            'login_id' => session('login_id'),

            //画像ファイル
            'header_img01' => $this->adminConfig->header_img01,
            'header_url01' => $this->adminConfig->header_url01,
            'header_img02' => $this->adminConfig->header_img02,
            'header_url02' => $this->adminConfig->header_url02,

            //販売店ごとの設定
            'max_button' => $this->adminConfig->max_button,
        ];

        $data['buttons'] = $this->buttons;
        session()->set('buttons', $this->buttons);

        log_message('debug', '★AdminAuthコントローラー loginAuth menu表示 $data = ' . print_r($data, true));
        return redirect()->to('spgsadmin/menu');

    }

    public function logout()
    {
        log_message('debug', 'AdminAuthController logoutが呼ばれました。');
        session()->destroy();
        return redirect()->to('spgsadmin/login');
    }


}
