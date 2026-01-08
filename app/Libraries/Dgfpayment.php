<?php
//**************************************************************************
// Creation:　株式会社 イングコーポレーション
//   SYSTEM:　ＷＥＢ照会
//**************************************************************************
//　Modeule           Spgsinfo　Controller
//**************************************************************************
//  日付      担当者      変更理由（仕変コード）
//--------------------------------------------------------------------------
//2025.12.05  kimura       Mnt-000  CI4対応
//**************************************************************************

// defined('BASEPATH') OR exit('No direct script access allowed');
namespace App\Libraries;

const AES_KEY = 'Ing4496Toms1064';
const AES_IV = '9876512347913465';

class Dgfpayment
{

    // protected $CI;
    protected $spgskensinModel;

    public function __construct()
    {
        // $this->CI =& get_instance();
        $this->spgskensinModel = model('SpgskensinModel');
    }


    public function GetPaymentSiteAdress($wkdat)
    {

        log_message('debug', '★Dgfpaymentライブラリ GetPaymentSiteAdress が呼び出されました。');

        // 送信先URLと認証情報
        $url = 'https://smilepayment.jp/agentpay/payment/agent_auth';
        $auth_basic = base64_encode('U12345678901234567890:p98765432109876543210');

        // 送信データ作成
        $data = [
            'order_id' => $wkdat['order_id'],
            'stoc_id' => 'ITEM-1000',
            'stoc_name' => $wkdat['stoc_name'],
            'kingaku' => $wkdat['kingaku'],
            'type' => $wkdat['type'],
            'tel' => $wkdat['tel'],
            'name' => $wkdat['name'],
            'mail' => $wkdat['mail'],
            'return_url' => $wkdat['return_url'],
            'return_token' => $wkdat['return_token'],
            'kekka_snd_url' => $wkdat['kekka_snd_url']
        ];
        $data_json = json_encode($data); // JSONエンコード
        log_message('debug', '★Dgfpaymentライブラリ GetPaymentSiteAdress $data_json=' . print_r($data_json, true));

        $header = array(
            'Content-type: application/json; charset=UTF-8',
            'Authorization: Basic ' . $auth_basic
        );

        $options = array(
            // HTTPコンテキストオプションをセット
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $header),
                'content' => $data_json
            )
        );
        $context = stream_context_create($options);

        // APIリクエスト
        $raw_data = file_get_contents($url, false, $context);

        // エラーチェック
        if ($raw_data === false) {
            log_message('debug', '★Dgfpaymentライブラリ GetPaymentSiteAdress API呼び出し失敗' . $url);
            log_message('debug', '★Dgfpaymentライブラリ GetPaymentSiteAdress エラー' . print_r(error_get_last(), true));
            return (object) [
                'error' => 'API呼び出しに失敗しました',
                'kekka' => false
            ];
        }

        log_message('debug', '★Dgfpaymentライブラリ GetPaymentSiteAdress APIレスポンス' . $raw_data);

        $ret = json_decode($raw_data);

        if (!$ret) {
            log_message('error', '★JSONデコード失敗: ' . $raw_data);
            return (object) [
                'error' => 'レスポンスの解析に失敗しました',
                'kekka' => false
            ];
        }

        log_message('debug', '★デコード後: ' . print_r($ret, true));

        // hash値の存在確認
        if (!isset($ret->hash)) {
            log_message('error', '★hash値が返されませんでした');
            return $ret;
        }

        // 送ったHASH値をkensin_infoに保存する
        // $sq ="UPDATE spgskensin";
        // $sq .= " SET     hash = '".$ret->{'hash'}."'";
        // $sq .= " WHERE   misecd = ".$wkdat['misecd'];
        // $sq .= " AND     usercd = '".$wkdat['usercd']."'";
        // $retdb = $this->CI->db->simple_query($sq);
        $retdb = $this->spgskensinModel
            ->where('misecd', $wkdat['misecd'])
            ->where('usercd', $wkdat['usercd'])
            ->set('hash', $ret->hash)
            ->update();

        if ($retdb == true) {
            log_message('debug', '★hash値をDBに保存しました $hash = ' . $ret->hash);
            //返却データをセット
            $data['kekka'] = true;
        } else {
            //エラー　登録がありません
            // $error = $this->CI->db->error();
            $db = \Config\Database::connect();
            $error = $db->error();
            log_message('error', '★DB更新失敗' . print_r($error . TRUE));
            $data['error'] = $error;
            $data['kekka'] = false;
        }

        return $ret;

    }

    public function GetArgs(string $text)
    {
        //echo $text."<br>";
        $text_e = $this->base64_urlsafe_decode($text);
        //echo $text_e."<br>";
        $text_d = $this->decrypt($text_e);
        //echo $text_d."<br>";
        $args = explode(",", $text_d);
        return $args;
    }

    public function UpdateResultForSpgsInfo($args)
    {
        // $this->CI->load->database();
        // $this->CI->load->model('spgsinfo_model');
        $order_id = $args[0];
        $wks = explode("_", $order_id);
        $cds = explode("-", $wks[1]);
        $mise = $cds[0];
        $ucd = $cds[1];
        $kin = $wks[2];
        $date = substr($wks[3], 0, 8);
        $status = $args[2];
        $type = $args[3];
        $date2 = substr($args[4], 0, 8);
        /*
                echo $mise."<br>";
                echo $ucd."<br>";
                echo $kin."<br>";
                echo $date."<br>";
        */
        $kekka = "NONE";
        switch ($status) {
            case "111":
            case "251":
            case "351":
            case "511":
            case "611":
                $kekka = "true";
                $data['kekka'] = true;
                break;
            default:
                $data['kekka'] = true;
                //$kekka = "true";
                //echo "false";
                break;
        }
        if ($kekka === "true") {
            // $wkdata = $this->CI->spgsinfo_model->get_kensin_info($mise, $ucd);
            $wkdata = $this->spgskensinModel->readData($mise, $ucd);

            //var_dump($wkdata);
            if (isset($wkdata["torigokeikin"])) {
                $torigokeikin = intval(str_replace(",", "", $wkdata["torigokeikin"]));
                $seikyukin = intval(str_replace(",", "", $wkdata["seikyukin"]));
                if ($torigokeikin > 0) {
                    // 請求額がゼロ円の場合、処理しない。
                    /*
                    echo strval($torigokeikin)."<br>";
                    echo strval($seikyukin)."<br>";
                    */
                    for ($wki = 1; $wki < 7; $wki++) {
                        if ($wkdata["toriymd" . $wki] == '') {
                            //データ
                            $strDate = substr($date2, 0, 4) . "/" . substr($date2, 4, 2) . "/" . substr($date2, 6, 2);
                            $torigokeikin -= intval($kin);
                            $seikyukin -= intval($kin);

                            // この明細に更新する。
                            // $sq ="UPDATE spgskensin";
                            // $sq .= " SET    toriymd".$wki." = '".$strDate."'";
                            // $sq .= " ,      toriname".$wki." = 'ご入金'";
                            // $sq .= " ,      torikin".$wki." = '".number_format($kin)."'";
                            // $sq .= " ,      torigokeikin = '".number_format($torigokeikin)."'";
                            // $sq .= " ,      seikyukin = '".number_format($seikyukin)."'";
                            // $sq .= " WHERE   misecd = ".$mise;
                            // $sq .= " AND     usercd = '".$ucd."'";
                            // var_dump($sq);
                            // $ret = $this->CI->db->simple_query($sq);
                            $ret = $this->spgskensinModel
                                ->where('misecd', $mise)
                                ->where('usercd', $ucd)
                                ->set([
                                    'toriymd' . $wki => $strDate,
                                    'toriname' . $wki => 'ご入金',
                                    'torikin' . $wki => number_format($kin),
                                    'torigokeikin' => number_format($torigokeikin),
                                    'seikyukin' => number_format($seikyukin)
                                ])
                                ->update();

                            if ($ret == true) {
                                //返却データをセット
                                $data['kekka'] = true;
                            } else {
                                //エラー　登録がありません
                                // $error = $this->CI->db->error();
                                $db = \Config\Database::connect();
                                $error = $db->error();
                                $data['error'] = $error;
                                $data['kekka'] = false;
                            }
                            break;
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function GetReceipt($_ary)
    {

        $url = 'https://smilepayment.jp/agentpay/payment/getreceipt/' . $_ary['hash'];
        $auth_basic = base64_encode('U12345678901234567890:p98765432109876543210');


        $header = array(
            'Content-type: application/json; charset=UTF-8',
            'Authorization: Basic ' . $auth_basic
        );

        $options = array(
            // HTTPコンテキストオプションをセット
            'http' => array(
                'method' => 'GET',
                'header' => implode("\r\n", $header)
            )
        );
        $context = stream_context_create($options);

        $raw_data = file_get_contents($url, false, $context);
        //var_dump($raw_data);
        //exit;
        $pPath = "./fileout/" . $_ary['order_id'] . ".pdf";

        file_put_contents($pPath, $raw_data);
        header('Content-Type: ' . 'application/pdf');
        header('X-Content-Type-Options: nosniff');
        header('Content-Length: ' . filesize($pPath));
        header('Content-Disposition: attachment; filename="' . basename($pPath) . '"');
        header('Connection: close');
        while (ob_get_level()) {
            ob_end_clean();
        }
        readfile($pPath);
        return True;

    }

    function encrypt($text)
    {
        return $text === null ? null :
            openssl_encrypt($text, 'AES-256-CBC', AES_KEY, OPENSSL_RAW_DATA, AES_IV);
    }

    function decrypt($text)
    {
        return $text === null ? null :
            openssl_decrypt($text, 'AES-256-CBC', AES_KEY, OPENSSL_RAW_DATA, AES_IV);
    }

    function base64_urlsafe_encode($val)
    {
        $val = base64_encode($val);
        return str_replace(array('+', '/', '='), array('_', '-', '.'), $val);
    }

    function base64_urlsafe_decode($val)
    {
        $val = str_replace(array('_', '-', '.'), array('+', '/', '='), $val);
        return base64_decode($val);
    }
}

?>