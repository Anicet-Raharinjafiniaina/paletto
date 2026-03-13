<?php

namespace App\Controllers;

use App\Controllers\Acces;

class Dashboard extends BaseController
{

    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }
    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(7);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Tableau de bord";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('dashboard', $arr);
            return;
        }
        echo view('dashboard', $arr);
    }

    public function getDataForChartPie()
    {
        $periode = $this->request->getPost('periode');
        $periode = "hebdomadaire";
        $arrSumEmplacement = $this->getEmplacement($periode);
        $arrSumPalette = $this->getPalette($periode);
        $arrSumMouvement = $this->getMouvement($periode);
        $result = [
            'emplacement' => $arrSumEmplacement,
            'palette'     => $arrSumPalette,
            'mouvement'     => $arrSumMouvement
        ];
        return json_encode($result);
    }

    /** retourne Nombre  emplacement libre et occupé */
    public function getEmplacement($periode)
    {
        $whereDate = '';
        switch ($periode) {
            case 'quotidien':
                $whereDate = "DATE(date_action) = CURRENT_DATE";
                break;

            case 'hebdomadaire':
                $whereDate = "date_trunc('week', date_action) = date_trunc('week', CURRENT_DATE)";
                break;

            case 'mensuel':
                $whereDate = "date_trunc('month', date_action) = date_trunc('month', CURRENT_DATE)";
                break;

            case 'annuel':
                $whereDate = "date_trunc('year', date_action) = date_trunc('year', CURRENT_DATE)";
                break;

            default:
                return $this->response->setJSON([]);
        }

        $sql = "SELECT 
                    COALESCE(SUM(CASE WHEN emplacement_statut_id = 2 THEN 1 ELSE 0 END),0) AS nb_occupe,
                    COALESCE(SUM(CASE WHEN emplacement_statut_id = 1 THEN 1 ELSE 0 END),0) AS nb_libre
                FROM (
                    SELECT 
                        emplacement_statut_id,
                        COALESCE(date_modification, date_creation) AS date_action
                    FROM liste_emplacement
                    WHERE flag_suppression = 0
                ) t
                WHERE $whereDate";

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }

    public function getPalette($periode)
    {
        $periodes = [
            'quotidien'    => "DATE(date_action) = CURRENT_DATE",
            'hebdomadaire' => "date_trunc('week', date_action) = date_trunc('week', CURRENT_DATE)",
            'mensuel'      => "date_trunc('month', date_action) = date_trunc('month', CURRENT_DATE)",
            'annuel'       => "date_trunc('year', date_action) = date_trunc('year', CURRENT_DATE)"
        ];

        if (!isset($periodes[$periode])) {
            return $this->response->setJSON([]);
        }

        $whereDate = $periodes[$periode];

        $sql = "SELECT 
                    COALESCE(SUM(CASE WHEN palette_statut_id = 1 THEN 1 ELSE 0 END),0) AS nb_libre,
                    COALESCE(SUM(CASE WHEN palette_statut_id = 2 THEN 1 ELSE 0 END),0) AS nb_attribue,
                    COALESCE(SUM(CASE WHEN palette_statut_id = 3 THEN 1 ELSE 0 END),0) AS nb_occupe
                FROM (
                    SELECT 
                        palette_statut_id,
                        COALESCE(date_modification, date_creation) AS date_action
                    FROM palette
                    WHERE flag_suppression = 0
                ) t
                WHERE $whereDate";

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }

    public function getMouvement($periode)
    {
        $periodes = [
            'quotidien'    => "DATE(date_action) = CURRENT_DATE",
            'hebdomadaire' => "date_trunc('week', date_action) = date_trunc('week', CURRENT_DATE)",
            'mensuel'      => "date_trunc('month', date_action) = date_trunc('month', CURRENT_DATE)",
            'annuel'       => "date_trunc('year', date_action) = date_trunc('year', CURRENT_DATE)"
        ];

        if (!isset($periodes[$periode])) {
            return $this->response->setJSON([]);
        }

        $whereDate = $periodes[$periode];

        $sql = "SELECT 
                    COALESCE(SUM(CASE WHEN mouvement_type_id = 1 THEN 1 ELSE 0 END),0) AS nb_entree,
                    COALESCE(SUM(CASE WHEN mouvement_type_id = 2 THEN 1 ELSE 0 END),0) AS nb_sortie,
                    COALESCE(SUM(CASE WHEN mouvement_type_id = 3 THEN 1 ELSE 0 END),0) AS nb_transfert         
                FROM (
                    SELECT 
                        mouvement_type_id,
                        COALESCE(date_modification, date_creation) AS date_action
                    FROM mouvement
                    WHERE flag_suppression = 0
                ) t
                WHERE $whereDate
                ";

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }
}
