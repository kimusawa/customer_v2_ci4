<?php

namespace App\Models;

use CodeIgniter\Model;

class SpgsuserModel extends Model
{
    protected $table = 'spgsuser';
    protected $primaryKey = 'misecd,usercd';
    protected $useAutoIncrement = false;    // 自動採番するかどうかはfalseに変更
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['mail', 'pwd'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function readData($misecd, $usercd)
    {
        return $this->where('misecd', $misecd)
            ->where('usercd', $usercd)
            ->first();
    }

    /**
     * Get user detail data
     */
    public function get_spgsuser($misecd, $usercd)
    {
        $user = $this->where('misecd', $misecd)
            ->where('usercd', $usercd)
            ->first();

        if ($user) {
            $user['misecd'] = $misecd;
            $user['usercd'] = $usercd;

            // dspusercdの作成（存在しない場合、またはフォーマットが必要な場合）
            if (!isset($user['dspusercd']) || $user['dspusercd'] == '') {
                $ucd1 = substr($usercd, 0, 2);
                $ucd2 = substr($usercd, 2, 4);
                $ucd3 = substr($usercd, 6, 3);
                if ($ucd3 != '000') {
                    $user['dspusercd'] = $ucd1 . '-' . $ucd2 . '-' . $ucd3;
                } else {
                    $user['dspusercd'] = $ucd1 . '-' . $ucd2;
                }
            }
            return $user;
        }
        return null;
    }

    /**
     * Password update
     */
    public function pwdupdate($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // spgspwd insert
        $spgspwdData = [
            'misecd' => $data['misecd'],
            'usercd' => $data['usercd'],
            'oldpwd' => $data['oldpwd'],
            'newpwd' => $data['newpwd'],
            'entryymd' => date("Ymd"),
            'entrytime' => date("His"),
            'sndymd' => 0,
            'sndtime' => 0
        ];
        $db->table('spgspwd')->insert($spgspwdData);

        // spgsuser update
        $this->where('misecd', $data['misecd'])
            ->where('usercd', $data['usercd'])
            ->set(['pwd' => $data['newpwd']])
            ->update();

        $db->transComplete();
        return $db->transStatus();
    }
}
