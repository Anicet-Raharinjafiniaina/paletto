<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Palette;
use App\Controllers\QrCodeController;

class Mouvement extends BaseController
{
    protected $dbX3;

    public function __construct()
    {
        $this->dbX3 = db_connect('connex_v12');
    }

    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $arr['arr_article'] = $this->getAllArticle();
        $arr['arr_palette'] = $this->getAllPaletteNoTOccuped();
        $palette = new Palette();
        $arr['arr_client'] = $palette->getAllClient();
        $arr['titre'] = "Gestion des mouvements";
        $arr['menu_palette'] = "Attribution palette";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('mouvement/list_view', $arr);
            return;
        }
        echo view('mouvement/list_view', $arr);
    }

    public function getAllArticle()
    {
        $crud = new CrudModel(TBL_ARTICLE);
        $arrJoin = [[
            'table' => TBL_PALETTE,
            'type'  => 'LEFT',
            'on'    => TBL_PALETTE . '.id = ' . TBL_ARTICLE . '.palette_id'
        ]];
        $select = TBL_ARTICLE . '.id,' . TBL_ARTICLE . '.code,' . TBL_ARTICLE . '.nom,' . TBL_ARTICLE . '.client_nom,' . TBL_ARTICLE . '.qr_code_text';
        return  $crud->getAllData([TBL_PALETTE . '.palette_statut_id' => 3, TBL_PALETTE . '.flag_suppression' => 0], $arrJoin, $select);
    }

    public function getAllPaletteNoTOccuped()
    {
        $crud = new CrudModel(TBL_PALETTE);
        return  $crud->getAllData(['palette_statut_id != 3' => null, 'flag_suppression' => 0], [], "id, code");
    }

    // public function getAllArticle() // venant de X3
    // {
    //     $crud = new CrudModel('BASANEXP.ITMMASTER', 'x3');
    //     $a =  $crud->getAllData([], [], "*", "", "", "", "", 1);
    //     echo '<pre>';
    //     print_r($a);
    //     echo '</pre>';
    // }

    public function getClientForPalette()
    {
        $palette_id = trim($this->request->getPost('palette_id') ?? '');
        $crud = new CrudModel(TBL_PALETTE);
        $data = $crud->getDataById(['id' => intval($palette_id)], [], "client_code, client_nom");
        return json_encode($data);
    }

    public function getArticleTypeahead()
    {
        $search = trim($this->request->getPost('code') ?? '');

        $sql = "SELECT TOP 10
                    ITMREF_0 AS id
                FROM BASANEXP.ITMMASTER
                WHERE ITMREF_0 LIKE ? COLLATE SQL_Latin1_General_CP1_CI_AS
                ORDER BY ITMREF_0";

        $res = $this->dbX3->query($sql, [$search . '%'])->getResult();
        $arr = [];
        foreach ($res as $k => $v) :
            array_push($arr, $v->id);
        endforeach;
        return json_encode($arr);
    }


    public function getDetailArticleByCode()
    {
        $code = trim($this->request->getPost('code') ?? '');
        $sql = "SELECT TOP 1
                    ITMREF_0 AS code,
                    ITMDES1_0 AS libelle,
                    ZPCB_0 AS pcb,
                    ACCCOD_0 AS palettisation
                FROM BASANEXP.ITMMASTER
                WHERE ITMREF_0 = ? COLLATE SQL_Latin1_General_CP1_CI_AS";

        $res = $this->dbX3->query($sql, [$code])->getRow();
        return json_encode($res);
    }

    public function insertArticle()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr = $this->request->getVar('data');
        if (!empty($arr)) {
            $palette = new Palette();
            $arr = $palette->splitClient($arr);
            $arr['dluo'] = $this->normalizeDate($arr['dluo']);
            $crud = new CrudModel(TBL_ARTICLE);
            $isStatutOccupe = $this->isStatutOccupe($arr['palette_id']);
            if ($isStatutOccupe == true) {
                return json_encode(2); // code doublon
            } else {
                $arr = $this->generateQrCode($arr); // génération du QR code et ajout de l'image en base64 et du texte dans le tableau $arr
                $result = $crud->create($arr, 30);
                if ($result == 1) {
                    $this->majInfosPalette($arr['palette_id'], $arr['client_code'], $arr['client_nom']); // maj du statut de la palette à "occupé" si la maj est réussie et aussi maj du client_code et client_nom de la palette
                }
                return json_encode(intVal($result));
            }
        }
        return json_encode(0);
    }

    public function generateQrCode($arr)
    {
        $qr = new QrCodeController();
        $texte = $this->getCodePaletteById($arr['palette_id']) . '-' . $arr['code'] . '-' . $arr['client_code'];
        $image = $qr->generateBase64($texte);
        $arr['qr_code_image'] = $image;
        $arr['qr_code_text']   = $texte;
        return $arr;
    }

    public function getCodePaletteById($id)
    {
        $crud = new CrudModel(TBL_PALETTE);
        $data = $crud->getDataById(['id' => intval($id)], [], "code");
        return $data->code;
    }

    /**
     * Pour avoir DD-MM-YYYY à partir de YYYY-MM-DD et aussi pour retourner la date telle quelle si elle n'est pas au format attendu
     */
    function normalizeDate($date)
    {
        $date = trim($date);
        // Vérifie si la date est au format dd/mm/yyyy
        if (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $date, $matches)) {
            // Transforme en yyyy-mm-dd
            return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
        }
        // Sinon on retourne la date telle quelle
        return $date;
    }

    public function majInfosPalette($id, $client_code = null, $client_nom = null)
    {
        if ($id != null && $id != "") {
            $crud = new CrudModel(TBL_PALETTE);
            $crud->maj(["id" => $id], ['palette_statut_id' => 3, 'client_code' => $client_code, 'client_nom' => $client_nom], 0);
        }
    }

    public function isStatutOccupe($id)
    {
        $crud = new CrudModel(TBL_PALETTE);
        $data = $crud->getDataById(['id' => $id], [], "palette_statut_id");
        if ($data != null && $data->palette_statut_id == 3) {
            return true;
        }
        return false;
    }


    /**
     * Visualisation d'un détail
     */
    public function getArticle()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_ARTICLE);
        $arrJoin = [[
            'table' => TBL_PALETTE,
            'type'  => 'LEFT',
            'on'    => TBL_PALETTE . '.id = ' . TBL_ARTICLE . '.palette_id'
        ]];
        $select = TBL_ARTICLE . '.id,' . TBL_ARTICLE . '.code,' . TBL_ARTICLE . '.nom,' . TBL_ARTICLE . '.client_code,' . TBL_ARTICLE . '.client_nom,' . TBL_ARTICLE . '.quantite,' . TBL_ARTICLE . '.lot,' . TBL_ARTICLE . '.dluo,' . TBL_ARTICLE . '.unite_pcb,' . TBL_ARTICLE . '.palettisation,' . TBL_ARTICLE . '.qr_code_image,' . TBL_PALETTE . '.code as palette_code';
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arrData = $crud->getDataById(array(TBL_ARTICLE . '.id' => intval($id)), $arrJoin, $select);
        $arr["action"] = $action;
        $arr["data"] = $arrData;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('article/maj_view', $arr);
    }
}
