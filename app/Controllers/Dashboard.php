<?php

namespace App\Controllers;

use App\Models\LaporanModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $laporan = new LaporanModel();

        return view('dashboard', [
            'title'      => 'Dashboard',
            'ringkasan'  => $laporan->ringkasan(),
            'jadwal'     => $laporan->jadwalMendatang(5),
            'ekskul'     => $laporan->anggotaPerEkskul(),
            'kehadiran'  => $laporan->ringkasanKehadiran(),
        ]);
    }
}