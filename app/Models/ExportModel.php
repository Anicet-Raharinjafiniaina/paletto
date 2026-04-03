<?php

namespace App\Models;

use CodeIgniter\Model;


class ExportModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function historique($actionId)
    {
        if ($actionId != "" && $actionId != null) {
            $arrHisto = [
                'data_json' => json_encode(["Export"]),
                'utilisateur_id' =>  session()->get('utilisateur')['user_id'],
                'action_id' => $actionId,
            ];
            $db = \Config\Database::connect();
            $builder = $db->table('historique');
            $builder->insert($arrHisto);
        }
    }
}
