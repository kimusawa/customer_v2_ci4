<?php

namespace App\Libraries;

use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class CsrfExceptionHandler implements ExceptionHandlerInterface
{
    public function handle(Throwable $exception, RequestInterface $request, ResponseInterface $response, int $statusCode, int $exitCode): void
    {
        // URLヘルパーをロード
        helper('url');

        // ログ出力（デバッグ用）
        log_message('error', '★CsrfExceptionHandler triggered.');
        log_message('error', '★Request URI: ' . $request->getUri());

        // ログインURLの構築 (site_urlを使用して正しいURLを生成)
        $redirectUrl = site_url('login');

        // URLに 'spgsadmin' が含まれている場合は管理者のログイン画面へ
        if (strpos((string) $request->getUri(), 'spgsadmin') !== false) {
            $redirectUrl = site_url('spgsadmin/login');
        }

        log_message('error', '★Redirecting to: ' . $redirectUrl);

        if (!headers_sent()) {
            header('Location: ' . $redirectUrl);
        }

        exit($exitCode);
    }
}
