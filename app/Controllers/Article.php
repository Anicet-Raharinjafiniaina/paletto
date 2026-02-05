<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Palette;

class Article extends BaseController
{
    protected $dbX3;

    public function __construct()
    {
        $this->dbX3 = db_connect('connex_v12');
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
        $arr['arr_palette'] = $this->getAllPaletteNoTOccuped();
        $palette = new Palette();
        $arr['arr_client'] = $palette->getAllClient();
        $arr['titre'] = "Gestion des palettes";
        $arr['menu_palette'] = "article";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('article/list_view', $arr);
            return;
        }
        echo view('article/list_view', $arr);
    }

    public function getAllPaletteNoTOccuped()
    {
        $crud = new CrudModel(TBL_PALETTE);
        return  $crud->getAllData(['palette_statut_id != 3' => null, 'flag_suppression' => 0], [], "id, code");
    }

    public function getAllArticle() // venant de X3
    {
        $crud = new CrudModel('BASANEXP.ITMMASTER', 'x3');
        return   $crud->getAllData([], [], "ITMREF_0");
    }

    public function getArticleTypeahead()
    {
        $search = trim($this->request->getPost('code') ?? '');

        $sql = "
        SELECT TOP 10
            ITMREF_0 AS id,
            ITMDES1_0 AS libelle
        FROM BASANEXP.ITMMASTER
        WHERE ITMREF_0 LIKE ? COLLATE SQL_Latin1_General_CP1_CI_AS
        ORDER BY ITMREF_0
    ";

        $res = $this->dbX3->query($sql, [$search . '%'])->getResult();
        $arr = [];
        foreach ($res as $k => $v) :
            array_push($arr, $v->id);
        endforeach;
        return json_encode($arr);
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
    // public function getPalette()
    // {
    //     $acces  = new Acces();
    //     $is_ok = $acces->is_ok(4);
    //     if (!$is_ok) {
    //         return redirect()->to('/');
    //     }
    //     $crud = new CrudModel(TBL_PALETTE);
    //     $id = trim($this->request->getVar('id'));
    //     $action = trim($this->request->getVar('action'));
    //     $arrData = $crud->getDataById(array('id' => intval($id)));
    //     $arr['arr_palette_statut'] = $this->getAllStatut();
    //     $arr['arr_client'] = $this->getAllClient();
    //     $arr["action"] = $action;
    //     $arr["data"] = $arrData;
    //     $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
    //     $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
    //     echo view('palette/maj_view', $arr);
    // }

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

    // public function deletePalette()
    // {
    //     $acces  = new Acces();
    //     $is_ok = $acces->is_ok(4);
    //     if (!$is_ok) {
    //         return redirect()->to('/');
    //     }
    //     $id = $this->request->getVar('id');
    //     if ($id != "" && $id != null) {
    //         $crud = new CrudModel(TBL_PALETTE);
    //         $canDelete = $this->checkPalette($id);
    //         if ($canDelete == true) {
    //             $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 29);
    //             return json_encode($result);
    //         } else {
    //             return json_encode(2); // non supprimable car le statut est occupé
    //         }
    //     }
    //     return json_encode(0);
    // }
}
