<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ---------- Publik ----------
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');

// ---------- Wajib login ----------
$routes->group('', ['filter' => 'auth'], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Siswa
    $routes->group('data/siswa', static function ($routes) {
        $routes->get('/', 'Siswa::index');
        $routes->get('create', 'Siswa::create');
        $routes->post('store', 'Siswa::store');
        $routes->get('edit/(:num)', 'Siswa::edit/$1');
        $routes->post('update/(:num)', 'Siswa::update/$1');
        $routes->post('delete/(:num)', 'Siswa::delete/$1');
    });

    // Pembina
    $routes->group('data/pembina', static function ($routes) {
        $routes->get('/', 'Pembina::index');
        $routes->get('create', 'Pembina::create');
        $routes->post('store', 'Pembina::store');
        $routes->get('edit/(:num)', 'Pembina::edit/$1');
        $routes->post('update/(:num)', 'Pembina::update/$1');
        $routes->post('delete/(:num)', 'Pembina::delete/$1');
    });

    // Ekstrakurikuler
    $routes->group('data/ekstrakurikuler', static function ($routes) {
        $routes->get('/', 'Ekstrakurikuler::index');
        $routes->get('create', 'Ekstrakurikuler::create');
        $routes->post('store', 'Ekstrakurikuler::store');
        $routes->get('edit/(:num)', 'Ekstrakurikuler::edit/$1');
        $routes->post('update/(:num)', 'Ekstrakurikuler::update/$1');
        $routes->post('delete/(:num)', 'Ekstrakurikuler::delete/$1');
    });

    // Pendaftaran Ekskul
    $routes->group('data/pendaftaran_ekskul', static function ($routes) {
        $routes->get('/', 'Pendaftaran::index');
        $routes->get('create', 'Pendaftaran::create');
        $routes->post('store', 'Pendaftaran::store');
        $routes->get('edit/(:num)', 'Pendaftaran::edit/$1');
        $routes->post('update/(:num)', 'Pendaftaran::update/$1');
        $routes->post('delete/(:num)', 'Pendaftaran::delete/$1');
    });

    // Jadwal Ekskul
    $routes->group('data/jadwal_ekskul', static function ($routes) {
        $routes->get('/', 'Jadwal::index');
        $routes->get('create', 'Jadwal::create');
        $routes->post('store', 'Jadwal::store');
        $routes->get('edit/(:num)', 'Jadwal::edit/$1');
        $routes->post('update/(:num)', 'Jadwal::update/$1');
        $routes->post('delete/(:num)', 'Jadwal::delete/$1');
    });

    // Kehadiran Ekskul
    $routes->group('data/kehadiran_ekskul', static function ($routes) {
        $routes->get('/', 'Kehadiran::index');
        $routes->get('create', 'Kehadiran::create');
        $routes->post('store', 'Kehadiran::store');
        $routes->get('edit/(:num)', 'Kehadiran::edit/$1');
        $routes->post('update/(:num)', 'Kehadiran::update/$1');
        $routes->post('delete/(:num)', 'Kehadiran::delete/$1');

        // Input massal per jadwal
        $routes->get('input/(:num)', 'Kehadiran::input/$1');
        $routes->post('simpan/(:num)', 'Kehadiran::simpan/$1');
    });

    // Rekap / Laporan
    $routes->group('rekap', static function ($routes) {
        $routes->get('/', 'Rekap::index');
        $routes->get('cetak', 'Rekap::cetak');
        $routes->get('export', 'Rekap::export');
    });
});