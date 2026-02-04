<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;

class Palette extends BaseController
{
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
        $arr['arr_data_palette'] = $this->getAllPalette();
        $arr['arr_palette_statut'] = $this->getAllStatut();
        $arr['arr_client'] = $this->getAllClient();
        $arr['titre'] = "Gestion des palettes";
        $arr['menu_palette'] = "liste palette";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('palette/list_view', $arr);
            return;
        }
        echo view('palette/list_view', $arr);
    }

    public function getAllPalette()
    {
        $crud = new CrudModel(TBL_PALETTE);
        $arrJoin = [[
            'table' => TBL_PALETTE_STATUT,
            'type'  => 'LEFT',
            'on'    => TBL_PALETTE_STATUT . '.id = ' . TBL_PALETTE . '.palette_statut_id'
        ]];
        $select = TBL_PALETTE . '.id,' . TBL_PALETTE . '.code,' . TBL_PALETTE . '.palette_statut_id,' . TBL_PALETTE_STATUT . '.statut,' . TBL_PALETTE . '.client';
        return  $crud->getAllData(['flag_suppression' => 0], $arrJoin, $select);
    }

    public function getAllStatut()
    {
        $crud = new CrudModel(TBL_PALETTE_STATUT);
        return  $crud->getAllData();
    }

    public function getAllClient() // venant de X3
    {
        return [
            ['id' => 1,  'nom' => 'Entreprise Alpha'],
            ['id' => 2,  'nom' => 'Société Beta'],
            ['id' => 3,  'nom' => 'Groupe Gamma'],
            ['id' => 4,  'nom' => 'Client Delta'],
            ['id' => 5,  'nom' => 'Compagnie Epsilon'],
            ['id' => 6,  'nom' => 'Holding Zeta'],
            ['id' => 7,  'nom' => 'Entreprise Eta'],
            ['id' => 8,  'nom' => 'Société Theta'],
            ['id' => 9,  'nom' => 'Groupe Iota'],
            ['id' => 10, 'nom' => 'Client Kappa'],
        ];
    }


    public function insertPalette()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr = $this->request->getVar('data');
        if (!empty($arr)) {
            $crud = new CrudModel(TBL_PALETTE);
            $is_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr['code'])), "flag_suppression" => 0));
            if ($is_exist > 0) {
                return json_encode(2); // code doublon
            } else {
                $result = $crud->create($arr, 27);
                return json_encode(intVal($result));
            }
        }
        return json_encode(0);
    }

    /**
     * Visualisation d'un détail
     */
    public function getPalette()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_PALETTE);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arrData = $crud->getDataById(array('id' => intval($id)));
        $arr['arr_palette_statut'] = $this->getAllStatut();
        $arr['arr_client'] = $this->getAllClient();
        $arr["action"] = $action;
        $arr["data"] = $arrData;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('palette/maj_view', $arr);
    }

    public function majPalette()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr_data = $this->request->getVar('data');
        $crud = new CrudModel(TBL_PALETTE);
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
                $result = $crud->maj(["id" => $id], $arr_data, 28);
                return json_encode($result);
            }
        }
    }

    public function deletePalette()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_PALETTE);
            $canDelete = $this->checkPalette($id);
            if ($canDelete == true) {
                $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 29);
                return json_encode($result);
            } else {
                return json_encode(2); // non supprimable car le statut est occupé
            }
        }
        return json_encode(0);
    }

    public function checkPalette($id = null)
    {
        if ($id != null) {
            $crud = new CrudModel(TBL_PALETTE);
            $arr = $crud->getDataById(['id' => $id]);
            if ($arr->palette_statut_id == 3) { // occupé
                return false;
            }
            return true;
        }
        return true;
    }
}
