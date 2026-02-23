<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;

class Emplacement extends BaseController
{
    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $arr['arr_data_emplacement'] = $this->getAllEmplacement();
        $arr['titre'] = "Gestion des emplacements";
        $arr['menu_emplacement'] = 'liste emplacement';
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('emplacement/list_view', $arr);
            return;
        }
        echo view('emplacement/list_view', $arr);
    }

    public function getEmplacementById($cage_id = null)
    {
        $crud = new CrudModel(VIEW_EMPLACEMENT);
        $arr = [];
        if ($cage_id != null) {
            $arr = $crud->getDataById(array('cage_id' => $cage_id));
        }
        return  $arr;
    }

    public function getAllEmplacement()
    {
        $crud = new CrudModel(VIEW_EMPLACEMENT);
        return  $crud->getAllData();
    }

    public function checkEmplacementByCageId($cage_id = null)
    {
        $crud = new CrudModel(TBL_EMPLACEMENT);
        $nb = 0;
        if ($cage_id != null) {
            $nb = $crud->getNb(array('cage_id' => $cage_id, "flag_suppression" => 0));
        }
        return  $nb;
    }

    public function updatetEmplacement($id = null, $action = null)
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        if ($id == null || $id == "") {
            return;
        }
        $arrEmplacement = $this->getEmplacementById($id);
        $isEmplacementExist = $this->checkEmplacementByCageId($id);
        if (!empty($arrEmplacement)) {
            $qrCodeController = new QrCodeController();
            $qr_code_image = $qrCodeController->generateBase64($arrEmplacement->qr_code_texte);
            $crudEmplacement = new CrudModel(TBL_EMPLACEMENT);
            if ($isEmplacementExist == 0) {
                return $crudEmplacement->create(array("cage_id" => $id, "qr_code_texte" => $arrEmplacement->qr_code_texte, "qr_code_image" => $qr_code_image), $action);
            } else {
                return $crudEmplacement->maj(array("cage_id" => $id), array("qr_code_texte" => $arrEmplacement->qr_code_texte, "qr_code_image" => $qr_code_image, "emplacement_statut_id" => 1), $action);
            }
        }
    }

    public function getDetailEmplacement()
    {
        $id = $this->request->getPost('id');
        $crud = new CrudModel(VIEW_EMPLACEMENT);
        $arrJoin = array(
            array("table" => TBL_EMPLACEMENT_STATUT, "on" => VIEW_EMPLACEMENT . ".statut_id = " . TBL_EMPLACEMENT_STATUT . ".id", "type" => "left"),
        );
        $arr['data'] = $crud->getDataById(['emplacement_id' => $id], $arrJoin, "*");
        echo view('emplacement/detail', $arr);
    }

    public function gererEmplacement() {}
}
