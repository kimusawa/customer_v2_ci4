<?php

//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//            標準版　設定ファイル
//**************************************************************************
//　 CI3からの移植元: customer_v2/application/config/config_spgsinfo.php
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2025.12.04  kimura       Mnt-000  CI4対応
//--------------------------------------------------------------------------

namespace Config\Info;

use CodeIgniter\Config\BaseConfig;

class Spgsinfo extends BaseConfig
{

    //
    // SPGS照会　メニューのボタン名・URL設定項目
    //

    public $max_button = 8; // 最大ボタン数（今は使用していない。下のボタンを配列にいれ、配列数を使用）

    public $button1 = array('name' => '当月検針情報', 'url' => 'spgsinfo/kensin_disp/', 'option' => 0);
    public $button2 = array('name' => '請求照会', 'url' => 'spgsinfo/seikyu_disp/', 'option' => 1);
    public $button3 = array('name' => '取引履歴情報', 'url' => 'spgsinfo/torihiki_disp/0/', 'option' => 0);
    public $button4 = array('name' => '料金表情報', 'url' => 'spgsinfo/ryokin_disp/0/', 'option' => 0);
    public $button5 = array('name' => '設置器具', 'url' => 'spgsinfo/kigu_disp/0/', 'option' => 0);
    public $button6 = array('name' => 'メールアドレス変更', 'url' => 'spgsinfo/mail_change/', 'option' => 0);
    public $button7 = array('name' => 'パスワード変更', 'url' => 'spgsinfo/pwd_change/', 'option' => 0);
    public $button8 = array('name' => 'ログアウト', 'url' => 'spgsinfo/logout/', 'option' => 0);
    // public $button9 = array('name'=>'テスト',     'url'=>'spgsinfo/test/',			'option'=>0);
    // public $button10 = array('name'=>'テスト２',     'url'=>'spgsinfo/test2/',			'option'=>0);


    //
    // SPGS照会　顧客用：情報の設定項目
    //

    // 販売店名称
    public $misename = '株式会社イングコーポレーション';
    // 送信者
    public $mailfrom = 'admin@smile.co.jp';
    // 電話番号
    public $misetel = '048-542-4496';
    // コピーライト
    public $copywrite = 'ingCorporation';
    // URL
    public $miseurl = 'http://www.smile.co.jp/';
    // ＳＭＴＰプロトコル
    public $smtp_protocol = 'smtp';
    // ＳＭＴＰキャラクター
    public $smtp_charset = 'ISO-2022-JP';
    // ＳＭＴＰサーバー
    public $smtp_host = 'scs03.smile.co.jp';
    // ＳＭＴＰユーザー
    public $smtp_user = 'ing-system@smile.co.jp';
    // ＳＭＴＰパスワード
    public $smtp_pass = 'ing-system@4496';
    // ＳＭＴＰポート番号
    public $smtp_port = '587';
    // Customer_Listレコード数/ページ
    public $customer_rec = '30';
    // Customer_Dispの明細数/ページ
    public $meisai_rec = '5';
    // 入力前チェック項目数
    public $checkcount = '12';
    // ヘッダ画像１（Codeigniter\images\spgsに置く）
    public $header_img01 = 'logo_sample.png';

    // ヘッダ画像１にリンクを張る(不要なときはjavascript:void(0)をあてる)
    public $header_url01 = 'javascript:void(0)';
    // ヘッダ画像２（Codeigniter\images\spgsに置く）
    public $header_img02 = 'logo_sample2.png';
    //public $header_img02  	= 'hayashi-logo2.png';

    // ヘッダ画像２にリンクを張る(不要なときはjavascript:void(0)をあてる)
    public $header_url02 = 'javascript:void(0)';

    // DGフィナンシャル決済1:有0:無
    public $dgf_flg = 1;
    // 請求書ダウンロードフラグ 1:有 0:無
    public $bill_flg = 1;
    // お知らせ表示フラグ 2:下 1:上 0:無
    public $oshirase_flg = 2;
    // SPGS_STYLE 0:243でお尻が000の場合見えなくなるタイプ 1:FREEタイプ
    public $code_style = 1;
    //[Mnt-009]------------------------------------------------------------------------------------------>> Edit Start 25/11/04
    public $autoSeikyuKill = 1;
    //[Mnt-009]<<------------------------------------------------------------------------------------------ Edit E n d 25/11/04
    // 決済システム用ブラウザバックアドレス
    // public $return_url  = 'http://www.smileeiseisystem.com/customer_v2/spgsinfo/Redgfpayment/';
    public $return_url = 'http://localhost:8080/customer_v2_ci4/public/spgsinfo/Redgfpayment/';
    // 決済システム用結果受信アドレス
    // public $kekka_snd_url  = 'http://www.smileeiseisystem.com/customer_v2/spgsinfo/RcvKekka/';
    public $kekka_snd_url = 'http://localhost:8080/customer_v2_ci4/public/spgsinfo/RcvKekka/';

    // パスワードハッシュ化フラグ 1:有 0:無
    public $pwd_hash_flg = 0;

}
