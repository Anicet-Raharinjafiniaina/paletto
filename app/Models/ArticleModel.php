<?php

namespace App\Models;

use CodeIgniter\Model;


class ArticleModel extends Model
{
    protected $dbX3;
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
        $this->dbX3 = db_connect('connex_v12');
    }

    public function getAllArticle()
    {
        $crud = new CrudModel(TBL_ARTICLE);
        $arrJoin = [
            [
                'table' => TBL_PALETTE,
                'type'  => 'LEFT',
                'on'    => TBL_PALETTE . '.id = ' . TBL_ARTICLE . '.palette_id'
            ],
        ];
        $select = TBL_ARTICLE . '.id,' . TBL_ARTICLE . '.code,' . TBL_ARTICLE . '.nom,' . TBL_ARTICLE . '.client_nom,' . TBL_ARTICLE . '.qr_code_text';
        return  $crud->getAllData([TBL_ARTICLE . '.flag_suppression' => 0, TBL_PALETTE . '.palette_statut_id' => 3, TBL_PALETTE . '.flag_suppression' => 0, /*TBL_ARTICLE . '.affectee_emplacement' => 0,*/ TBL_ARTICLE . '.mouvement_type_id' => null], $arrJoin, $select);
    }

    public function getArticleTypeaheadModel($search)
    {
        $sql = "SELECT TOP 10
                    ITMREF_0 AS id
                FROM BASANEXP.ITMMASTER
                WHERE ITMREF_0 LIKE ? COLLATE SQL_Latin1_General_CP1_CI_AS
                ORDER BY ITMREF_0";
        $res = $this->dbX3->query($sql, ['%' . $search . '%'])->getResult();
        $arr = [];
        foreach ($res as $k => $v) :
            array_push($arr, $v->id);
        endforeach;
        return $arr;
    }

    public function getArticleHorsX3TypeaheadModel($search)
    {
        $sql = "SELECT 
                    code AS id
                FROM article_hors_x3
                WHERE code ILIKE ?
                AND flag_suppression = 0
                ORDER BY code
                LIMIT 10";
        $res = $this->db->query($sql, ['%' . $search . '%'])->getResult();
        $arr = [];
        foreach ($res as $k => $v) :
            array_push($arr, $v->id);
        endforeach;
        return $arr;
    }

    public function getDetailArticleByCodeModel($code)
    {
        $sql = "SELECT TOP 1
                    ITMREF_0 AS code,
                    ITMDES1_0 AS libelle,
                    ZPCB_0 AS pcb,
                    ACCCOD_0 AS palettisation,
                    STU_0 AS unite_stockage
                FROM BASANEXP.ITMMASTER
                WHERE ITMREF_0 = ? COLLATE SQL_Latin1_General_CP1_CI_AS";

        $res = $this->dbX3->query($sql, [$code])->getRow();
        return $res;
    }

    public function getDetailArticleHorsX3ByCodeModel($code)
    {
        $sql = "SELECT 
                    code,
                    nom AS libelle,
                    palettisation AS palettisation,
                    unite_stockage AS unite_stockage
                FROM article_hors_x3
                WHERE code = ?
                LIMIT 1";
        $res = $this->db->query($sql, [$code])->getRow();
        return $res;
    }
}
