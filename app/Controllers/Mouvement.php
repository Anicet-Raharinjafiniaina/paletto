<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Palette;
use App\Controllers\QrCodeController;

class Mouvement extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(5);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Gestion des mouvements";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('mouvement/list_view', $arr);
            return;
        }
        echo view('mouvement/list_view', $arr);
    }


    public function getEmplacementTypeahead()
    {
        $search = trim($this->request->getPost('code') ?? '');
        $sql = "SELECT qr_code_texte
                FROM emplacement
                WHERE qr_code_texte ILIKE ?
                    AND emplacement_statut_id = 1
                ORDER BY qr_code_texte
                LIMIT 10";
        $res = $this->db->query($sql, [$search . '%'])->getResult();
        $arr = [];
        foreach ($res as $v) {
            $arr[] = $v->qr_code_texte;
        }
        return json_encode($arr);
    }

    /** Palette occupée par un article */
    public function getPaletteTypeahead()
    {
        $search = trim($this->request->getPost('code') ?? '');
        $sql = "SELECT article.qr_code_text
                FROM article
                LEFT JOIN palette ON palette.id = article.palette_id
                WHERE article.qr_code_text ILIKE ?
                    AND palette.palette_statut_id = 3 
                    AND article.affectee_emplacement = 0
                ORDER BY article.qr_code_text
                LIMIT 10";
        $res = $this->db->query($sql, [$search . '%'])->getResult();
        $arr = [];
        foreach ($res as $v) {
            $arr[] = $v->qr_code_text;
        }
        return json_encode($arr);
    }

    /*** QR code de l'emplacement */
    public function getEmplacementIdByQrCode($code, $statut_id)
    {
        $crud = new CrudModel(TBL_EMPLACEMENT);
        $arr = $crud->getDataById(['qr_code_texte' => trim($code), 'emplacement_statut_id' => $statut_id], [], "id");
        return (!empty($arr)) ? $arr->id : null;
    }

    /** QR code palette-article-client (palette accupée et non affecté à un emplacement) */
    public function getPaletteIdByQrCode($code, $palette_statut_id, $affectee_emplacement = 0)
    {
        $crud = new CrudModel(TBL_ARTICLE);
        $arrJoin = [[
            'table' => TBL_PALETTE,
            'type'  => 'LEFT',
            'on'    => TBL_PALETTE . '.id = ' . TBL_ARTICLE . '.palette_id'
        ]];
        $arr = $crud->getDataById(['qr_code_text' => trim($code), TBL_PALETTE . '.palette_statut_id' => $palette_statut_id, TBL_ARTICLE . '.affectee_emplacement' => $affectee_emplacement], $arrJoin, TBL_ARTICLE . ".palette_id");
        return (!empty($arr)) ? $arr->palette_id : null;
    }


    /** Pour l'entrée */
    public function validerEntree()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(5);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr = $this->request->getVar('data');
        if (!empty($arr)) {
            $crud = new CrudModel(TBL_MOUVEMENT);
            $emplacement_id = $this->getEmplacementIdByQrCode($arr['qr_emplacement'], 1);
            $palette_id = $this->getPaletteIdByQrCode($arr['qr_palette'], 3);
            if (is_null($emplacement_id)) {
                return json_encode(2); // emplacement non valide
            } else if (is_null($palette_id)) {
                return json_encode(3); // palette non valide
            } else {
                $result = $crud->create(['mouvement_type_id' => 1, 'emplacement_id' => $emplacement_id, 'palette_id' => $palette_id], 33);
                $this->majStatutEmplacement($emplacement_id, 2);
                $this->majStatutArticle($arr['qr_palette'], 1);
                return json_encode($result);
            }
        }
        return json_encode(0);
    }

    public function majStatutEmplacement($id, $statut_id)
    {
        if ($id != null && $id != "") {
            $crud = new CrudModel(TBL_EMPLACEMENT);
            $crud->maj(["id" => $id], ['emplacement_statut_id' => $statut_id], 0);
        }
    }

    /** Pour mettre à jour la colonnne affectee_emplacement 
     * - 0 : non affecté à un emplacement * - 1 : affecté à un emplacement *
     */
    public function majStatutArticle($qr_code_text, $statut_id)
    {
        if ($qr_code_text != null && $qr_code_text != "") {
            $crud = new CrudModel(TBL_ARTICLE);
            $crud->maj(["qr_code_text" => $qr_code_text], ['affectee_emplacement' => $statut_id], 0);
        }
    }

    /** Pour la sortie (libérer l'emplacement) */
    public function validerSortie()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(5);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr = $this->request->getVar('data');
        if (!empty($arr)) {
            $crud = new CrudModel(TBL_MOUVEMENT);
            $emplacement_id = $this->getEmplacementIdByQrCode($arr['qr_emplacement'], 2);
            $palette_id = $this->getPaletteIdByQrCode($arr['qr_palette'], 3, 1);
            if (is_null($emplacement_id)) {
                return json_encode(2); // emplacement non valide
            } else if (is_null($palette_id)) {
                return json_encode(3); // palette non valide
            } else {
                $result = $crud->create(['mouvement_type_id' => 1, 'emplacement_id' => $emplacement_id, 'palette_id' => $palette_id], 33);
                $this->majStatutEmplacement($emplacement_id, 2);
                $this->majStatutArticle($arr['qr_palette'], 1);
                return json_encode($result);
            }
        }
        return json_encode(0);
    }
}
