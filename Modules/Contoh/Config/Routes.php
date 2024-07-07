<?php
$routes->group('home', ["namespace" => "\Modules\Contoh\Controllers"], function ($routes) {
    $routes->get('', "Home::index");
    // $routes->get('orderan', "Orderan::index");

    // //kategori ::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    // $routes->get('produk/kategori', "Kategori::index", ['filter' => 'authfilter']);
    // $routes->post('produk/kategori', "Kategori::tambah", ['filter' => 'authfilter']);
    // $routes->post('produk/kategori/edit/(:any)', "Kategori::edit/$1", ['filter' => 'authfilter']);
    // $routes->get('produk/kategori/hapus/(:any)', "Kategori::hapus/$1", ['filter' => 'authfilter']);
    // $routes->get('produk/kategori/(:any)', "Kategori::ambil/$1", ['filter' => 'authfilter']);
});
