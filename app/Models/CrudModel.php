<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\LibTrace;

class CrudModel extends Model
{
    protected $db_app;
    protected $logApp;
    protected $table;
    protected $userId;
    protected $fnTable;
    protected $typeBd;
    protected $ip;
    protected $db;

    public function __construct($tbl = null, $db = null)
    {
        parent::__construct();
        $session = \Config\Services::session();
        $this->db_app = db_connect();
        if ($db == null) {
            $this->db_app = db_connect();
            $this->table = $tbl;
        }

        if ($db != null) {
            $this->db_app = db_connect("connex_v12", true);
            $this->table = $tbl;
        }
    }

    /**
     * Insertion d'une ligne de données dans la table
     * @param Array $arr
     * @param Integer $actionId
     * @return Boolean
     */
    public function create($arr, $actionId)
    {
        if (!empty($arr)) {
            $this->db_app->transBegin();
            $arr['date_creation'] = date('Y-m-d H:i:s');
            $arr['cree_par'] = session()->get('utilisateur')['user_id'];
            $this->db_app->table($this->table)->insert($arr);
            if ($actionId != 0) {
                $arrHisto = [
                    'data_json' => json_encode($arr),
                    'utilisateur_id' =>  session()->get('utilisateur')['user_id'],
                    'action_id' => $actionId,
                ];
                $db = \Config\Database::connect();
                $builder = $db->table('historique');
                $builder->insert($arrHisto);
            }
            if ($this->db_app->transStatus() === false) {
                $this->db_app->transRollback();
                return 0;
            } else {
                $this->db_app->transCommit();
                return 1;
            }
        }
        return 0;
    }

    public function createReturnId($arr, $actionId)
    {
        if (!empty($arr)) {
            $session = \Config\Services::session();
            $this->db_app->transBegin();
            $arr['date_creation'] = date('Y-m-d H:i:s');
            $arr['cree_par'] = session()->get('utilisateur')['user_id'];
            $this->db_app->table($this->table)->insert($arr);
            $id = $this->db_app->insertID();

            if ($actionId != 0) {
                $arrHisto = [
                    'data_json' => json_encode($arr),
                    'utilisateur_id' =>  session()->get('utilisateur')['user_id'],
                    'action_id' => $actionId,
                ];
                $db = \Config\Database::connect();
                $builder = $db->table('historique');
                $builder->insert($arrHisto);
            }

            if ($this->db_app->transStatus() === false) {
                $this->db_app->transRollback();
                return NULL;
            } else {
                $this->db_app->transCommit();
                return $id;
            }
        }
        return NULL;
    }


    /**
     * Insertion de plusieurs lignes de données dans la table
     * @param Array $arr
     * @param Integer $actionId
     * @return Boolean
     */
    // public function createAll($arr, $actionId)
    // {
    //     if (!empty($arr)) {
    //         $this->db_app->transBegin();
    //         $this->db_app->table($this->table)->insertBatch($arr);
    //         $arrHisto = [
    //             'data_json' => json_encode($arr),
    //             'utilisateur_id' => !empty($this->session->infos_perm) ? $this->session->infos_perm['user_id'] : null,
    //             'action_id' => $actionId,
    //             'ip_machine' => $this->ip
    //         ];
    //         $this->logApp->histo(null, $this->table, $actionId, $arrHisto, $this->typeBd);
    //         if ($this->db_app->transStatus() === false) {
    //             $this->db_app->transRollback();
    //             return false;
    //         } else {
    //             $this->db_app->transCommit();
    //             return true;
    //         }
    //     }
    //     return false;
    // }

    /**
     * Mise à jour des données
     * @param Array $arrFilter
     * @param Integer $id
     * @param Array $arr
     * @param Integer $actionId
     * @return Boolean
     */
    public function maj($arrFilter, $arr, $actionId)
    {
        if (!empty($arrFilter) && !empty($arr)) {
            $this->db_app->transBegin();
            if ($actionId != 0) {
                $arr_old = $this->db->table($this->table)->where($arrFilter)->get()->getRowArray();
                $arrHisto = [
                    'data_json' => json_encode($arr_old),
                    'utilisateur_id' =>  session()->get('utilisateur')['user_id'],
                    'action_id' => $actionId,
                ];
                $db = \Config\Database::connect();
                $builder = $db->table('historique');
                $builder->insert($arrHisto);
            }
            $arr['date_modification'] = date('Y-m-d H:i:s');
            $arr['modifie_par'] = session()->get('utilisateur')['user_id'];
            $this->db_app->table($this->table)->where($arrFilter)->update($arr);
            if ($this->db_app->transStatus() === false) {
                $this->db_app->transRollback();
                return 0;
            } else {
                $this->db_app->transCommit();
                return 1;
            }
        }
        return 0;
    }

