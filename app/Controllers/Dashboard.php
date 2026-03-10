<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;

class Dashboard extends BaseController
{
    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(5);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }
}
