<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthGuard implements FilterInterface
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

        log_message('debug', '★AuthGuard before が呼び出されました。');

       // Filter 引数（admin / user）
        $guardType = $arguments[0] ?? 'user';

        // site_type は SiteConfigFilter で設定済み
        $siteType = session('site_type') ?? 'user';

        // 念のためのガード
        if ($guardType !== $siteType) {
            // 想定外アクセスは user 扱い
            $siteType = 'user';
        }
        log_message('debug', '★AuthGuard before $siteType=' . $siteType);
        log_message('debug', '★AuthGuard before $guardType=' . $guardType);

        // =========================
        // ログイン判定
        // =========================
        if ($guardType === 'admin') {

            if (! session('admin_logged_in')) {
            log_message('debug', '★AuthGuard before $admin_logged_inがfalse');
                return redirect()->to('/spgsadmin/login');
            }

        } else {
            if (! session('user_logged_in')) {
                log_message('debug', '★AuthGuard before $user_logged_inがfalse');
                return redirect()->to('/login');
            }
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
