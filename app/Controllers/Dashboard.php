<?php

namespace App\Controllers;

use App\Controllers\Acces;
use App\Models\DashboardModel;

class Dashboard extends BaseController
{

    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(7);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Tableau de bord";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('dashboard', $arr);
            return;
        }
        echo view('dashboard', $arr);
    }

    public function getDataForChartPie()
    {
        $model = new DashboardModel();
        $periode = $this->request->getPost('periode');
        $arrSumEmplacement = $model->getEmplacement();
        $arrSumPalette = $model->getPalette();
        $arrSumMouvement = $model->getMouvement($periode);
        $arrEntrepot = $model->getEntrepot();
        $arrFluxMouvement = $model->getMouvementFlux($periode);
        $result = [
            'emplacement' => $arrSumEmplacement,
            'palette'     => $arrSumPalette,
            'mouvement'     => $arrSumMouvement,
            'entrepot'     => $arrEntrepot,
            'fluxMouvement' => $arrFluxMouvement
        ];
        return json_encode($result);
    }
}
