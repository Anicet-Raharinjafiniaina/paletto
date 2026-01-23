<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Libraries\LibLdap;


class User extends BaseController
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
        // $acces  = new Acces();
        // $is_ok = $acces->is_ok(1);
        // if (!$is_ok) {
        //     return redirect()->to('/');
        // }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_UTILISATEUR);
        $arr_join = [
            array(
                'table' => 'profil',
                'on' => 'profil.id = ' . TBL_UTILISATEUR . '.profil_id',
                'type' => 'left'
            )
        ];
        $arr['arr_data_user'] = $crud->getAllData(array(TBL_UTILISATEUR . '.flag_suppression' => 0), $arr_join, TBL_UTILISATEUR . '.id,login,nom,mail,' . TBL_UTILISATEUR . '.actif,profil.libelle as profil');
        $arr['titre'] = "Paramètrage des utilisateurs";
        $arr['arr_profil'] = $this->getAllProfil();
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('user/user_view', $arr);
            return;
        }
        echo view('user/user_view', $arr);
    }

    public function getAllProfil()
    {
        $crud = new CrudModel(TBL_PROFIL);
        $arr = $crud->getAllData(array('flag_suppression' => 0, 'actif' => 1), [], 'id, libelle as text');
        return $arr;
    }

    /** Auto complète */
    public function getInfosUser()
    {
        // if (!$this->access->is_ok(24)) {
        //     return $this->access->get_redirect();
        // }

        $name = trim($this->request->getVar('c'));
        $filtre = '(|(cn=*' . $name . ')(cn=' . $name . '*)(cn=*' . $name . '*))';
        $login = isset($this->session->get('utilisateur')['login']) ? $this->session->get('utilisateur')['login'] : '';
        $mdp = isset($this->session->get('utilisateur')['mdp']) ? ($this->session->get('utilisateur')['mdp']) : '';
        $ad = new LibLdap($login, $mdp, null, $filtre);
        $res = $ad->getInfos();
        $arr = [];
        if (!$res) {
            $arr = ['Aucun résultat trouvé'];
        } else {
            foreach ($res as $key => $val) {
                if (is_numeric($key)) {
                    $arr[] = isset($res[$key]['name']) ? $res[$key]['name'][0] : "";
                }
            }
        }
        return json_encode($arr);
    }

    public function getMail()
    {
        // if (!$this->access->is_ok(24)) {
        //     return $this->access->get_redirect();
        // }
        $name = trim($this->request->getVar('n'));
        $filtre = '(cn=' . $name . ')';
        $login = isset($this->session->get('utilisateur')['login']) ? $this->session->get('utilisateur')['login'] : '';
        $mdp = isset($this->session->get('utilisateur')['mdp']) ? ($this->session->get('utilisateur')['mdp']) : '';
        $ad = new LibLdap($login, $mdp, null, $filtre);
        $res = $ad->getInfos();
        $msg = "";
        if (!$res) {
            $msg = 'not found';
        } else {
            $msg = isset($res[0]['mail']) ? $res[0]['mail'][0] : "";
        }
        return $msg;
    }

    public function getLogin()
    {
        // if (!$this->access->is_ok(24)) {
        //     return $this->access->get_redirect();
        // }
        $name = trim($this->request->getVar('n'));
        $filtre = '(cn=' . $name . ')';
        $login = isset($this->session->get('utilisateur')['login']) ? $this->session->get('utilisateur')['login'] : '';
        $mdp = isset($this->session->get('utilisateur')['mdp']) ? ($this->session->get('utilisateur')['mdp']) : '';
        $ad = new LibLdap($login, $mdp, null, $filtre);
        $res = $ad->getInfos();
        $msg = "";
        if (!$res) {
            $msg = 'not found';
        } else {
            $msg = isset($res[0]['samaccountname']) ? $res[0]['samaccountname'][0] : "";
        }
        return $msg;
    }

    public function getFonction()
    {
        // if (!$this->access->is_ok(24)) {
        //     return $this->access->get_redirect();
        // }
        $name = trim($this->request->getVar('n'));
        $filtre = '(cn=' . $name . ')';
        $login = isset($this->session->get('utilisateur')['login']) ? $this->session->get('utilisateur')['login'] : '';
        $mdp = isset($this->session->get('utilisateur')['mdp']) ? ($this->session->get('utilisateur')['mdp']) : '';
        $ad = new LibLdap($login, $mdp, null, $filtre);
        $res = $ad->getInfos();
        $msg = "";
        if (!$res) {
            $msg = 'not found';
        } else {
            $msg = isset($res[0]['title']) ? $res[0]['title'][0] : "";
        }
        return $msg;
    }

    public function insertUser()
    {
        // $acces  = new Acces();
        // $is_ok = $acces->is_ok(1);
        // if (!$is_ok) {
        //     return redirect()->to('/');
        // }
        $arr_user = $this->request->getVar('data');
        if (!empty($arr_user)) {
            $arr_user['profil_id'] =  $arr_user['profil'];
            unset($arr_user['profil']);
            $crud = new CrudModel(TBL_UTILISATEUR);
            $is_exist = $crud->getNb(array("login" => $arr_user['login'], "flag_suppression" => 0));
            if ($is_exist > 0) {
                return json_encode(2); // login doublon
            } else {
                $result = $crud->create($arr_user, 6);
                return json_encode(intVal($result));
            }
        }
        return json_encode(0);
    }

    /**
     * Visualisation d'un détail
     */
    public function getUser()
    {
        // $acces  = new Acces();
        // $is_ok = $acces->is_ok(1);
        // if (!$is_ok) {
        //     return redirect()->to('/');
        // }
        $crud = new CrudModel(TBL_UTILISATEUR);

        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arrData = $crud->getDataById(array('id' => intval($id)));
        $arr["errors"] = array();
        $arr["action"] = $action;
        $arr["data"] = $arrData;
        $arr["arr_profil"] = $this->getAllProfil();
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('user/maj_user_view', $arr);
    }


    public function majUser()
    {
        // $acces  = new Acces();
        // $is_ok = $acces->is_ok(1);
        // if (!$is_ok) {
        //     return redirect()->to('/');
        // }
        $arr_data = $this->request->getVar('data');
        $crud = new CrudModel(TBL_UTILISATEUR);
        if (!empty($arr_data)) {
            $is_login_exist = $crud->getNb(array("login" => $arr_data['login'], "id != " . $arr_data['id'] => null, "flag_suppression" => 0));
            $is_data_exist = $crud->getNb($arr_data);
            if ($is_login_exist > 0) {
                return json_encode(2); // login doublon
            } else if ($is_data_exist > 0) {
                return json_encode(3); // aucune modification
            } else {
                $id = $arr_data['id'];
                unset($arr_data['id']);
                $result = $crud->maj(["id" => $id], $arr_data, 7);
                return json_encode($result);
            }
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser()
    {
        // $acces  = new Acces();
        // $is_ok = $acces->is_ok(1);
        // if (!$is_ok) {
        //     return redirect()->to('/');
        // }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_UTILISATEUR);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 8);
            return json_encode($result);
        }
        return json_encode(0);
    }
}
