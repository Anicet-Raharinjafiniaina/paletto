<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Libraries\LibDataTable;

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
        $emplacement_statut_id = $this->request->getPost('statut_1');
        $sql = "SELECT qr_code_texte
                FROM emplacement
                WHERE qr_code_texte ILIKE ?
                    AND emplacement_statut_id = $emplacement_statut_id
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
        $palette_statut_id = $this->request->getPost('statut_1');
        $affectee_emplacement = $this->request->getPost('statut_2');
        $sql = "SELECT article.qr_code_text
                FROM article
                LEFT JOIN palette ON palette.id = article.palette_id
                WHERE article.qr_code_text ILIKE ?
                    AND palette.palette_statut_id = $palette_statut_id 
                    AND article.affectee_emplacement = $affectee_emplacement
                    AND article.commentaire IS DISTINCT FROM 'Sortie'
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
        $arr = $crud->getDataById(['qr_code_texte' => trim($code), 'emplacement_statut_id' => $statut_id, 'flag_suppression' => 0], [], "id");
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
        $arr = $crud->getDataById(['qr_code_text' => trim($code), TBL_PALETTE . '.palette_statut_id' => $palette_statut_id, TBL_ARTICLE . '.affectee_emplacement' => $affectee_emplacement, TBL_PALETTE . '.flag_suppression' => 0], $arrJoin, TBL_ARTICLE . ".palette_id");
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
            $emplacement_id = $this->getEmplacementIdByQrCode($arr['qr_emplacement_entree'], 1);
            $palette_id = $this->getPaletteIdByQrCode($arr['qr_palette_entree'], 3);
            if (is_null($emplacement_id)) {
                return json_encode(2); // emplacement non valide
            } else if (is_null($palette_id)) {
                return json_encode(3); // palette non valide
            } else {
                $article_id = $this->getArticleId($arr['qr_palette_entree']);
                $result = $crud->create(['mouvement_type_id' => 1, 'emplacement_id' => $emplacement_id, 'palette_id' => $palette_id, 'article_id' => $article_id, 'date_mouvement' => date('Y-m-d H:i:s')], 33);
                $this->majStatutEmplacement($emplacement_id, 2);
                $this->majStatutArticle($arr['qr_palette_entree'], 1, "Entrée");
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

    public function getArticleId($qr_code_text)
    {
        if ($qr_code_text != null && $qr_code_text != "") {
            $crud = new CrudModel(TBL_ARTICLE);
            $arr = $crud->getAllData(['qr_code_text' => $qr_code_text, 'flag_suppression' => 0], [], "id", "id", "", "desc");
            return (!empty($arr)) ? $arr[0]->id : null;
        }
    }
    /** Pour mettre à jour la colonnne affectee_emplacement 
     * - 0 : non affecté à un emplacement * - 1 : affecté à un emplacement *
     */
    public function majStatutArticle($qr_code_text, $statut_id, $commentaire = "")
    {
        if ($qr_code_text != null && $qr_code_text != "") {
            $crud = new CrudModel(TBL_ARTICLE);
            $crud->maj(["qr_code_text" => $qr_code_text], ['affectee_emplacement' => $statut_id, 'commentaire' => $commentaire], 0);
        }
    }

    /*** Maj palette*/
    public function majPalette($id)
    {
        $crud = new CrudModel(TBL_PALETTE);
        $crud->maj(["id" => $id], ['client_code' => null, 'client_nom' => null, 'palette_statut_id' => 1], 0);
    }


    /** Pour la sortie (libérer l'emplacement et la palette) */
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
            $emplacement_id = $this->getEmplacementIdByQrCode($arr['qr_emplacement_sortie'], 2);
            $palette_id = $this->getPaletteIdByQrCode($arr['qr_palette_sortie'], 3, 1);
            if (is_null($emplacement_id)) {
                return json_encode(2); // emplacement non valide
            } else if (is_null($palette_id)) {
                return json_encode(3); // palette non valide
            } else {
                $article_id = $this->getArticleId($arr['qr_palette_sortie']);
                $crud->maj(['emplacement_id' => $emplacement_id, 'palette_id' => $palette_id, 'article_id' => $article_id, 'actif' => 1, 'flag_suppression' => 0], ['actif' => 0], 0);
                $result = $crud->create(['mouvement_type_id' => 2, 'emplacement_id' => $emplacement_id, 'palette_id' => $palette_id, 'article_id' => $article_id, 'date_mouvement' => date('Y-m-d H:i:s')], 34);
                $this->majStatutEmplacement($emplacement_id, 1);
                $this->majStatutArticle($arr['qr_palette_sortie'], 0, "Sortie");
                $this->majPalette($palette_id);
                return json_encode($result);
            }
        }
        return json_encode(0);
    }

    /** Pour le transfert (libérer l'emplacement et affecter à un autre emplacement libre) */
    public function validerTransfert()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(5);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr = $this->request->getVar('data');
        if (!empty($arr)) {
            $crud = new CrudModel(TBL_MOUVEMENT);
            $new_emplacement_id = $this->getEmplacementIdByQrCode($arr['qr_emplacement_transfert'], 1);
            $palette_id = $this->getPaletteIdByQrCode($arr['qr_palette_transfert'], 3, 1);
            if (is_null($new_emplacement_id)) {
                return json_encode(2); // emplacement non valide
            } else if (is_null($palette_id)) {
                return json_encode(3); // palette non valide
            } else {
                $old_emplacement_id = $this->getEmplacementIdByQrCodeText($arr['qr_palette_transfert']);
                $article_id = $this->getArticleId($arr['qr_palette_transfert']);
                $crud->maj(['emplacement_id' => $old_emplacement_id, 'palette_id' => $palette_id, 'article_id' => $article_id, 'actif' => 1], ['actif' => 0], 0);
                $result = $crud->create(['mouvement_type_id' => 3, 'emplacement_id' => $new_emplacement_id, 'palette_id' => $palette_id, 'article_id' => $article_id, 'date_mouvement' => date('Y-m-d H:i:s')], 35);
                $this->majStatutArticle($arr['qr_palette_transfert'], 1, "Transfert");
                $this->majStatutEmplacement($old_emplacement_id, 1); // statut libre
                $this->majStatutEmplacement($new_emplacement_id, 2); // statut occupé
                return json_encode($result);
            }
        }
        return json_encode(0);
    }

    public function getEmplacementIdByQrCodeText($qr_code_text)
    {
        if ($qr_code_text != null && $qr_code_text != "") {
            $crud = new CrudModel(TBL_ARTICLE);
            $arr = $crud->getDataById(['qr_code_text' => trim($qr_code_text), 'affectee_emplacement' => 1, 'flag_suppression' => 0], [], "id,palette_id");
            if (!empty($arr)) {
                $palette_id = $arr->palette_id;
                $article_id = $arr->id;
                $crudEmplacement = new CrudModel(TBL_MOUVEMENT);
                $arrEmplacement = $crudEmplacement->getDataById(['palette_id' => $palette_id, 'article_id' => $article_id, 'mouvement_type_id in (1,3)' => null, 'actif' => 1, 'flag_suppression' => 0], [], "emplacement_id");
                return (!empty($arrEmplacement)) ? $arrEmplacement->emplacement_id : null;
            }
        }
    }

    public function historiqueMouvement()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(5);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $select = [TBL_MOUVEMENT . '.id', TBL_EMPLACEMENT . '.qr_code_texte as emplacement', TBL_ARTICLE . '.qr_code_text as palette_article', TBL_MOUVEMENT_TYPE . '.type', TBL_MOUVEMENT . '.date_mouvement'];
        $searchable = [TBL_EMPLACEMENT . '.qr_code_texte', TBL_ARTICLE . '.qr_code_text', TBL_MOUVEMENT_TYPE . '.type'];

        $arrJoin = [
            [
                'table' => TBL_EMPLACEMENT,
                'condition' => TBL_EMPLACEMENT . '.id = ' . TBL_MOUVEMENT . '.emplacement_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_ARTICLE,
                'condition' => TBL_ARTICLE . '.id = ' . TBL_MOUVEMENT . '.article_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_PALETTE,
                'condition' => TBL_PALETTE . '.id = ' . TBL_MOUVEMENT . '.palette_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_MOUVEMENT_TYPE,
                'condition' => TBL_MOUVEMENT_TYPE . '.id = ' . TBL_MOUVEMENT . '.mouvement_type_id',
                'type' => 'left'
            ]
        ];
        $where = [
            TBL_EMPLACEMENT . '.flag_suppression' => 1
        ];
        $libDataTable = new LibDataTable();
        return $libDataTable->index(TBL_MOUVEMENT, $select, $searchable, ["voir"], $arrJoin, $where);
    }

    public function getDetailMouvement()
    {
        $id = $this->request->getPost('id');
        $crud = new CrudModel(TBL_MOUVEMENT);
        $arrJoin = [
            [
                'table' => TBL_EMPLACEMENT,
                'on' => TBL_EMPLACEMENT . '.id = ' . TBL_MOUVEMENT . '.emplacement_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_ARTICLE,
                'on' => TBL_ARTICLE . '.id = ' . TBL_MOUVEMENT . '.article_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_PALETTE,
                'on' => TBL_PALETTE . '.id = ' . TBL_MOUVEMENT . '.palette_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_MOUVEMENT_TYPE,
                'on' => TBL_MOUVEMENT_TYPE . '.id = ' . TBL_MOUVEMENT . '.mouvement_type_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_UTILISATEUR,
                'on' => TBL_UTILISATEUR . '.id = ' . TBL_MOUVEMENT . '.cree_par',
                'type' => 'left'
            ]
        ];
        $select = [TBL_MOUVEMENT . '.id', TBL_EMPLACEMENT . '.qr_code_texte as emplacement', TBL_PALETTE . '.code as palette_code', TBL_ARTICLE . '.client_code', TBL_ARTICLE . '.client_nom', TBL_ARTICLE . '.code as article_code', TBL_ARTICLE . '.nom as article_nom', TBL_ARTICLE . '.dluo', TBL_ARTICLE . '.unite_pcb', TBL_ARTICLE . '.quantite', TBL_ARTICLE . '.lot',  TBL_ARTICLE . '.palettisation', TBL_ARTICLE . '.commentaire', TBL_MOUVEMENT_TYPE . '.type as mouvement_type', TBL_MOUVEMENT . '.date_mouvement', TBL_UTILISATEUR . '.nom as auteur'];
        $arr['data'] = $crud->getDataById([TBL_MOUVEMENT . '.id' => $id], $arrJoin, $select);
        echo view('mouvement/detail', $arr);
    }
}
