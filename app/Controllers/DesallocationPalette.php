<?php

namespace App\Controllers;

use App\Controllers\Acces;
use App\Models\ArticleModel;
use App\Models\CrudModel;

class DesallocationPalette extends BaseController
{
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
        $arr['titre'] = "Désallocation de palette";
        $arr['menu_palette'] = "Désallocation palette";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('desallocation_palette/list_view', $arr);
            return;
        }
        echo view('desallocation_palette/list_view', $arr);
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
        echo view('desallocation_palette/detail', $arr);
    }

    public function libererPalette()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $estAffecte = $this->checkEmplacement($id);
            if ($estAffecte == true) { // déjà affecté à un emplacement
                return json_encode(2);
            } else {
                $crudArticle = new CrudModel(TBL_ARTICLE);
                $arrArticle = $crudArticle->getDataById(["id" => $id]);
                $crudArticle->maj(["id" => $id], ["actif" => 0, "flag_suppression" => 1], 0);
                $crud = new CrudModel(TBL_PALETTE);
                $result = $crud->maj(["id" => $arrArticle->palette_id], ["palette_statut_id" => 1, "client_code" => null, "client_nom" => null], 32);
                return json_encode($result);
            }
        }
        return json_encode(0);
    }

    /** Vérification si c'est déjà affecté à un emplacement */
    public function checkEmplacement($idArticle = null)
    {
        if ($idArticle != null) {
            $crud = new CrudModel(TBL_ARTICLE);
            $arr = $crud->getDataById(['id' => $idArticle]);
            if ($arr->affectee_emplacement == 1) { // déjà affecté
                return true;
            }
            return false;
        }
        return false;
    }
}
