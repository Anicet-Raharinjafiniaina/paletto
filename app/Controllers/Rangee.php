<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Libraries\LibLdap;


class Rangee extends BaseController
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
        $crud = new CrudModel(TBL_RANGEE);
        $arrJoin = [
            [
                'table' => TBL_ENTREPOT,
                'type'  => 'LEFT',
                'on'    => TBL_ENTREPOT . '.id = ' . TBL_RANGEE . '.entrepot_id'
            ],
            [
                'table' => TBL_ALLEE,
                'type'  => 'LEFT',
                'on'    => TBL_ALLEE . '.id = ' . TBL_RANGEE . '.allee_id'
            ],
        ];
        $select = TBL_RANGEE . ".id, " . TBL_RANGEE . ".code, "  . TBL_ALLEE . ".code AS allee," . "CONCAT(" . TBL_ENTREPOT . ".code, ' - ', " . TBL_ENTREPOT . ".nom) AS entrepot";
        $arr['arr_data_rangee'] = $crud->getAllData(array(TBL_RANGEE . '.flag_suppression' => 0), $arrJoin, $select);
        $arr['titre'] = "Gestion des emplacements";
        $arr['arr_data_entrepot'] = $this->getAllEntrepot();
        // $arr['arr_data_allee'] = $this->getAllAllee();
        $arr['menu_emplacement'] = 'rangee';
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('rangee/list_view', $arr);
            return;
        }
        echo view('rangee/list_view', $arr);
    }

    public function getAllEntrepot()
    {
        $crud = new CrudModel(TBL_ENTREPOT);
        $arr_data_entrepot = $crud->getAllData(array('flag_suppression' => 0), [], "id,code,nom");
        return $arr_data_entrepot;
    }

    public function getAllAllee()
    {
        $crud = new CrudModel(TBL_ALLEE);
        $arr_data_allee = $crud->getAllData(array('flag_suppression' => 0), [], "id,code");
        return $arr_data_allee;
    }

    public function getAllAlleeByEntrepot()
    {
        $entrepot_id = $this->request->getVar('entrepot_id');
        if ($entrepot_id == "" || $entrepot_id == null) {
            return json_encode([]);
        }
        $crud = new CrudModel(TBL_ALLEE);
        $arr_data_allee = $crud->getAllData(array('flag_suppression' => 0, 'entrepot_id' => $entrepot_id), [], "id,code AS text");
        return json_encode($arr_data_allee);
    }

    public function insertRangee()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr = $this->request->getVar('data');
        if (!empty($arr)) {
            $crud = new CrudModel(TBL_RANGEE);
            $is_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr['code'])), "entrepot_id" => $arr['entrepot_id'], "allee_id" => $arr['allee_id'], "flag_suppression" => 0));
            if ($is_exist > 0) {
                return json_encode(2); // code doublon
            } else {
                $result = $crud->create($arr, 15);
                return json_encode(intVal($result));
            }
        }
        return json_encode(0);
    }

    /**
     * Visualisation d'un détail
     */
    public function getRangee()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_RANGEE);

        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arrData = $crud->getDataById(array('id' => intval($id)));
        $arr['arr_data_entrepot'] = $this->getAllEntrepot();
        // $arr['arr_data_allee'] = $this->getAllAllee();
        $arr["errors"] = array();
        $arr["action"] = $action;
        $arr["data"] = $arrData;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('rangee/maj_view', $arr);
    }

    public function majRangee()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr_data = $this->request->getVar('data');
        $crud = new CrudModel(TBL_RANGEE);
        if (!empty($arr_data)) {
            unset($arr_data['allee_id_base']);
            $is_code_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr_data['code'])), "entrepot_id" => $arr_data['entrepot_id'], "allee_id" => $arr_data['allee_id'], "id != " . $arr_data['id'] => null, "flag_suppression" => 0));
            $is_data_exist = $crud->getNb($arr_data);
            if ($is_code_exist > 0) {
                return json_encode(2); // code doublon
            } else if ($is_data_exist > 0) {
                return json_encode(3); // aucune modification
            } else {
                $id = $arr_data['id'];
                unset($arr_data['id']);
                $result = $crud->maj(["id" => $id], $arr_data, 16);
                return json_encode($result);
            }
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteRangee()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_RANGEE);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 17);
            return json_encode($result);
        }
        return json_encode(0);
    }
}
