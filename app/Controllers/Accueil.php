<?php

namespace App\Controllers;


class Accueil extends BaseController
{
    public function index()
    {
        $session = session();
        $utilisateur = $session->get('utilisateur');
        $valide_session = $utilisateur && isset($utilisateur['login']) && isset($utilisateur['profil_id']);
        if (!$valide_session) { // Session invalide
            return redirect()->to('/');
        }
        $arr['titre'] = "";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('acceuil', $arr);
            return;
        }
        return view('acceuil', $arr);
    }
}
