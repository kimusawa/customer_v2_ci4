<?php

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */

    public static function dgfpayment($getShared = true)
    {
        log_message('debug', '★Services dgfpayment 実行');
        if ($getShared) {
            return static::getSharedInstance('dgfpayment');
        }

        return new \App\Libraries\Dgfpayment();
    }

    public static function uploadSeikyu($getShared = true)
    {
        log_message('debug', '★Services uploadSeikyu 実行');
        if ($getShared) {
            // 作成済みの円スタンスがあればそれを返す
            return static::getSharedInstance('uploadSeikyu');
        }

        // 新しいインスタンスを作成して返す
        return new \App\Libraries\Upload_seikyu();
    }

}
