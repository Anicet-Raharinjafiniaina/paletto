<?php

namespace App\Controllers;

use App\Controllers\Acces;
use App\Libraries\LibDataTable;

class Historique extends BaseController
{

    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(8);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Historique des interactions";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('historique/historique_action_view', $arr);
            return;
        }
        echo view('historique/historique_action_view', $arr);
    }

    public function historiqueAction()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(8);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }

        $arrJoin = [
            array(
                'table' => TBL_UTILISATEUR,
                'condition' => TBL_UTILISATEUR . '.id = ' . TBL_HISTORIQUE . '.utilisateur_id',
                'type' => 'left'
            ),
            array(
                'table' => TBL_ACTION,
                'condition' => TBL_ACTION . '.id = ' . TBL_HISTORIQUE . '.action_id',
                'type' => 'left'
            )
        ];
        $select = [
            TBL_HISTORIQUE . '.id',
            TBL_ACTION . '.libelle',
            TBL_UTILISATEUR . '.login',
            TBL_UTILISATEUR . '.nom',
            "to_char(" . TBL_HISTORIQUE . ".date_creation,'DD/MM/YYYY HH24:MI:SS') as date_creation"
        ];

        $searchable = [
            TBL_ACTION . '.libelle',
            TBL_UTILISATEUR . '.login',
            TBL_UTILISATEUR . '.nom',
            "to_char(" . TBL_HISTORIQUE . ".date_creation,'DD/MM/YYYY HH24:MI:SS')"
        ];
        $libDataTable = new LibDataTable();
        $orderBy = ['column' => TBL_HISTORIQUE . '.id', 'dir' => 'desc'];
        return $libDataTable->index(TBL_HISTORIQUE, $select, $searchable, [], $arrJoin, [], $orderBy);
    }
}
