<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Libraries\LibLdap;


class Emplacement extends BaseController
{
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->db = db_connect();
        $this->session = \Config\Services::session();
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
        $is_ok = $acces->is_ok(4);
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

    /**
     * Visualisation d'un détail
     */
    public function getEntrepot()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_ENTREPOT);

        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arrData = $crud->getDataById(array('id' => intval($id)));
        $arr["errors"] = array();
        $arr["action"] = $action;
        $arr["data"] = $arrData;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('entrepot/maj_entrepot_view', $arr);
    }


    public function majEntrepot()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr_data = $this->request->getVar('data');
        $crud = new CrudModel(TBL_ENTREPOT);
        if (!empty($arr_data)) {
            $is_code_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr_data['code'])), "id != " . $arr_data['id'] => null, "flag_suppression" => 0));
            $is_data_exist = $crud->getNb($arr_data);
            if ($is_code_exist > 0) {
                return json_encode(2); // code doublon
            } else if ($is_data_exist > 0) {
                return json_encode(3); // aucune modification
            } else {
                $id = $arr_data['id'];
                unset($arr_data['id']);
                $result = $crud->maj(["id" => $id], $arr_data, 10);
                return json_encode($result);
            }
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteEntrepot()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_ENTREPOT);
            $arr_base = $crud->getDataById(array("id = " . $id => null));
            if ($arr_base->emplacement > 0) {
                return json_encode(2); // l'entrepôt contient encore un ou des emplacements (c'est pas supprimable)
            }
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 11);
            return json_encode($result);
        }
        return json_encode(0);
    }
}
