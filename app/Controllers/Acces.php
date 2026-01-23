<?php

namespace App\Controllers;

use App\Models\CrudModel;

class Acces extends BaseController
{
    public function is_ok($id_page)
    {
        $session = session();
        $valide_session = $session->has('login') && $session->has('profil_id');
        $access_page = $this->acces_page($id_page);
        if (!$valide_session) { // Session invalide
            echo 'Session invalide';
            return false;
        } else if ($access_page == false) { // pas accès à la page
            echo 'Vous n\'avez pas le droit d\'accès.';
            return false;
        }
        return true;
    }

    public function acces_page($id_page)
    {
        $crud  = new CrudModel(TBL_ACCES);
        $arr = $crud->getDataById(array("profil_id" => session()->get('profil_id')));
        if (!empty($arr)) {
            $arr_page = array_map('intval', explode(',', trim($arr->page_id, '{}')));
            if (in_array(intval($id_page), $arr_page)) {
                return true;
            }
        }
        return false;
    }
}
