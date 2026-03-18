<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;

class ListeEmplacement extends BaseController
{
    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $this->load();
    }

    /** Liste des emplacements */
    public function load()
    {
        $arr['arr_data_emplacement'] = $this->getAllEmplacement();
        $arr['titre'] = "Liste des emplacements";
        $arr['menu_emplacement'] = 'Liste emplacement';
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('emplacement_adresse/list_view', $arr);
            return;
        }
        echo view('emplacement_adresse/list_view', $arr);
    }

    public function getListeEmplacementById($emplacement_id = null)
    {
        $crud = new CrudModel(VIEW_EMPLACEMENT_ADRESSE);
        $arr = [];
        if ($emplacement_id != null) {
            $arr = $crud->getDataById(array('emplacement_id' => $emplacement_id));
        }
        return  $arr;
    }

    public function getAllEmplacement()
    {
        $crud = new CrudModel(VIEW_EMPLACEMENT_ADRESSE);
        return  $crud->getAllData();
    }

    public function checkListeEmplacementById($emplacement_id = null)
    {
        $crud = new CrudModel(TBL_EMPLACEMENT_ADRESSE);
        $nb = 0;
        if ($emplacement_id != null) {
            $nb = $crud->getNb(array('emplacement_id' => $emplacement_id, "flag_suppression" => 0));
        }
        return  $nb;
    }

    public function updatetEmplacement($id = null, $action = null)
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        if ($id == null || $id == "") {
            return;
        }
        $arrEmplacement = $this->getListeEmplacementById($id);
        $isEmplacementExist = $this->checkListeEmplacementById($id); // emplacement exist?
        if (!empty($arrEmplacement)) {
            $qrCodeController = new QrCodeController();
            $qr_code_image = $qrCodeController->generateBase64($arrEmplacement->qr_code_texte);
            $crudEmplacement = new CrudModel(TBL_EMPLACEMENT_ADRESSE);
            if ($isEmplacementExist == 0) {
                return $crudEmplacement->create(array("emplacement_id" => $id, "qr_code_texte" => $arrEmplacement->qr_code_texte, "qr_code_image" => $qr_code_image), $action);
            } else {
                return $crudEmplacement->maj(array("emplacement_id" => $id), array("qr_code_texte" => $arrEmplacement->qr_code_texte, "qr_code_image" => $qr_code_image, "emplacement_statut_id" => 1), $action);
            }
        }
    }

    public function getDetailEmplacement()
    {
        $id = $this->request->getPost('id');
        $crud = new CrudModel(VIEW_EMPLACEMENT_ADRESSE);
        $arrJoin = array(
            array("table" => TBL_EMPLACEMENT_STATUT, "on" => VIEW_EMPLACEMENT_ADRESSE . ".statut_id = " . TBL_EMPLACEMENT_STATUT . ".id", "type" => "left"),
        );
        $arr['data'] = $crud->getDataById(['emplacement_id' => $id], $arrJoin, "*");
        echo view('emplacement_adresse/detail', $arr);
    }

    /** /Liste des emplacements */

    /** Gestion emplacement  */
    // public function getListeEmplacement() // afficher la listes des emplacements 
    // {}

    /** /Gestion emplacement  */
}
