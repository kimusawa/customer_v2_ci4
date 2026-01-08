<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 注意：get postは全て大文字にしないと警告が出ます

// 顧客画面用
$routes->GET('/', 'AuthController::login');
$routes->GET('login', 'AuthController::login');
$routes->GET('logout', 'AuthController::logout');
$routes->GET('spgsinfo/logout', 'AuthController::logout');
$routes->GET('spgsinfo/menu', 'AuthController::login');
$routes->match(['GET', 'POST'], 'spgsinfo', 'AuthController::login');

// 特商法
$routes->match(['GET', 'POST'], 'spgsinfo/toksho', 'SpgsinfoController::toksho');

// ログインできるのはPOSTのみ
$routes->POST('spgsinfo/menu', 'AuthController::loginAuth');

// 決済コールバック用（AuthGuard除外）
// 決済サイトからの戻り（POST/GET）でSameSite属性によりセッションCookieが送信されない場合があるため
// ここで受け取り、Controller内でリダイレクトすることでセッションを復帰させる
$routes->group('spgsinfo', ['filter' => ['instanceConfig']], function ($routes) {
    $routes->match(['GET', 'POST'], 'Redgfpayment', 'SpgsinfoController::Redgfpayment');
    $routes->match(['GET', 'POST'], 'Redgfpayment/(:segment)', 'SpgsinfoController::Redgfpayment/$1');
});


$routes->group('', ['filter' => ['instanceConfig', 'authGuard:user']], function ($routes) {

    // 当月検針情報
    $routes->match(['GET', 'POST'], 'spgsinfo/kensin_disp', 'SpgsinfoController::kensin_disp');
    $routes->get('spgsinfo/dgfpayment', 'SpgsinfoController::dgfpayment');

    // 決済コールバック用
    $routes->match(['GET', 'POST'], 'spgsinfo/RcvKekka/(:segment)', 'SpgsinfoController::RcvKekka/$1');

    // 請求照会
    $routes->match(['GET', 'POST'], 'spgsinfo/seikyu_disp', 'SpgsinfoController::seikyu_disp');
    $routes->get('spgsinfo/download_seikyu/(:segment)', 'SpgsinfoController::download_seikyu/$1');

    // 取引履歴情報
    $routes->match(['GET', 'POST'], 'spgsinfo/torihiki_disp/(:num)', 'SpgsinfoController::torihiki_disp/$1');

    // 料金表情報
    $routes->match(['GET', 'POST'], 'spgsinfo/ryokin_disp/(:num)', 'SpgsinfoController::ryokin_disp/$1');

    // 設置器具
    $routes->match(['GET', 'POST'], 'spgsinfo/kigu_disp/(:num)', 'SpgsinfoController::kigu_disp/$1');

    // メール変更
    $routes->match(['GET', 'POST'], 'spgsinfo/mail_change', 'SpgsinfoController::mail_change');
    $routes->match(['GET', 'POST'], 'spgsinfo/mailentry', 'SpgsinfoController::mailentry');
    $routes->match(['GET', 'POST'], 'spgsinfo/mailupdate', 'SpgsinfoController::mailupdate');

    // メールパスワード変更
    $routes->match(['GET', 'POST'], 'spgsinfo/pwd_change', 'SpgsinfoController::pwd_change');
    $routes->match(['GET', 'POST'], 'spgsinfo/pwdupdate', 'SpgsinfoController::pwdupdate');

});

// 管理者画面用
$routes->GET('/spgsadmin', 'AdminAuthController::login');
$routes->GET('spgsadmin/login', 'AdminAuthController::login');
$routes->GET('spgsadmin/logout', 'AdminAuthController::logout');
$routes->GET('spgsadmin/logout', 'AdminAuthController::logout');
$routes->match(['GET', 'POST'], 'spgsadmin', 'AdminAuthController::login');

// ログインできるのはPOSTのみ
$routes->POST('spgsadmin/menu', 'AdminAuthController::loginAuth');

$routes->group('spgsadmin', ['filter' => ['instanceConfig', 'authGuard:admin']], function ($routes) {

    // group内のパスは、グループ名spgsadmin/を除いて記述すること

    // メニュー画面
    $routes->GET('menu', 'AdminSpgsController::menu');

    // 照会用データ読込
    $routes->match(['GET', 'POST'], 'upload', 'AdminSpgsController::upload');
    $routes->match(['GET', 'POST'], 'upload_exec', 'AdminSpgsController::upload_exec');
    $routes->get('upload_complete', 'AdminSpgsController::upload_complete');  // 完了画面へリダイレクト

    // ログイン履歴照会
    $routes->match(['GET', 'POST'], 'userlogin_disp', 'AdminSpgsController::userlogin_disp');
    $routes->match(['GET', 'POST'], 'userlogin_disp/(:num)', 'AdminSpgsController::userlogin_disp/$1');
    $routes->match(['GET', 'POST'], 'download_seekhistory', 'AdminSpgsController::download_seekhistory');

    // 管理者データ出力
    $routes->match(['GET', 'POST'], 'pwdmail', 'AdminSpgsController::pwdmail');
    $routes->match(['GET', 'POST'], 'pwdmail_disp', 'AdminSpgsController::pwdmail_disp');
    $routes->match(['GET', 'POST'], 'pwdmail_download', 'AdminSpgsController::pwdmail_download');

    // お客様パスワード・変更初期化
    $routes->match(['GET', 'POST'], 'pwduser_disp/(:num)', 'AdminSpgsController::pwduser_disp/$1');
    $routes->match(['GET', 'POST'], 'pwduser_disp', 'AdminSpgsController::pwduser_disp');
    $routes->match(['GET', 'POST'], 'pwdchange_disp', 'AdminSpgsController::pwdchange_disp');
    $routes->match(['GET', 'POST'], 'pwdupdate', 'AdminSpgsController::pwdupdate');
    $routes->match(['GET', 'POST'], 'kokyaku_init', 'AdminSpgsController::kokyaku_init');

    // 照会用データ（お客様　個別）　読込
    $routes->match(['GET', 'POST'], 'upload2', 'AdminSpgsController::upload2');
    $routes->match(['GET', 'POST'], 'upload2_exec', 'AdminSpgsController::upload2_exec');

});

