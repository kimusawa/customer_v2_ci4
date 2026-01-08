<?php

namespace Config\Info;

use CodeIgniter\Config\BaseConfig;

class Spgsadmin extends BaseConfig
{
    //
    // SPGS照会　メニューのボタン名・URL設定項目
    //

    public $max_button = 5; // 最大ボタン数（今は使用していない。下のボタンを配列にいれ、配列数を使用）

    public $button1 = array('name'=>'照会用データ読込',   'url'=>'spgsadmin/upload/', 	'option'=>0);
    public $button2 = array('name'=>'ログイン履歴照会',       'url'=>'spgsadmin/userlogin_disp/',	'option'=>1);
    public $button3 = array('name'=>'管理者データ出力',   'url'=>'spgsadmin/pwdmail/','option'=>0);
    public $button4 = array('name'=>'お客様パスワード変更・初期化',     'url'=>'spgsadmin/pwduser_disp/0/',	'option'=>0);
    public $button5 = array('name'=>'照会用データ\n（お客様 個別）読込',       'url'=>'spgsadmin/upload2/',	'option'=>0);
    // public $button6 = array('name'=>'メールアドレス変更', 'url'=>'spgsadmin/mail_change/','option'=>0);
    // public $button7 = array('name'=>'パスワード変更', 'url'=>'spgsadmin/pwd_change/',		'option'=>0);
    // public $button8 = array('name'=>'ログアウト',     'url'=>'spgsadmin/logout/',			'option'=>0);
    // public $button9 = array('name'=>'テスト',     'url'=>'spgsadmin/test/',			'option'=>0);
    // public $button10 = array('name'=>'テスト２',     'url'=>'spgsadmin/test2/',			'option'=>0);


    //
    // SPGS照会　管理者用：情報の設定項目
    //

    // 送信者
    public string $mailfrom = 'enoki@smile.co.jp';

    // Customer_Listレコード数/ページ
    public string $customer_rec = '30';

    // Customer_Dispの明細数/ページ
    public string $meisai_rec = '10';

    // 入力前チェック項目数
    public string $checkcount = '12';

    // ヘッダ画像１（Codeigniter\images\spgsに置く）
    public string $header_img01 	= 'logo_sample.png';

    // ヘッダ画像１にリンクを張る(不要なときはjavascript:void(0)をあてる)
    public string $header_url01 	= 'javascript:void(0)';

    // ヘッダ画像２（Codeigniter\images\spgsに置く）
    public string $header_img02 	= 'logo_sample2.png';

    // ヘッダ画像２にリンクを張る(不要なときはjavascript:void(0)をあてる)
    public string $header_url02 	= 'javascript:void(0)';

    // 請求書自動削除 0:する 1:しない
    public int $autoSeikyuKill = 1;

    // パスワードハッシュ化フラグ 1:有 0:無
    public $pwd_hash_flg  = 0;

}
