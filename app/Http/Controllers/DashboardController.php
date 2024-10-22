<?php

namespace App\Http\Controllers;

use App\Models\Kmeans;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $clusters = Kmeans::with('file')->get()->groupBy('cluster');

        return view('upload', compact('clusters'));
    }
}
