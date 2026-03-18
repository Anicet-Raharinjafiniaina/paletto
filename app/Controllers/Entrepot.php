<?php

namespace App\Controllers;

use App\Controllers\Acces;
use App\Controllers\Emplacement;
use App\Models\CrudModel;

class Entrepot extends BaseController
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
        $crud = new CrudModel(TBL_ENTREPOT);
        $arr['arr_data_entrepot'] = $crud->getAllData(array('flag_suppression' => 0), [], "*");
        $arr['titre'] = "Gestion des entrepôts";
        $arr['menu_emplacement'] = 'Entrepot';
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('entrepot/list_view', $arr);
            return;
        }
        echo view('entrepot/list_view', $arr);
    }

    public function insertEntrepot()
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
            $crud = new CrudModel(TBL_ENTREPOT);
            $is_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr['code'])), "flag_suppression" => 0));
            if ($is_exist > 0) {
                return json_encode(2); // code doublon
            } else {
                $result = $crud->create($arr, 9);
                return json_encode(intVal($result));
            }
        }
        return json_encode(0);
    }

    /**
     * Visualisation d'un détail
     */
    public function getEntrepot()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
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
        echo view('entrepot/maj_view', $arr);
    }

    public function majEntrepot()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(3);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $arr_data = $this->request->getVar('data');
        $crud = new CrudModel(TBL_ENTREPOT);
        $emplacementController = new Emplacement();
        if (!empty($arr_data)) {
            $is_code_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr_data['code'])), "id != " . $arr_data['id'] => null, "flag_suppression" => 0));
            $is_data_exist = $crud->getNb($arr_data);

            $arrFilter = ['entrepot_id' => $arr_data['id'], 'statut_id' => 2];
            $nb = $emplacementController->compterListeEmplacement($arrFilter);

            if ($is_code_exist > 0) {
                return json_encode(2); // code doublon
            } else if ($nb > 0) {
                return json_encode(3); // Impossible de faire la modification car l’emplacement associé à cet entrepôt est occupé.
            } else if ($is_data_exist > 0) {
                return json_encode(4); // aucune modification
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
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_ENTREPOT);
            $emplacementController = new Emplacement();
            $arrFilter = ['entrepot_id' => $id, 'statut_id' => 2];
            $nb = $emplacementController->compterListeEmplacement($arrFilter);
            if ($nb > 0) {
                return json_encode(2); // Impossible de faire la modification car l’emplacement associé à cet entrepôt est occupé.
            }
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 11);
            return json_encode($result);
        }
        return json_encode(0);
    }
}
