<?php

namespace App\Controllers;

use App\Controllers\Acces;
use App\Controllers\Palette;
use App\Controllers\QrCodeController;
use App\Models\ArticleModel;
use App\Models\CrudModel;

/**
 *  Gestion des articles liés au client et à la palette
 *   */
class Article extends BaseController
{
    // protected $dbX3;
    // protected $db;

    // public function __construct()
    // {
    //     $this->db = db_connect();
    //     $this->dbX3 = db_connect('connex_v12');
    // }

    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $this->load();
    }

    public function load()
    {
        $model = new ArticleModel();
        $arr['arr_article'] = $model->getAllArticle();
        $palette = new Palette();
        $arr['arr_client'] = $palette->getAllClient();
        $arr['titre'] = "Gestion des palettes";
        $arr['menu_palette'] = "Attribution palette";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('article/list_view', $arr);
            return;
        }
        echo view('article/list_view', $arr);
    }

    // public function getAllArticle()
    // {
    //     $crud = new CrudModel(TBL_ARTICLE);
    //     $arrJoin = [
    //         [
    //             'table' => TBL_PALETTE,
    //             'type'  => 'LEFT',
    //             'on'    => TBL_PALETTE . '.id = ' . TBL_ARTICLE . '.palette_id'
    //         ],
    //     ];
    //     $select = TBL_ARTICLE . '.id,' . TBL_ARTICLE . '.code,' . TBL_ARTICLE . '.nom,' . TBL_ARTICLE . '.client_nom,' . TBL_ARTICLE . '.qr_code_text';
    //     return  $crud->getAllData([TBL_ARTICLE . '.flag_suppression' => 0, TBL_PALETTE . '.palette_statut_id' => 3, TBL_PALETTE . '.flag_suppression' => 0, /*TBL_ARTICLE . '.affectee_emplacement' => 0,*/ TBL_ARTICLE . '.mouvement_type_id' => null], $arrJoin, $select);
    // }

    public function getAllPaletteNoTOccuped()
    {
        $crud = new CrudModel(TBL_PALETTE);
        $arr =  $crud->getAllData(['palette_statut_id != 3' => null, 'flag_suppression' => 0], [], "id, code as text");
        return json_encode($arr);
    }

    // public function getAllArticle1() // venant de X3
    // {
    //     $crud = new CrudModel('BASANEXP.ITMMASTER', 'x3');
    //     $a =  $crud->getAllData(['ITMREF_0' => 200019783], [], "*", "", "", "", "", 1);
    //     echo '<pre>';
    //     print_r($a);
    //     echo '</pre>';
    // }

    public function getUnitePCB() // PCU
    {
        $code = trim($this->request->getPost('code'));
        $crud = new CrudModel('BASANEXP.ITMMASTER', 'x3');
        $arrDataPCU = $crud->getDataById(['ITMREF_0' => $code], [], 'PCU_0,PCU_1,PCU_2,PCU_3,PCU_4,PCU_5');
        $arrPCU = [];
        foreach ($arrDataPCU as $v) {
            if (!empty(trim($v))) {
                $arrPCU[] = [
                    "id" => $v,
                    "text" => $v
                ];
            }
        }
        return json_encode($arrPCU);
    }

    public function getPalettisation()
    {
        $code = trim($this->request->getPost('code'));
        $crud = new CrudModel('BASANEXP.ITMMASTER', 'x3');
        $arrData = $crud->getDataById(['ITMREF_0' => $code], [], 'PCU_0,PCU_1,PCU_2,PCU_3,PCU_4,PCU_5,PCUSTUCOE_0,PCUSTUCOE_1,PCUSTUCOE_2,PCUSTUCOE_3,PCUSTUCOE_4,PCUSTUCOE_5');

        $arrPal = [];
        for ($i = 0; $i <= 5; $i++) {
            $key = $arrData->{'PCU_' . $i};
            $value = $arrData->{'PCUSTUCOE_' . $i};
            if (!empty(trim($key))) {
                $arrPal[$key] = (float) $value;
            }
        }
        return json_encode($arrPal);
    }


    public function getUnitePCBHorsX3() // PCU
    {
        $code = trim($this->request->getPost('code'));
        $crud = new CrudModel(TBL_ARTICLE_HORS_X3);
        $arrDataPCU = $crud->getDataById(['code' => $code], [], 'unite_pcb');
        $arrDataPCU = explode(',', trim($arrDataPCU->unite_pcb, '{}'));
        $arrPCU = [];
        foreach ($arrDataPCU as $v) {
            if (!empty(trim($v))) {
                $arrPCU[] = [
                    "id" => $v,
                    "text" => $v
                ];
            }
        }
        return json_encode($arrPCU);
    }

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
        $model = new ArticleModel();
        $arr = $model->getArticleTypeaheadModel($search);
        return json_encode($arr);
    }

    public function getArticleHorsX3Typeahead()
    {
        $search = trim($this->request->getPost('code') ?? '');
        $model = new ArticleModel();
        $arr = $model->getArticleHorsX3TypeaheadModel($search);
        return json_encode($arr);
    }

    public function getDetailArticleByCode()
    {
        $code = trim($this->request->getPost('code') ?? '');
        $model = new ArticleModel();
        $res = $model->getDetailArticleByCodeModel($code);
        return json_encode($res);
    }

    public function getDetailArticleHorsX3ByCode()
    {
        $code = trim($this->request->getPost('code') ?? '');
        $model = new ArticleModel();
        $res = $model->getDetailArticleHorsX3ByCodeModel($code);
        return json_encode($res);
    }

    public function insertArticle()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
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
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $crud = new CrudModel(TBL_ARTICLE);
        $arrJoin = [[
            'table' => TBL_PALETTE,
            'type'  => 'LEFT',
            'on'    => TBL_PALETTE . '.id = ' . TBL_ARTICLE . '.palette_id'
        ]];
        $select = TBL_ARTICLE . '.id,' . TBL_ARTICLE . '.code,' . TBL_ARTICLE . '.nom,' . TBL_ARTICLE . '.client_code,' . TBL_ARTICLE . '.client_nom,' . TBL_ARTICLE . '.quantite,' . TBL_ARTICLE . '.lot,' . TBL_ARTICLE . '.dluo,' . TBL_ARTICLE . '.unite_pcb,' . TBL_ARTICLE . '.palettisation,' . TBL_ARTICLE . '.unite_stockage,'  . TBL_ARTICLE . '.observation,' . TBL_ARTICLE . '.qr_code_text,' . TBL_ARTICLE . '.qr_code_image,' . TBL_PALETTE . '.code as palette_code';
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arrData = $crud->getDataById(array(TBL_ARTICLE . '.id' => intval($id)), $arrJoin, $select);
        $arr["action"] = $action;
        $arr["data"] = $arrData;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('article/detail', $arr);
    }
}
