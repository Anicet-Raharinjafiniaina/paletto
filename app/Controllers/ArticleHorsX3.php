<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;

/**
 *  Gestion des articles dans l'application 
 * */
class ArticleHorsX3 extends BaseController
{
    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(6);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_ARTICLE_HORS_X3);
        $arr['arr_data_article_hors_x3'] = $crud->getAllData(array('flag_suppression' => 0), [], '*');
        $arr['titre'] = "Gestion des articles qui n'existent pas sur x3";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('article_hors_x3/list_view', $arr);
            return;
        }
        echo view('article_hors_x3/list_view', $arr);
    }

    public function insertArticle()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(6);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $arr = $this->request->getVar('data');
        $arr =  $this->traiterUnitePCB($arr);
        $arr =  $this->traiterPalettisation($arr);
        if (!empty($arr)) {
            $crud = new CrudModel(TBL_ARTICLE_HORS_X3);
            $is_code_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr['code'])), "flag_suppression" => 0));
            if ($is_code_exist > 0) {
                return json_encode(2); // le code existe déjà
            } else {
                $result = $crud->create($arr, 36);
                return json_encode(intVal($result));
            }
        }
        return json_encode(0);
    }

    /**
     * Transformer les différentes valeurs unité PCB en {x,x,x,x,..} 
     */
    public function traiterUnitePCB($arrData)
    {
        $unite_pcb = [];
        foreach ($arrData as $key => $value) { // Récupérer toutes les clés 'unite_pcb_x'
            if (preg_match('/^unite_pcb(_upd)?_\d+$/', $key)) {
                $unite_pcb[] = $value;
                unset($arrData[$key]);
            }
        }
        $arrData['unite_pcb'] = '{' . implode(',', $unite_pcb) . '}';         // Transformer le tableau en string "{1,2,3,4}"
        return $arrData;
    }

    /**
     * Transformer les différentes valeurs de palettisation en {x,x,x,x,..} 
     */
    public function traiterPalettisation($arrData)
    {
        $unite_pcb = [];
        foreach ($arrData as $key => $value) { // Récupérer toutes les clés 'palettisation'
            if (preg_match('/^palettisation(_upd)?_\d+$/', $key)) {
                $unite_pcb[] = $value;
                unset($arrData[$key]);
            }
        }
        $arrData['palettisation'] = '{' . implode(',', $unite_pcb) . '}';         // Transformer le tableau en string "{1,2,3,4}"
        return $arrData;
    }
    /**
     * Visualisation d'un détail
     */
    public function getArticle()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(6);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $crud = new CrudModel(TBL_ARTICLE_HORS_X3);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arrData = $crud->getDataById(array('id' => intval($id)));
        $arrPcbPal = $this->getPCBPalettisation($arrData);
        $arrData->arrPcbPal = $arrPcbPal;
        $arr["action"] = $action;
        $arr["data"] = $arrData;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('article_hors_x3/maj_view', $arr);
    }

    /**
     * Pour avoir la combinaison unité PCB-Palettisation
     */
    public function getPCBPalettisation($arr, $keyUnite = 'unite_pcb', $keyPal = 'palettisation')
    {
        if (!isset($arr->$keyUnite) || !isset($arr->$keyPal)) { // Vérifie si les propriétés existent
            return [];
        }
        $unites = explode(',', trim($arr->$keyUnite, '{}')); // Transforme les chaînes en tableaux
        $palettisations = explode(',', trim($arr->$keyPal, '{}')); // Convertit les valeurs en int        
        $palettisations = array_map('intval', $palettisations); // Combine en tableau associatif        
        return array_combine($unites, $palettisations);
    }

    public function majArticle()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(6);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $arr_data = $this->request->getVar('data');
        $arr_data =  $this->traiterUnitePCB($arr_data);
        $arr_data =  $this->traiterPalettisation($arr_data);
        $crud = new CrudModel(TBL_ARTICLE_HORS_X3);
        if (!empty($arr_data)) {
            $is_code_exist = $crud->getNb(array("LOWER(code)" => strtolower(trim($arr_data['code'])), "id != " . $arr_data['id'] => null, "flag_suppression" => 0));
            $is_data_exist = $crud->getNb($arr_data);

            $arrFilter = ["LOWER(code)" => strtolower(trim($arr_data['code'])), 'affectee_emplacement' => 1, 'flag_suppression' => 0];
            $nb = $this->nbEmplacementsArticle($arrFilter);
            if ($nb > 0) {
                return json_encode(4); // Impossible d’effectuer la modification, car un ou plusieurs emplacement(s) sont utilisés par l’article.
            }

            if ($is_code_exist > 0) {
                return json_encode(2); // code doublon
            } else if ($is_data_exist > 0) {
                return json_encode(3); // aucune modification
            } else {
                $id = $arr_data['id'];
                unset($arr_data['id']);
                $result = $crud->maj(["id" => $id], $arr_data, 37);
                return json_encode($result);
            }
        }
    }

    /**
     * Supprimer un article
     */
    public function deleteArticle()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(6);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_ARTICLE_HORS_X3);
            $nb = $this->nbEmplacementsArticle(["id" => $id]);
            if ($nb > 0) {
                return json_encode(2); // Impossible d’effectuer la suppression, car un ou plusieurs emplacement(s) sont utilisés par l’article.
            } else {
                $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 38);
                return json_encode($result);
            }
        }
        return json_encode(0);
    }


    /**
     * Pour avoir le nombre d'article 
     */
    function nbEmplacementsArticle($arrFiltre)
    {
        $crud = new CrudModel(TBL_ARTICLE);
        return $crud->getNb($arrFiltre);
    }
}
