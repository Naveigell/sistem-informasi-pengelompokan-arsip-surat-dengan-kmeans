<?php

namespace App\Http\Controllers;

use App\Actions\GenerateKmeans;

class KmeansController extends Controller
{
    public function calculate()
    {
        return redirect(route('dashboard.index'));
    }
}
