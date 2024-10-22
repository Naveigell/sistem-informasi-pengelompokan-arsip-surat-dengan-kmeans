<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Utils\Kmeans\Document;
use App\Utils\Kmeans\Kmeans;
use Illuminate\Http\Request;

class KmeansController extends Controller
{
    public function calculate()
    {
        $files = File::all()->map(fn(File $file) => new Document($file->real_name, $file));

        $kmeans = new Kmeans($files, \request('k', 2));
        $kmeans->clustering();

        \App\Models\Kmeans::truncate();

        foreach ($kmeans->getClusters()->values() as $index => $members) {
            foreach ($members as $document) {
                $model = new \App\Models\Kmeans(["cluster" => $index]);
                $model->file()->associate($document->getModel());
                $model->save();
            }
        }

        return redirect(route('dashboard.index'));
    }
}
