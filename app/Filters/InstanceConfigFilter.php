<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class InstanceConfigFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $uriStr = (string) service('uri');
        $fid = uniqid('f', true);
        log_message('debug', "★[{$fid}] Filter START: {$uriStr}");

        $uri = service('uri');
        $firstSegment = $uri->getSegment(1);

        // site_type 確定（ログイン前）
        $siteType = ($firstSegment === 'spgsadmin') ? 'admin' : 'user';

        // セッション名を動的に変更（セッション開始前に設定）
        $sessionConfig = config('Session');

        if ($siteType === 'admin') {
            $sessionConfig->cookieName = 'ci_session_admin';
        } else {
            $sessionConfig->cookieName = 'ci_session_user';
        }

        log_message('debug', "★[{$fid}] Calling session()->set('site_type')...");
        session()->set('site_type', $siteType);
        log_message('debug', "★[{$fid}] session()->set('site_type') DONE.");

        // 共通（user）設定
        $userConfig = new \Config\Info\Spgsinfo();
        session()->set('userConfig', $userConfig);

        if ($siteType === 'admin') {
            // 管理者追加設定
            $adminConfig = new \Config\Info\Spgsadmin();
            session()->set('adminConfig', $adminConfig);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
