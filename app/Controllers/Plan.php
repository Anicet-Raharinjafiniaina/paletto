<?php

namespace App\Controllers;

use App\Models\CrudModel;

class Plan extends BaseController
{
    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(11);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Plan d'un entrepôt";
        $crud = new CrudModel(TBL_ENTREPOT);
        $arr['arr_entrepot'] = $crud->getAllData(array('flag_suppression' => 0), [], "*");
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('plan/plan_view', $arr);
            return;
        }
        echo view('plan/plan_view', $arr);
    }

    public function getDetail()
    {
        $acces = new Acces();
        $is_ok = $acces->is_ok(11);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }

        $id = trim($this->request->getVar('id'));

        // 1. Données nb emplacements (libre/occupé)
        $nbEmplacement = $this->getNbEmplacementByEntrepotId($id); // nb emplacements (libre/occupé)
        $tree = $this->getHierarchiqueData($id); //Hiérarchie pour le tableau

        // 3. Générer le HTML du tableau dans un buffer
        ob_start();
        include APPPATH . 'Views/plan/tableau.php';
        $tableauHtml = ob_get_clean();

        return json_encode([
            'libre'   => $nbEmplacement['libre'],
            'occupe'  => $nbEmplacement['occupe'],
            'tableau' => $tableauHtml,
        ]);
    }

    public function getNbEmplacementByEntrepotId($id)
    {
        $crud = new CrudModel(VIEW_EMPLACEMENT_ADRESSE);
        $arr['libre'] = $crud->getNb(['entrepot_id' => $id, 'statut_id' => 1]);
        $arr['occupe'] = $crud->getNb(['entrepot_id' => $id, 'statut_id' => 2]);
        return $arr;
    }

    /**
     * Récupère tous les emplacements d'un entrepôt en une seule requête
     * et reconstruit la hiérarchie après.
     */
    public function getDataEmplacementByCageId($id): array
    {
        $crud = new CrudModel(VIEW_EMPLACEMENT_ADRESSE);

        $columns = implode(',', [
            'entrepot_id',
            'entrepot_code',
            'allee_id',
            'allee_code',
            'rangee_id',
            'rangee_code',
            'niveau_id',
            'niveau_code',
            'cage_id',
            'cage_code',
            'emplacement_adresse_id',
            'emplacement_id',
            'emplacement_code',
            'statut_id',
            'qr_code_texte'
        ]);

        $arr = $crud->getAllData(['entrepot_id' => $id], [], $columns, 'allee_code,rangee_code,niveau_code,cage_code,emplacement_code', '', '', 'asc');
        return (array)$arr;
    }

    public function getHierarchiqueData($id)
    {
        $flat = $this->getDataEmplacementByCageId($id);
        $tree = [];

        foreach ($flat as $row) {
            $row = (array) $row;

            $alleeId  = $row['allee_id'];
            $rangeeId = $row['rangee_id'];
            $niveauId = $row['niveau_id'];
            $cageId   = $row['cage_id'];

            // Allée
            if (!isset($tree[$alleeId])) {
                $tree[$alleeId] = [
                    'allee_code' => $row['allee_code'],
                    'rangees'    => [],
                ];
            }

            // Rangée
            if (!isset($tree[$alleeId]['rangees'][$rangeeId])) {
                $tree[$alleeId]['rangees'][$rangeeId] = [
                    'rangee_code' => $row['rangee_code'],
                    'niveaux'     => [],
                ];
            }

            // Niveau
            if (!isset($tree[$alleeId]['rangees'][$rangeeId]['niveaux'][$niveauId])) {
                $tree[$alleeId]['rangees'][$rangeeId]['niveaux'][$niveauId] = [
                    'niveau_code' => $row['niveau_code'],
                    'cages'       => [],
                ];
            }

            // Cage
            if (!isset($tree[$alleeId]['rangees'][$rangeeId]['niveaux'][$niveauId]['cages'][$cageId])) {
                $tree[$alleeId]['rangees'][$rangeeId]['niveaux'][$niveauId]['cages'][$cageId] = [
                    'cage_code'    => $row['cage_code'],
                    'emplacements' => [],
                ];
            }

            // Emplacement
            $tree[$alleeId]['rangees'][$rangeeId]['niveaux'][$niveauId]['cages'][$cageId]['emplacements'][] = [
                'emplacement_id'         => $row['emplacement_id'],
                'emplacement_code'       => $row['emplacement_code'],
                'emplacement_adresse_id' => $row['emplacement_adresse_id'],
                'statut_id'              => $row['statut_id'],
                'qr_code_texte'          => $row['qr_code_texte'],
            ];
        }

        return $tree;
    }
}
