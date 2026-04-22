<?php

namespace App\Models;

use CodeIgniter\Model;


class DashboardModel extends Model
{
    protected $dbX3;
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
        $this->dbX3 = db_connect('connex_v12');
    }

    /** retourne Nombre  emplacement libre et occupé */
    public function getEmplacement()
    {
        $sql = "SELECT 
                    COALESCE(SUM(CASE WHEN emplacement_statut_id = 2 THEN 1 ELSE 0 END),0) AS nb_occupe,
                    COALESCE(SUM(CASE WHEN emplacement_statut_id = 1 THEN 1 ELSE 0 END),0) AS nb_libre
                FROM (
                    SELECT 
                        emplacement_statut_id,
                        COALESCE(date_modification, date_creation) AS date_action
                    FROM emplacement_adresse
                    WHERE flag_suppression = 0
                ) t";
        $query = $this->db->query($sql);
        return $query->getRowArray();
    }

    public function getPalette()
    {
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
                ) t";
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
                        COALESCE(date_mouvement) AS date_action
                    FROM mouvement
                    WHERE flag_suppression = 0
                ) t
                WHERE $whereDate
                ";

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }

    /** Pourcentage pour chaque entrepôt */
    public function getEntrepot()
    {
        $sql = "SELECT 
                    entrepot_code,
                    TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM 
                        ROUND(100.0 * SUM(CASE WHEN statut_id = 1 THEN 1 ELSE 0 END) / COUNT(*), 2)::text
                    )) AS libre,
                    TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM 
                        ROUND(100.0 * SUM(CASE WHEN statut_id = 2 THEN 1 ELSE 0 END) / COUNT(*), 2)::text
                    )) AS occupe
                FROM (
                    SELECT 
                        entrepot_code,
                        statut_id,
                        COALESCE(date_modification, date_creation) AS date_action
                    FROM emplacement_adresse_view
                ) t
                GROUP BY entrepot_code
                ORDER BY entrepot_code";

        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function getMouvementFlux($periode)
    {
        $periodes = [
            'quotidien' => [
                'group' => "date_trunc('hour', date_mouvement)",
                'label' => "TO_CHAR(date_trunc('hour', date_mouvement),'HH24:00')",
                'where' => "DATE(date_mouvement) = CURRENT_DATE"
            ],
            'hebdomadaire' => [
                'group' => "date_trunc('day', date_mouvement)",
                'label' => "TO_CHAR(date_trunc('day', date_mouvement),'Dy')",
                'where' => "date_trunc('week', date_mouvement) = date_trunc('week', CURRENT_DATE)"
            ],
            'mensuel' => [
                'group' => "date_trunc('week', date_mouvement)",
                'label' => "TO_CHAR(date_trunc('week', date_mouvement),'\"S\"IW')",
                'where' => "date_trunc('month', date_mouvement) = date_trunc('month', CURRENT_DATE)"
            ],
            'annuel' => [
                'group' => "date_trunc('month', date_mouvement)",
                'label' => "TO_CHAR(date_trunc('month', date_mouvement),'Mon')",
                'where' => "date_trunc('year', date_mouvement) = date_trunc('year', CURRENT_DATE)"
            ]
        ];

        $p = $periodes[$periode];

        $sql = "SELECT
                    {$p['label']} AS periode,
                    SUM(CASE WHEN mouvement_type_id = 1 THEN 1 ELSE 0 END) AS entree,
                    SUM(CASE WHEN mouvement_type_id = 2 THEN 1 ELSE 0 END) AS sortie,
                    SUM(CASE WHEN mouvement_type_id = 3 THEN 1 ELSE 0 END) AS transfert
                FROM mouvement
                WHERE flag_suppression = 0
                AND {$p['where']}
                GROUP BY {$p['group']}
                ORDER BY {$p['group']}
                ";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }
}