    public function del($arrFilter, $arr, $actionId)
    {
        if (!empty($arrFilter) && !empty($arr)) {
            $this->db_app->transBegin();
            if ($actionId != 0) {
                $arr_old = $this->db->table($this->table)->where($arrFilter)->get()->getRowArray();
                $arrHisto = [
                    'data_json' => json_encode($arr_old),
                    'utilisateur_id' => session()->get('utilisateur')['user_id'],
                    'action_id' => $actionId,
                ];
                $db = \Config\Database::connect();
                $builder = $db->table('historique');
                $builder->insert($arrHisto);
            }
            $arr['date_suppression'] = date('Y-m-d H:i:s');
            $arr['supprime_par'] =  session()->get('utilisateur')['user_id'];
            $this->db_app->table($this->table)->where($arrFilter)->update($arr);
            if ($this->db_app->transStatus() === false) {
                $this->db_app->transRollback();
                return 0;
            } else {
                $this->db_app->transCommit();
                return 1;
            }
        }
        return 0;
    }

    /**
     * Une ligne d'enregistrement de la table en utilisant un filtre
     * @param Array $arrFilter
     * @param Array $arrJoin
     * @param mixed $select
     * @return StdClass
     */
    public function getDataById($arrFilter, $arrJoin = [], $select = "*")
    {
        if (!empty($arrFilter)) {
            $builder = $this->db_app->table($this->table);
            if (!empty($arrJoin)) {
                foreach ($arrJoin as $key => $val) :
                    $builder->join($val['table'], $val['on'], $val['type']);
                endforeach;
            }
            return $builder->where($arrFilter)->select($select)->get()->getRow();
        }
    }

    /**
     * Toutes les données enregistrées dans la table
     * @param Array $arrFilter
     * @param Array $arrJoin
     * @param mixed $select
     * @param mixed $sort
     * @param mixed $group
     * @param mixed $order
     * @param mixed $distinct
     * @return StdClass
     */
    public function getAllData($arrFilter = [], $arrJoin = [], $select = "*", $sort = "", $group = "", $order = "asc", $distinct = "", $limit = 0)
    {
        $builder = $this->db_app->table($this->table);
        if (!empty($arrJoin)) {
            foreach ($arrJoin as $key => $val) :
                $builder->join($val['table'], $val['on'], $val['type']);
            endforeach;
        }
        if (!empty($arrFilter)) {
            $builder->where($arrFilter);
        }
        if ($group != "") {
            $builder->groupBy($group);
        }
        if ($sort != "" && $order != "") {
            $builder->orderBy($sort, $order);
        }
        if ($distinct != "") {
            $builder->distinct();
        }
        if ($limit > 0) {
            $builder->limit($limit);
        }
        $builder->select($select, false);
        return $builder->get()->getResult();
    }

    /**
     * Nombre de données enregistrées
     * @param Array $arrFilter
     *
     */
    public function getNb($arrFilter = [])
    {
        $builder = $this->db_app->table($this->table);
        if (!empty($arrFilter)) {
            $builder->where($arrFilter);
        }
        return $builder->countAllResults();
    }

    public function getSum($select, $arrFilter = [])
    {
        $builder = $this->db_app->table($this->table);
        if (!empty($arrFilter)) {
            $builder->where($arrFilter);
        }
        return $builder->selectSum($select, 'nb')->get()->getRow()->nb;
    }


    public function getDataByIdArray($arrFilter, $arrJoin = [], $select = "*")
    {
        if (!empty($arrFilter)) {
            $builder = $this->db_app->table($this->table);
            if (!empty($arrJoin)) {
                foreach ($arrJoin as $key => $val) :
                    $builder->join($val['table'], $val['on'], $val['type']);
                endforeach;
            }
            return $builder->where($arrFilter)->select($select)->get()->getRowArray();
        }
    }

