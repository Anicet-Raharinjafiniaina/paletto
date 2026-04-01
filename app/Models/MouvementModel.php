<?php

namespace App\Models;

use CodeIgniter\Model;


class MouvementModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function getEmplacementTypeaheadModel($search, $emplacement_statut_id)
    {
        $sql = "SELECT qr_code_texte
                FROM emplacement_adresse
                JOIN emplacement ON emplacement.id = emplacement_adresse.emplacement_id
                WHERE qr_code_texte ILIKE ?
                    AND emplacement_statut_id = $emplacement_statut_id
                    AND emplacement_adresse.flag_suppression = 0
                    AND emplacement.flag_suppression = 0
                ORDER BY qr_code_texte
                LIMIT 10";
        $res = $this->db->query($sql, ['%' . $search . '%'])->getResult();
        $arr = [];
        foreach ($res as $v) {
            $arr[] = $v->qr_code_texte;
        }
        return $arr;
    }

    public function getPaletteTypeaheadModel($search, $palette_statut_id, $affectee_emplacement)
    {
        $sql = "SELECT article.qr_code_text
                FROM article
                LEFT JOIN palette ON palette.id = article.palette_id
                WHERE article.qr_code_text ILIKE ?
                    AND palette.palette_statut_id = $palette_statut_id 
                    AND article.affectee_emplacement = $affectee_emplacement
                    AND article.flag_suppression = 0
                    AND palette.flag_suppression = 0
                    AND article.actif = 1
                    /*AND article.mouvement_type_id != 2*/
                ORDER BY article.qr_code_text
                LIMIT 10";
        $res = $this->db->query($sql, ['%' . $search . '%'])->getResult();
        $arr = [];
        foreach ($res as $v) {
            $arr[] = $v->qr_code_text;
        }
        return $arr;
    }
}
