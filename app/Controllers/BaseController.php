<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\SpgskanriloginModel;
use App\Models\SpgskenmsgModel;
use App\Models\SpgskensinModel;
use App\Models\SpgskiguModel;
use App\Models\SpgsloginModel;
use App\Models\SpgsmailModel;
use App\Models\SpgspwdModel;
use App\Models\SpgsryokinModel;
use App\Models\SpgstoriModel;
use App\Models\SpgsuserModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{

    protected $appKey; // アプリケーション内の振り分けキー
    protected $buttons; // メニューのボタン設定

    // db接続用
    protected $db;


    // 販売店（テナント）固有設定
    protected $userConfig;
    protected $adminConfig;
    protected $session; // セッション管理用のプロパティ


    // ユーザー画面用
    protected $spgsuserModel;
    protected $spgskensinModel;
    protected $spgstoriModel;
    protected $spgsryokinModel;
    protected $spgskenmsgModel;
    protected $spgskiguModel;
    protected $spgsmailModel;
    protected $spgspwdModel;

    // 管理者画面用
    protected $spgskanriloginModel;
    protected $spgsloginModel;

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    // protected $helpers = [];
    protected $helpers = ['form', 'session', 'url', 'filesystem'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $uri = service('uri');
        $requestId = uniqid('req_', true);
        $startTime = microtime(true);
        log_message('debug', "★[{$requestId}] initController START: " . (string) $uri);

        register_shutdown_function(function () use ($requestId, $startTime, $uri) {
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            log_message('debug', "★[{$requestId}] Request END: " . (string) $uri . " (Duration: " . round($duration, 4) . "s)");
        });

        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');

        // 共通プロパティの初期化
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->userConfig = session('userConfig') ?? config(\Config\Info\Spgsinfo::class);
        $this->adminConfig = session('adminConfig') ?? config(\Config\Info\Spgsadmin::class);

        // ユーザー用
        $this->spgsuserModel = model('SpgsuserModel');
        $this->spgskensinModel = model('SpgskensinModel');
        $this->spgstoriModel = model('SpgstoriModel');
        $this->spgsryokinModel = model('SpgsryokinModel');
        $this->spgskenmsgModel = model('SpgskenmsgModel');
        $this->spgskiguModel = model('SpgskiguModel');
        $this->spgsmailModel = model('SpgsmailModel');
        $this->spgspwdModel = model('SpgspwdModel');

        // 管理者用
        $this->spgskanriloginModel = model('spgskanriloginModel');
        $this->spgsloginModel = model('spgsloginModel');

    }

}