    public function getAllDataArray($arrFilter = [], $arrJoin = [], $select = "*", $sort = "", $group = "", $order = "asc", $distinct = "", $limit = 0)
    {
        $builder = $this->db_app->table($this->table);
        if (!empty($arrJoin)) {
            foreach ($arrJoin as $key => $val) :
                $builder->join($val['table'], $val['on'], $val['type']);
            endforeach;
        }
        if (!empty($arrFilter)) {
            $builder->where($arrFilter);
        }
        if ($group != "") {
            $builder->groupBy($group);
        }
        if ($sort != "" && $order != "") {
            $builder->orderBy($sort, $order);
        }
        if ($distinct != "") {
            $builder->distinct();
        }
        if ($limit > 0) {
            $builder->limit($limit);
        }
        $builder->select($select, false);
        return $builder->get()->getResultArray();
    }

    public function getAllDataUnion($arr = [], $arrUnion = [], $arrFinal = [])
    {
        $builder = $this->db_app->table($this->table);
        if (!empty($arr['join'])) {
            foreach ($arr['join'] as $key => $val) :
                $builder->join($val['table'], $val['on'], $val['type']);
            endforeach;
        }
        if (!empty($arr['filter'])) {
            $builder->where($arr['filter']);
        }
        if ($arr['group'] != "") {
            $builder->groupBy($arr['group']);
        }
        if ($arr['sort'] != "" && $arr['order'] != "") {
            $builder->orderBy($arr['sort'], $arr['order']);
        }
        if ($arr['distinct'] != "") {
            $builder->distinct();
        }
        if ($arr['limit'] > 0) {
            $builder->limit($arr['limit']);
        }
        $builder->select($arr['select'], false);

        if (!empty($arrUnion)) {
            foreach ($arrUnion as $keyUnion => $valUnion) {
                $union = $this->db_app->table($valUnion['table']);
                if (!empty($valUnion['join'])) {
                    foreach ($valUnion['join'] as $key => $val) :
                        $union->join($val['table'], $val['on'], $val['type']);
                    endforeach;
                }
                if (!empty($valUnion['filter'])) {
                    $union->where($valUnion['filter']);
                }
                if ($valUnion['group'] != "") {
                    $union->groupBy($valUnion['group']);
                }
                if ($valUnion['sort'] != "" && $valUnion['order'] != "") {
                    $union->orderBy($valUnion['sort'], $valUnion['order']);
                }
                if ($valUnion['distinct'] != "") {
                    $union->distinct();
                }
                if ($valUnion['limit'] > 0) {
                    $union->limit($valUnion['limit']);
                }
                $union->select($valUnion['select'], false);
                $builder->union($union);
            }
        }
        if (!empty($arrFinal)) {
            $qr = $this->db_app->newQuery()->fromSubquery($builder, 'tab');
            if (!empty($arrFinal['filter'])) {
                $qr->where($arrFinal['filter']);
            }
            if ($arrFinal['group'] != "") {
                $qr->groupBy($arrFinal['group']);
            }
            if ($arr['sort'] != "" && $arrFinal['order'] != "") {
                $qr->orderBy($arrFinal['sort'], $arrFinal['order']);
            }
            if ($arrFinal['distinct'] != "") {
                $qr->distinct();
            }
            if ($arrFinal['limit'] > 0) {
                $qr->limit($arrFinal['limit']);
            }
            return $qr->get()->getResult();
        } else {
            return $builder->get()->getResult();
        }
    }


    /**
     * Vérifie si la colonne existe dans une table
     *
     * @param  mixed $table
     * @param  mixed $columnName
     */
    public function verifColumnOfTable($columnName)
    {
        return $this->db_app->table('information_schema.columns')
            ->where('table_name', $this->table)
            ->where('column_name', $columnName)
            ->select('column_name')
            ->countAllResults();
    }

    public function logInOut($actionId, $message)
    {
        if ($actionId != "" && $actionId != null) {
            $db = \Config\Database::connect();
            $this->db->transBegin();
            $arrHisto = [
                'data_json' => json_encode([$message]),
                'utilisateur_id' => session()->get('utilisateur')['user_id'],
                'action_id' => $actionId,
                'date_creation' => date('Y-m-d H:i:s'),
            ];

            $builder = $db->table('historique');
            $builder->insert($arrHisto);
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return 0;
            } else {
                $this->db->transCommit();
                return 1;
            }
        }
        return 0;
    }
}
