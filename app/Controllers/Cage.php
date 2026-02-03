<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Niveau;
use App\Controllers\Rangee;
use App\Controllers\QrCodeController;

class Cage extends BaseController
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
        $crud = new CrudModel(TBL_CAGE);
        $arrJoin = [
            [
                'table' => TBL_ENTREPOT,
                'type'  => 'LEFT',
                'on'    => TBL_ENTREPOT . '.id = ' . TBL_CAGE . '.entrepot_id'
            ],
            [
                'table' => TBL_ALLEE,
                'type'  => 'LEFT',
                'on'    => TBL_ALLEE . '.id = ' . TBL_CAGE . '.allee_id'
            ],
            [
                'table' => TBL_RANGEE,
                'type'  => 'LEFT',
                'on'    => TBL_RANGEE . '.id = ' . TBL_CAGE . '.rangee_id'
            ],
            [
                'table' => TBL_NIVEAU,
                'type'  => 'LEFT',
                'on'    => TBL_NIVEAU . '.id = ' . TBL_CAGE . '.niveau_id'
            ],
        ];
        $select = TBL_CAGE . ".id, " . TBL_CAGE . ".code, " . TBL_NIVEAU . ".code AS niveau," . TBL_RANGEE . ".code AS rangee," . TBL_ALLEE . ".code AS allee," . "CONCAT(" . TBL_ENTREPOT . ".code, ' - ', " . TBL_ENTREPOT . ".nom) AS entrepot";
        $arr['arr_data_cage'] = $crud->getAllData(array(TBL_CAGE . '.flag_suppression' => 0), $arrJoin, $select);
        $arr['titre'] = "Gestion des emplacements";
        $rangee = new Rangee();
        $arr['arr_data_entrepot'] = $rangee->getAllEntrepot();
        $arr['menu_emplacement'] = 'cage';
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('cage/list_view', $arr);
            return;
        }
        echo view('cage/list_view', $arr);
    }

    public function getAllNiveauByRangee()
    {
        $entrepot_id = $this->request->getVar('entrepot_id');
        $allee_id = $this->request->getVar('allee_id');
        $rangee_id = $this->request->getVar('rangee_id');
        if (($allee_id == "" || $allee_id == null) || ($entrepot_id == "" || $entrepot_id == null) || ($rangee_id == "" || $rangee_id == null)) {
            return json_encode([]);
        }
        $crud = new CrudModel(TBL_NIVEAU);
        $arr_data_niveau = $crud->getAllData(array('flag_suppression' => 0, 'entrepot_id' => $entrepot_id, 'allee_id' => $allee_id, 'rangee_id' => $rangee_id), [], "id,code AS text");
        return json_encode($arr_data_niveau);
    }

    public function insertCage()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr = $this->request->getVar('data');
        if (!empty($arr)) {
            $crud = new CrudModel(TBL_CAGE);
            $is_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr['code'])), "entrepot_id" => $arr['entrepot_id'], "allee_id" => $arr['allee_id'], "rangee_id" => $arr['rangee_id'], "niveau_id" => $arr['niveau_id'], "flag_suppression" => 0));
            if ($is_exist > 0) {
                return json_encode(2); // code doublon
            } else {
                $id = $crud->createReturnId($arr, 21); // insertion cage
                $emplacement = new Emplacement();
                $result = $emplacement->updatetEmplacement($id, 24); // maj emplacement
                return json_encode(intVal($result));
            }
        }
        return json_encode(0);
    }

    /**
     * Visualisation d'un détail
     */
    public function getCage()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_CAGE);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arrData = $crud->getDataById(array('id' => intval($id)));
        $rangee = new Rangee();
        $arr['arr_data_entrepot'] = $rangee->getAllEntrepot();
        $arr["action"] = $action;
        $arr["data"] = $arrData;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('cage/maj_view', $arr);
    }

    public function majCage()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr_data = $this->request->getVar('data');
        $crud = new CrudModel(TBL_CAGE);
        if (!empty($arr_data)) {
            unset($arr_data['allee_id_base']);
            unset($arr_data['rangee_id_base']);
            unset($arr_data['niveau_id_base']);
            $is_code_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr_data['code'])), "entrepot_id" => $arr_data['entrepot_id'], "allee_id" => $arr_data['allee_id'], "rangee_id" => $arr_data['rangee_id'], "niveau_id" => $arr_data['niveau_id'], "id != " . $arr_data['id'] => null, "flag_suppression" => 0));
            $is_data_exist = $crud->getNb($arr_data);
            if ($is_code_exist > 0) {
                return json_encode(2); // code doublon
            } else if ($is_data_exist > 0) {
                return json_encode(3); // aucune modification
            } else {
                $id = $arr_data['id'];
                unset($arr_data['id']);
                $result = $crud->maj(["id" => $id], $arr_data, 22);
                if ($result != 1) {
                    return json_encode($result);
                }
                $emplacement = new Emplacement();
                $result =  $emplacement->updatetEmplacement($id, 25); // maj emplacement
                return json_encode($result);
            }
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteCage()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_CAGE);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 23);
            return json_encode($result);
        }
        return json_encode(0);
    }
}
