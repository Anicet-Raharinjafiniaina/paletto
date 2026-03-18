<?php

namespace App\Controllers;

use App\Controllers\Acces;
use App\Libraries\LibDataTable;
use App\Models\CrudModel;

class HistoriqueMouvement extends BaseController
{

    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(9);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Historique des mouvements";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('historique/historique_mouvement_view', $arr);
            return;
        }
        echo view('historique/historique_mouvement_view', $arr);
    }

    public function historiqueMouvement()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(9);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }

        $select = [TBL_MOUVEMENT . '.id', TBL_EMPLACEMENT_ADRESSE . '.qr_code_texte as emplacement', TBL_ARTICLE . '.qr_code_text as palette_article', TBL_MOUVEMENT_TYPE . '.type', TBL_MOUVEMENT . '.date_mouvement'];
        $searchable = [TBL_EMPLACEMENT_ADRESSE . '.qr_code_texte', TBL_ARTICLE . '.qr_code_text', TBL_MOUVEMENT_TYPE . '.type'];

        $arrJoin = [
            [
                'table' => TBL_EMPLACEMENT_ADRESSE,
                'condition' => TBL_EMPLACEMENT_ADRESSE . '.id = ' . TBL_MOUVEMENT . '.emplacement_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_ARTICLE,
                'condition' => TBL_ARTICLE . '.id = ' . TBL_MOUVEMENT . '.article_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_PALETTE,
                'condition' => TBL_PALETTE . '.id = ' . TBL_MOUVEMENT . '.palette_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_MOUVEMENT_TYPE,
                'condition' => TBL_MOUVEMENT_TYPE . '.id = ' . TBL_MOUVEMENT . '.mouvement_type_id',
                'type' => 'left'
            ]
        ];
        /* $where = [
            TBL_EMPLACEMENT_ADRESSE . '.flag_suppression' => 1
        ];*/
        $libDataTable = new LibDataTable();
        $orderBy = ['column' => TBL_MOUVEMENT . '.date_mouvement', 'dir' => 'desc'];
        return $libDataTable->index(TBL_MOUVEMENT, $select, $searchable, ["voir"], $arrJoin, [], $orderBy);
    }

    public function getDetailMouvement()
    {
        $id = $this->request->getPost('id');
        $crud = new CrudModel(TBL_MOUVEMENT);
        $arrJoin = [
            [
                'table' => TBL_EMPLACEMENT_ADRESSE,
                'on' => TBL_EMPLACEMENT_ADRESSE . '.id = ' . TBL_MOUVEMENT . '.emplacement_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_ARTICLE,
                'on' => TBL_ARTICLE . '.id = ' . TBL_MOUVEMENT . '.article_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_PALETTE,
                'on' => TBL_PALETTE . '.id = ' . TBL_MOUVEMENT . '.palette_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_MOUVEMENT_TYPE,
                'on' => TBL_MOUVEMENT_TYPE . '.id = ' . TBL_MOUVEMENT . '.mouvement_type_id',
                'type' => 'left'
            ],
            [
                'table' => TBL_UTILISATEUR,
                'on' => TBL_UTILISATEUR . '.id = ' . TBL_MOUVEMENT . '.cree_par',
                'type' => 'left'
            ]
        ];
        $select = [TBL_MOUVEMENT . '.id', TBL_EMPLACEMENT_ADRESSE . '.qr_code_texte as emplacement', TBL_PALETTE . '.code as palette_code', TBL_ARTICLE . '.client_code', TBL_ARTICLE . '.client_nom', TBL_ARTICLE . '.code as article_code', TBL_ARTICLE . '.nom as article_nom', TBL_ARTICLE . '.dluo', TBL_ARTICLE . '.unite_pcb', TBL_ARTICLE . '.quantite', TBL_ARTICLE . '.lot',  TBL_ARTICLE . '.palettisation', TBL_ARTICLE . '.unite_stockage', TBL_MOUVEMENT_TYPE . '.type as mouvement_type', TBL_MOUVEMENT . '.date_mouvement', TBL_UTILISATEUR . '.nom as auteur'];
        $arr['data'] = $crud->getDataById([TBL_MOUVEMENT . '.id' => $id], $arrJoin, $select);
        echo view('historique/detail_mouvement', $arr);
    }
}
