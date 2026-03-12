<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;

class Dashboard extends BaseController
{
    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(7);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Tbaleau de bord";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('dashboard/list_view', $arr);
            return;
        }
        echo view('dashboard/list_view', $arr);
    }
}
