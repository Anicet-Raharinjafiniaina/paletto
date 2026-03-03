<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Rangee;


class Niveau extends BaseController
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
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_NIVEAU);
        $arrJoin = [
            [
                'table' => TBL_ENTREPOT,
                'type'  => 'LEFT',
                'on'    => TBL_ENTREPOT . '.id = ' . TBL_NIVEAU . '.entrepot_id'
            ],
            [
                'table' => TBL_ALLEE,
                'type'  => 'LEFT',
                'on'    => TBL_ALLEE . '.id = ' . TBL_NIVEAU . '.allee_id'
            ],
            [
                'table' => TBL_RANGEE,
                'type'  => 'LEFT',
                'on'    => TBL_RANGEE . '.id = ' . TBL_NIVEAU . '.rangee_id'
            ],

        ];
        $select = TBL_NIVEAU . ".id, " . TBL_NIVEAU . ".code, "  . TBL_RANGEE . ".code AS rangee," . TBL_ALLEE . ".code AS allee," . "CONCAT(" . TBL_ENTREPOT . ".code, ' - ', " . TBL_ENTREPOT . ".nom) AS entrepot";
        $arr['arr_data_niveau'] = $crud->getAllData(array(TBL_NIVEAU . '.flag_suppression' => 0, TBL_ENTREPOT . '.flag_suppression' => 0, TBL_ALLEE . '.flag_suppression' => 0, TBL_RANGEE . '.flag_suppression' => 0), $arrJoin, $select);
        $arr['titre'] = "Gestion des niveaux";
        $rangee = new Rangee();
        $arr['arr_data_entrepot'] = $rangee->getAllEntrepot();
        // $arr['arr_data_allee'] = $rangee->getAllAllee();
        $arr['menu_emplacement'] = 'Niveau';
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('niveau/list_view', $arr);
            return;
        }
        echo view('niveau/list_view', $arr);
    }

    public function getAllrangee()
    {
        $crud = new CrudModel(TBL_RANGEE);
        $arr_data_rangee = $crud->getAllData(array('flag_suppression' => 0), [], "id,code,allee_id,entrepot_id");
        return $arr_data_rangee;
    }

    public function getAllRangeeByAllee()
    {
        $entrepot_id = $this->request->getVar('entrepot_id');
        $allee_id = $this->request->getVar('allee_id');
        if (($allee_id == "" || $allee_id == null) || ($entrepot_id == "" || $entrepot_id == null)) {
            return json_encode([]);
        }
        $crud = new CrudModel(TBL_RANGEE);
        $arr_data_rangee = $crud->getAllData(array('flag_suppression' => 0, 'entrepot_id' => $entrepot_id, 'allee_id' => $allee_id), [], "id,code AS text");
        return json_encode($arr_data_rangee);
    }

    public function insertNiveau()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr = $this->request->getVar('data');
        if (!empty($arr)) {
            $crud = new CrudModel(TBL_NIVEAU);
            $is_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr['code'])), "entrepot_id" => $arr['entrepot_id'], "allee_id" => $arr['allee_id'],  "rangee_id" => $arr['rangee_id'], "flag_suppression" => 0));
            if ($is_exist > 0) {
                return json_encode(2); // code doublon
            } else {
                $result = $crud->create($arr, 18);
                return json_encode(intVal($result));
            }
        }
        return json_encode(0);
    }

    /**
     * Visualisation d'un détail
     */
    public function getNiveau()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_NIVEAU);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arrData = $crud->getDataById(array('id' => intval($id)));
        $rangee = new Rangee();
        $arr['arr_data_entrepot'] = $rangee->getAllEntrepot();
        $arr['arr_data_allee'] = $rangee->getAllAllee();
        $arr['arr_data_rangee'] = $this->getAllrangee();
        $arr["action"] = $action;
        $arr["data"] = $arrData;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('niveau/maj_view', $arr);
    }

    public function majNiveau()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr_data = $this->request->getVar('data');
        $crud = new CrudModel(TBL_NIVEAU);
        if (!empty($arr_data)) {
            unset($arr_data['allee_id_base']);
            unset($arr_data['rangee_id_base']);
            $is_code_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr_data['code'])), "entrepot_id" => $arr_data['entrepot_id'], "allee_id" => $arr_data['allee_id'], "rangee_id" => $arr_data['rangee_id'], "id != " . $arr_data['id'] => null, "flag_suppression" => 0));
            $is_data_exist = $crud->getNb($arr_data);

            $emplacementController = new Emplacement();
            $arrFilter = ['niveau_id' => $arr_data['id'], 'statut_id' => 2];
            $nb = $emplacementController->compterListeEmplacement($arrFilter);
            if ($nb > 0) {
                return json_encode(4); // Impossible de faire la modification car l’emplacement associé à ce niveau est occupé.
            }

            if ($is_code_exist > 0) {
                return json_encode(2); // code doublon
            } else if ($is_data_exist > 0) {
                return json_encode(3); // aucune modification
            } else {
                $id = $arr_data['id'];
                unset($arr_data['id']);
                $result = $crud->maj(["id" => $id], $arr_data, 19);
                return json_encode($result);
            }
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteNiveau()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_NIVEAU);
            $emplacementController = new Emplacement();
            $arrFilter = ['niveau_id' => $id, 'statut_id' => 2];
            $nb = $emplacementController->compterListeEmplacement($arrFilter);
            if ($nb > 0) {
                return json_encode(2); // Impossible de faire la suppression car l’emplacement associé à cette rangée est occupé.
            } else {
                $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 20);
                return json_encode($result);
            }
        }
        return json_encode(0);
    }
}
