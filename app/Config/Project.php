<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Project extends BaseConfig
{
    public string $title = 'Sistem Ekstrakurikuler Sekolah';
    public string $database = 'db_ekstrakurikuler';
    public int $stage = 1;
    public array $authTables = ['pembina'];
    public array $authNameFields = ['nama_pembina', 'nama', 'username'];
    public array $resources = [];
    
    public array $allRelations = [
        'ekstrakurikuler' => [
            'id_pembina' => ['pembina', 'id_pembina', 'nama_pembina']
        ], 
        'pendaftaran_ekskul' => [
            'id_siswa'  => ['siswa', 'id_siswa', 'nama_siswa'], 
            'id_ekskul' => ['ekstrakurikuler', 'id_ekskul', 'nama_ekskul']
        ], 
        'jadwal_ekskul' => [
            'id_ekskul' => ['ekstrakurikuler', 'id_ekskul', 'nama_ekskul']
        ], 
        'kehadiran_ekskul' => [
            'id_jadwal' => ['jadwal_ekskul', 'id_jadwal', 'tanggal'], 
            'id_siswa'  => ['siswa', 'id_siswa', 'nama_siswa']
        ]
    ];
    
    public string $reportTitle = 'Laporan Anggota dan Kehadiran Ekstrakurikuler';
}