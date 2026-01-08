<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Security extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * CSRF Protection Method
     * --------------------------------------------------------------------------
     *
     * Protection Method for Cross Site Request Forgery protection.
     *
     * @var string 'cookie' or 'session'
     */
    // public string $csrfProtection = 'cookie';
    public string $csrfProtection = 'session';

    /**
     * --------------------------------------------------------------------------
     * CSRF Token Randomization
     * --------------------------------------------------------------------------
     *
     * Randomize the CSRF Token for added security.
     */
    // public bool $tokenRandomize = false;
    public bool $tokenRandomize = true; // トークンをランダム化する

    /**
     * --------------------------------------------------------------------------
     * CSRF Token Name
     * --------------------------------------------------------------------------
     *
     * Token name for Cross Site Request Forgery protection.
     */
    // public string $tokenName = 'csrf_test_name';
    public string $tokenName = 'pgs_web_csrf_token';

    /**
     * --------------------------------------------------------------------------
     * CSRF Header Name
     * --------------------------------------------------------------------------
     *
     * Header name for Cross Site Request Forgery protection.
     */
    public string $headerName = 'X-CSRF-TOKEN';

    /**
     * --------------------------------------------------------------------------
     * CSRF Cookie Name
     * --------------------------------------------------------------------------
     *
     * Cookie name for Cross Site Request Forgery protection.
     */
    // public string $cookieName = 'csrf_cookie_name';
    // 上でcsrfProtectionを'cookie'にした場合に使われる
    // 今回は使われないけど、念のため変更
    public string $cookieName = 'pgs_web_csrf_cookie';

    /**
     * --------------------------------------------------------------------------
     * CSRF Expires
     * --------------------------------------------------------------------------
     *
     * Expiration time for Cross Site Request Forgery protection cookie.
     *
     * Defaults to two hours (in seconds).
     */
    // public int $expires = 7200;
    public int $expires = 3600; // 1時間（3600秒）に設定

    /**
     * --------------------------------------------------------------------------
     * CSRF Regenerate
     * --------------------------------------------------------------------------
     *
     * Regenerate CSRF Token on every submission.
     */
    public bool $regenerate = true; // トークンを毎回再生成する

    /**
     * --------------------------------------------------------------------------
     * CSRF Redirect
     * --------------------------------------------------------------------------
     *
     * Redirect to previous page with error on failure.
     *
     * @see https://codeigniter4.github.io/userguide/libraries/security.html#redirection-on-failure
     */
    public bool $redirect = (ENVIRONMENT === 'production'); // 本番環境ではリダイレクトを有効にする

        // 既存の設定に加えて、ペッパーを追加
    public string $pepper = 'nG8$Jx!r29Fq@tZp4bL7mE#QcR1vHsWd0P&UyKf3^TzN8wR';  // ← ここにペッパー文字列

}
