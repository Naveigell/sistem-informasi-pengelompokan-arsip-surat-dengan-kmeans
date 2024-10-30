<?php

namespace App\Actions;

use App\Models\File;
use App\Utils\Kmeans\Centroid;
use App\Utils\Kmeans\Document;
use App\Utils\Kmeans\Kmeans;

class GenerateKmeans
{
    public function generate()
    {
        $files = File::all()->map(function(File $file) {
            $document = new Document($file->real_name, $file);
            $document->setCustomWeights($this->customWeights());

            return $document;
        });

        $kmeans = new Kmeans($files, request('k', 2));
        $kmeans->setCentroids($this->createCustomCentroids($kmeans->makeUniqueWordLists()));
        $kmeans->clustering();

        \App\Models\Kmeans::truncate();

        foreach ($kmeans->getClusters()->values() as $index => $members) {
            foreach ($members as $document) {
                $model = new \App\Models\Kmeans(["cluster" => $index]);
                $model->file()->associate($document->getModel());
                $model->save();
            }
        }
    }

    public function customWeights()
    {
        return ["" => 0, "lpjk" => 500, "lpj" => 1000, "masuk" => 1500, "keluar" => 2000];
    }

    private function createCustomCentroids($uniqueWordLists)
    {
        $index = 0;

        return collect($this->customWeights())->map(function ($weight, $word) use ($uniqueWordLists, &$index) {

            $weights = array_fill(0, count($uniqueWordLists), 0);
            $array = array_combine($uniqueWordLists, $weights);

            if (!$word) {
                $centroid = Centroid::createWithCustomWords($array);
                $centroid->setIndex($index++);

                return $centroid;
            }

            $array[$word] = $weight;

            $centroid = Centroid::createWithCustomWords($array);
            $centroid->setIndex($index++);

            return $centroid;
        })->values();
    }
}
