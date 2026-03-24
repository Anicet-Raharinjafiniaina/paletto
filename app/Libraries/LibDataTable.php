<?php

namespace App\Libraries;

class LibDataTable
{
    // public function index($table, $columns, $searchable = [], $action = [])
    // {
    //     $request = service('request');
    //     $db = \Config\Database::connect();

    //     $start  = $request->getPost('start');
    //     $length = $request->getPost('length');
    //     $search = $request->getPost('search')['value'];
    //     $order  = $columns[$request->getPost('order')[0]['column']] ?? $columns[0];
    //     $dir    = $request->getPost('order')[0]['dir'] ?? 'asc';

    //     // Construction de la requête
    //     $builder = $db->table($table)->select($columns);

    //     // Recherche dynamique
    //     if (!empty($search) && !empty($searchable)) {
    //         $builder->groupStart();
    //         foreach ($searchable as $col) {
    //             $builder->orLike($col, $search);
    //         }
    //         $builder->groupEnd();
    //     }

    //     // Total filtré
    //     $filteredCount = $builder->countAllResults(false);

    //     // Pagination + tri
    //     $builder->orderBy($order, $dir)
    //         ->limit($length, $start);

    //     $data = $builder->get()->getResultArray();

    //     foreach ($data as &$row) {
    //         $row['actions'] = $action; // Ajouter les actions à chaque ligne
    //     }

    //     // Total global
    //     $totalCount = $db->table($table)->countAll();

    //     return json_encode([
    //         "draw" => intval($request->getPost('draw')),
    //         "recordsTotal" => $totalCount,
    //         "recordsFiltered" => $filteredCount,
    //         "data" => $data
    //     ]);
    // }

    public function index($table, $columns, $searchable = [], $action = [], $joins = [], $where = [], $orderBy = null)
    {
        $request = service('request');
        $db = \Config\Database::connect();

        $start  = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'] ?? '';

        $orderIndex = $request->getPost('order')[0]['column'] ?? 0;
        $order  = $columns[$orderIndex] ?? $columns[0];
        $dir    = $request->getPost('order')[0]['dir'] ?? 'asc';

        $builder = $db->table($table)->select($columns);

        // ✅ Ajouter les jointures
        if (!empty($joins)) {
            foreach ($joins as $join) {
                $builder->join(
                    $join['table'],
                    $join['condition'],
                    $join['type'] ?? 'left'
                );
            }
        }

        // ✅ WHERE dynamique
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                if (!is_array($value)) {
                    $builder->where($key, $value);
                }
            }
        }

        // order By
        if ($orderBy) {
            $order = $orderBy['column'];
            $dir   = $orderBy['dir'];
        }

        // Recherche
        if (!empty($search) && !empty($searchable)) {
            $builder->groupStart();
            foreach ($searchable as $col) {
                // $builder->orLike($col, $search);
                $builder->orLike($col, $search, 'both', null, true);
            }
            $builder->groupEnd();
        }

        // Total filtré
        $filteredCount = $builder->countAllResults(false);

        // Pagination + tri
        $builder->orderBy($order, $dir)
            ->limit($length, $start);

        $data = $builder->get()->getResultArray();

        foreach ($data as &$row) {
            $row['actions'] = $action;
        }

        // ⚠️ Total global sans filtre mais avec jointure si nécessaire
        $builderTotal = $db->table($table);

        if (!empty($joins)) {
            foreach ($joins as $join) {
                $builderTotal->join(
                    $join['table'],
                    $join['condition'],
                    $join['type'] ?? 'left'
                );
            }
        }

        $totalCount = $builderTotal->countAllResults();

        return json_encode([
            "draw" => intval($request->getPost('draw')),
            "recordsTotal" => $totalCount,
            "recordsFiltered" => $filteredCount,
            "data" => $data
        ]);
    }
}
