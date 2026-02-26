<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Libraries\LibLdap;

class Login extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        date_default_timezone_set('Indian/Antananarivo'); // centralisation timezone
    }

    public function index()
    {
        return view('login_view');
    }

    public function loginAuth()
    {
        $login = trim($this->request->getVar('login'));
        $password = trim($this->request->getVar('mdp'));

        // Vérifier si l'utilisateur existe dans la base locale
        $crudModel = new CrudModel(TBL_UTILISATEUR);
        $arrUser = $crudModel->getDataByIdArray(['login' => $login, 'actif' => 1, 'flag_suppression' => 0]);

        if (empty($arrUser)) {
            return json_encode(0); //l'utilisateurn'est pas autorisé à accéder à cette application
        }

        $crudProfil = new CrudModel(TBL_PROFIL);
        $arrProfil = $crudProfil->getDataByIdArray(['id' => $arrUser['profil_id'], 'actif' => 1, 'flag_suppression' => 0]);

        // Authentification via LDAP
        $ldap = new LibLdap($login, $password, $login);
        $res = $ldap->getInfos();

        if (!$res) {
            return json_encode(1); // Identifiant ou mot de passe incorrect
        }

        $info = $res[0];

        // Mise en minuscule des clés pour éviter les problèmes de casse
        $infoLower = array_change_key_case($info, CASE_LOWER);

        // Enregistrement dans la session
        $this->session->set('utilisateur', [
            'login'        => $login,
            'nom'          => $infoLower['sn'][0] ?? '',
            'prenom'       => $infoLower['givenname'][0] ?? '',
            'email'        => $infoLower['mail'][0] ?? '',
            'matricule'    => $infoLower['samaccountname'][0] ?? '',
            'poste'        => $infoLower['title'][0] ?? '',
            'direction'    => $infoLower['department'][0] ?? '',
            'bureau'       => $infoLower['physicaldeliveryofficename'][0] ?? '',
            'thumbnailphoto'    => $infoLower['thumbnailphoto'][0] ?? '',
            'user_id'      => $arrUser['id'] ?? '',
            'profil_id'    => $arrUser['profil_id'] ?? '',
            'profil'       => $arrProfil['libelle'] ?? '',
            'mdp'    => $password
        ]);

        // Historique de connexion
        $crudModelHisto = new CrudModel(TBL_HISTORIQUE);
        $crudModelHisto->logInOut(1, "Se connecter"); // 1 = action connexion
        $db = \Config\Database::connect();
        $builder = $db->table(TBL_UTILISATEUR);
        $builder->where('login', $login)->where('flag_suppression', 0)->where('login', $login)->update(['derniere_connexion' => date('Y-m-d H:i:s')]);

        return json_encode(2); //succès
    }

    public function logout()
    {
        $utilisateur = $this->session->get('utilisateur');
        if ($utilisateur) {
            $crudModelHisto = new CrudModel(TBL_HISTORIQUE);
            $crudModelHisto->logInOut(2, "Se déconnecter"); // 2 = action déconnexion
        }
        $this->session->destroy();
        return redirect()->to('/');
    }
}
