<?php

namespace App\Controllers;

use App\Controllers\Acces;
use App\Controllers\ListeEmplacement;
use App\Controllers\Rangee;
use App\Models\CrudModel;

class Emplacement extends BaseController
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

    public function load()
    {
        $crud = new CrudModel(TBL_EMPLACEMENT);
        $arrJoin = [
            [
                'table' => TBL_ENTREPOT,
                'type'  => 'LEFT',
                'on'    => TBL_ENTREPOT . '.id = ' . TBL_EMPLACEMENT . '.entrepot_id'
            ],
            [
                'table' => TBL_ALLEE,
                'type'  => 'LEFT',
                'on'    => TBL_ALLEE . '.id = ' . TBL_EMPLACEMENT . '.allee_id'
            ],
            [
                'table' => TBL_RANGEE,
                'type'  => 'LEFT',
                'on'    => TBL_RANGEE . '.id = ' . TBL_EMPLACEMENT . '.rangee_id'
            ],
            [
                'table' => TBL_NIVEAU,
                'type'  => 'LEFT',
                'on'    => TBL_NIVEAU . '.id = ' . TBL_EMPLACEMENT . '.niveau_id'
            ],
            [
                'table' => TBL_CAGE,
                'type'  => 'LEFT',
                'on'    => TBL_CAGE . '.id = ' . TBL_EMPLACEMENT . '.cage_id'
            ],
        ];
        $select = TBL_EMPLACEMENT . ".id, " . TBL_EMPLACEMENT . ".code, " . TBL_CAGE . ".code AS cage," . TBL_NIVEAU . ".code AS niveau," . TBL_RANGEE . ".code AS rangee," . TBL_ALLEE . ".code AS allee," . "CONCAT(" . TBL_ENTREPOT . ".code, ' - ', " . TBL_ENTREPOT . ".nom) AS entrepot";
        $arr['arr_data_emplacement'] = $crud->getAllData(array(TBL_EMPLACEMENT . '.flag_suppression' => 0, TBL_ENTREPOT . '.flag_suppression' => 0, TBL_ALLEE . '.flag_suppression' => 0, TBL_RANGEE . '.flag_suppression' => 0, TBL_NIVEAU . '.flag_suppression' => 0, TBL_CAGE . '.flag_suppression' => 0), $arrJoin, $select);
        $arr['titre'] = "Gestion des emplacements";
        $arr['menu_emplacement'] = 'Emplacement';
        $rangee = new Rangee();
        $arr['arr_data_entrepot'] = $rangee->getAllEntrepot();
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('emplacement/list_view', $arr);
            return;
        }
        echo view('emplacement/list_view', $arr);
    }

    public function getAllCageByNiveau()
    {
        $entrepot_id = $this->request->getVar('entrepot_id');
        $allee_id = $this->request->getVar('allee_id');
        $rangee_id = $this->request->getVar('rangee_id');
        $niveau_id = $this->request->getVar('niveau_id');
        if (($allee_id == "" || $allee_id == null) || ($entrepot_id == "" || $entrepot_id == null) || ($rangee_id == "" || $rangee_id == null) || ($niveau_id == "" || $niveau_id == null)) {
            return json_encode([]);
        }
        $crud = new CrudModel(TBL_CAGE);
        $arr_data_cage = $crud->getAllData(array('flag_suppression' => 0, 'entrepot_id' => $entrepot_id, 'allee_id' => $allee_id, 'rangee_id' => $rangee_id, 'niveau_id' => $niveau_id), [], "id,code AS text");
        return json_encode($arr_data_cage);
    }

    public function insertEmplacement()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $arr = $this->request->getVar('data');
        if (!empty($arr)) {
            $crud = new CrudModel(TBL_EMPLACEMENT);
            $is_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr['code'])), "entrepot_id" => $arr['entrepot_id'], "allee_id" => $arr['allee_id'], "rangee_id" => $arr['rangee_id'], "niveau_id" => $arr['niveau_id'], "cage_id" => $arr['cage_id'], "flag_suppression" => 0));
            if ($is_exist > 0) {
                return json_encode(2); // code doublon
            } else {
                $id = $crud->createReturnId($arr, 24); // insertion emplacement
                $listeEmplacement = new ListeEmplacement();
                $result = $listeEmplacement->updatetEmplacement($id, 0); // maj liste emplacement
                return json_encode(intVal($result));
            }
        }
        return json_encode(0);
    }

    /**
     * Visualisation d'un détail
     */
    public function getEmplacement()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $crud = new CrudModel(TBL_EMPLACEMENT);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arrData = $crud->getDataById(array('id' => intval($id)));
        $rangee = new Rangee();
        $arr['arr_data_entrepot'] = $rangee->getAllEntrepot();
        $arr["action"] = $action;
        $arr["data"] = $arrData;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('emplacement/maj_view', $arr);
    }

    public function majEmplacement()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $arr_data = $this->request->getVar('data');
        $crud = new CrudModel(TBL_EMPLACEMENT);
        if (!empty($arr_data)) {
            unset($arr_data['allee_id_base']);
            unset($arr_data['rangee_id_base']);
            unset($arr_data['niveau_id_base']);
            unset($arr_data['cage_id_base']);
            $is_code_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr_data['code'])), "entrepot_id" => $arr_data['entrepot_id'], "allee_id" => $arr_data['allee_id'], "rangee_id" => $arr_data['rangee_id'], "niveau_id" => $arr_data['niveau_id'], "cage_id" => $arr_data['cage_id'], "id != " . $arr_data['id'] => null, "flag_suppression" => 0));
            $is_data_exist = $crud->getNb($arr_data);

            $arrFilter = ['emplacement_id' => $arr_data['id'], 'statut_id' => 2];
            $nb = $this->compterListeEmplacement($arrFilter);

            if ($is_code_exist > 0) {
                return json_encode(2); // données doublon
            } else if ($is_data_exist > 0) {
                return json_encode(3); // aucune modification
            } else if ($nb > 0) {
                return json_encode(4); // Impossible de faire la modification car l'emplacement est occupé
            } else {
                $id = $arr_data['id'];
                unset($arr_data['id']);
                $result = $crud->maj(["id" => $id], $arr_data, 25);
                if ($result != 1) {
                    return json_encode($result);
                }
                $listeEmplacement = new ListeEmplacement();
                $result =  $listeEmplacement->updatetEmplacement($id, 0); // maj emplacement
                return json_encode($result);
            }
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteEmplacement()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $id = $this->request->getVar('id');
        $result = 0;
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_EMPLACEMENT);
            $crudDetailEmplacement = new CrudModel(TBL_EMPLACEMENT_ADRESSE);
            $arrFilter = ['emplacement_id' => $id, 'statut_id' => 1];
            $nb = $this->compterListeEmplacement($arrFilter);
            if ($nb > 0) { // emplacement libre
                $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 26);
                $crudDetailEmplacement->del(["emplacement_id" => $id, "flag_suppression" => 0], ["flag_suppression" => 1], 0);
            } else {
                return json_encode(2); //Impossible de faire la suppression car l'emplacement est occupé
            }
            return json_encode($result);
        }
        return json_encode($result);
    }


    function compterListeEmplacement($arrFilter)
    {
        $crud = new CrudModel(VIEW_EMPLACEMENT_ADRESSE);
        return $crud->getNb($arrFilter);
    }
}
