<?php

namespace App\Controllers;

use App\Controllers\Acces;
use App\Models\CrudModel;
use App\Controllers\User;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Profil extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(2);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Paramètrage des profils";
        $arr['arr_profil_page'] = $this->getAllProfilPages();
        $arr['arr_page'] = $this->getAllPages();
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('profil/profil_view', $arr);
            return;
        }
        echo view('profil/profil_view', $arr);
    }

    public function getAllProfil()
    {
        $crud = new CrudModel(TBL_PROFIL);
        $arr = $crud->getAllData(array('flag_suppression' => 0), [], 'id, libelle as text, actif');
        return $arr;
    }

    public function getAllPages()
    {
        $crud = new CrudModel(TBL_PAGE);
        $arr = $crud->getAllData(array('actif' => 1), [], 'id, libelle');
        return $arr;
    }

    public function getAllProfilPages()
    {
        $sql = "SELECT 
                    profil.id,
                    profil.libelle, 
                    CASE 
                        WHEN count(DISTINCT page.id) > 3 THEN
                            string_agg(DISTINCT page.libelle, ', ' ORDER BY page.libelle) FILTER (WHERE page.id IN (
                                SELECT id FROM page WHERE id = ANY(acces.page_id) ORDER BY libelle LIMIT 3
                            )) || ', ...'
                        ELSE
                            string_agg(DISTINCT page.libelle, ', ' ORDER BY page.libelle)
                    END AS pages,
                    profil.actif
                FROM 
                    acces
                JOIN 
                    profil ON acces.profil_id = profil.id
                JOIN 
                    LATERAL unnest(acces.page_id) AS page_id ON true
                JOIN 
                    page ON page.id = page_id.page_id 
                WHERE  
                    profil.flag_suppression = 0
                GROUP BY 
                    profil.id,
                    profil.libelle,
                    profil.actif;
                ";
        $arr = $this->db->query($sql)->getResult();
        return $arr;
    }

    public function insertProfil()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(2);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $arr_page_id = $this->request->getVar('arr_page_id');
        sort($arr_page_id);
        $profil = $this->request->getVar('profil');
        if (!empty($arr_page_id) && $profil != "") {
            $crud = new CrudModel(TBL_PROFIL);
            $crud_acces = new CrudModel(TBL_ACCES);
            $is_exist = $crud->getNb(array("LOWER(libelle)" => strtolower($profil), "flag_suppression" => 0));
            if ($is_exist > 0) {
                return json_encode(2); // le libelle est doublon
            } else {
                $result_profil = $crud->create(["libelle" => $profil], 4);
                $result = 0;
                if ($result_profil  == 1) {
                    $arr_profil_id =  $crud->getAllData(["flag_suppression" => 0], [], "id", "id", "", "desc", 1, 1);
                    $list_page_id = '{' . implode(',', $arr_page_id) . '}';
                    $result =  $crud_acces->create(["profil_id" => $arr_profil_id[0]->id, "page_id" => $list_page_id], 3);
                }
                return json_encode($result);
            }
        }
        return json_encode(0);
    }

    /**
     * Visualisation d'un détail
     */
    public function getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(2);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $crud_profil = new CrudModel(TBL_PROFIL);
        $crud_acces = new CrudModel(TBL_ACCES);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arr_profil = $crud_profil->getDataById(array('id' => intval($id)));
        $arr_acces = $crud_acces->getDataById(array('profil_id' => intval($id)));
        $arr["errors"] = array();
        $arr["action"] = $action;
        $arr["profil"] = $arr_profil;
        $arr["arr_list_page"] = array_map('intval', explode(',', str_replace(['{', '}'], '',  $arr_acces->page_id)));
        $arr["arr_profil"] = $this->getAllProfil();
        $arr["arr_page"] = $this->getAllPages();
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('profil/maj_profil_view', $arr);
    }

    public function majProfil()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(2);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $arr_page_id = $this->request->getVar('arr_page_id');
        sort($arr_page_id);
        $profil = $this->request->getVar('profil');
        $id = $this->request->getVar('id');
        $actif = $this->request->getVar('actif');
        $crud = new CrudModel(TBL_PROFIL);
        $crud_acces = new CrudModel(TBL_ACCES);
        $arr_acces_base = $crud_acces->getDataById(array('profil_id' => $id));
        $arr_acces_base =  array_map('intval', explode(',', str_replace(['{', '}'], '',  $arr_acces_base->page_id)));
        sort($arr_acces_base);
        if (!empty($arr_page_id) &&  $profil != "" && $id != "") {
            $is_libelle_exist = $crud->getNb(array("id != " . $id => null, ("LOWER(libelle)") => strtolower($profil), "flag_suppression" => 0));
            $arr_profil_base = $crud->getDataById(array("id" => $id, "flag_suppression" => 0));
            if ($is_libelle_exist > 0) {
                return json_encode(2); // login doublon
            } else if (($arr_page_id == $arr_acces_base) && (strtolower($profil) == strtolower($arr_profil_base->libelle)) && (intval($actif) == intval($arr_profil_base->actif))) {
                return json_encode(3); // aucune modification
            } else {
                $result = 0;
                if ((strtolower($profil) != strtolower($arr_profil_base->libelle)) || (intval($actif) != intval($arr_profil_base->actif))) {
                    $result =  $crud->maj(["id" => $id], ["libelle" => $profil, "actif" => $actif], 4);
                }
                if ($arr_page_id != $arr_acces_base) {
                    $list_page = '{' . implode(',', $arr_page_id) . '}';
                    $result = $crud_acces->maj(["profil_id" => $id], ["page_id" => $list_page], 4);
                }
                return json_encode($result);
            }
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteProfil()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(2);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_PROFIL);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 5);
            return json_encode($result);
        }
        return json_encode(0);
    }
}
