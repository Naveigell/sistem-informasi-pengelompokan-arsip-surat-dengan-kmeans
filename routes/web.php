<?php

use App\Utils\Kmeans\Kmeans;
use Illuminate\Support\Facades\Route;

use App\Utils\Kmeans\Document;

Route::get('/', function () {

    $documents = [
        new Document("saya suka makan nasi goreng"),
        new Document("saya suka makan nasi goreng dan ayam goreng"),
        new Document("dia suka makan ayam goreng"),
        new Document("dia suka soto ayam"),
        new Document("saya suka bakso"),
        new Document("saya suka soto ayam"),
        new Document("saya suka nasi goreng"),
        new Document("saya dan dia sama sama suka nasi goreng dan ayam goreng"),
        new Document("mereka suka bakso dan soto"),
        new Document("kami tidak suka soto ayam"),
        new Document("kami tidak suka nasi goreng"),
        new Document("kami suka ayam goreng"),
        new Document("mereka membuat bakso di luar"),
        new Document("mereka membuat ayam goreng"),
        new Document("aku sedang makan ayam goreng"),
        new Document("aku sedang makan ayam goreng di luar"),
        new Document("aku sedang makan ayam goreng di luar di kafe"),
        new Document("dia membuat bakso bakar"),
        new Document("bakso bakar dimakan oleh kami"),
        new Document("dia membuat ayam goreng di luar"),
        new Document("kami membuat ayam goreng di luar"),
        new Document("saya tidak suka makan nasi"),
        new Document("kamu suka makan nasi"),
        new Document("kami membuat telur dadar"),
        new Document("kami membuat telur dadar di luar"),
        new Document("saya suka makan nasi padang dan telur dadar"),
        new Document("mereka membuat roti"),
        new Document("roti dibuat mereka"),
        new Document("disana mereka makan roti"),
        new Document("roti dimakan oleh dia"),
    ];

    $kmeans = new Kmeans($documents);
});
