<?php

namespace App\Controllers;

class Home extends BaseController
{

    public function index()
    {

        // $arr['titre'] = "ma page";
        // $arr['request_ajax'] = 0;
        // if ($this->request->isAJAX()) {
        //     $arr['titre'] = "ma page ajax";
        //     $arr['request_ajax'] = 1;
        //     echo view('test', $arr);
        //     return;
        // }
        // echo view('test', $arr);

        echo view('login_view');
    }


    public function login()
    {
        $data = $this->request->getJSON();

        $login = $data->login;
        $password = $data->password;

        // TODO: Vérification LDAP
        // if ($ldap_ok) ...

        return $this->response->setJSON([
            'success' => true,
            'redirect' => base_url('dashboard')
        ]);
    }
}
