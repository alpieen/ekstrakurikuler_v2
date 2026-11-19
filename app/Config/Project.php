<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Project extends BaseConfig
{
    public string $title = 'Sistem Ekstrakurikuler Sekolah';
    public string $database = 'db_ekstrakurikuler';
    public int $stage = 12;
    public array $authTables = ['pembina'];
    public array $authNameFields = ['nama_pembina', 'nama', 'username'];

    /**
     * Menu sidebar: key = nama tabel (dipakai sebagai URL data/{key}).
     */
    public array $resources = [
        'siswa'              => ['title' => 'Data Siswa'],
        'pembina'            => ['title' => 'Data Pembina'],
        'ekstrakurikuler'    => ['title' => 'Ekstrakurikuler'],
        'pendaftaran_ekskul' => ['title' => 'Pendaftaran Ekskul'],
        'jadwal_ekskul'      => ['title' => 'Jadwal Ekskul'],
        'kehadiran_ekskul'   => ['title' => 'Kehadiran Ekskul'],
    ];

    public array $allRelations = [
        'ekstrakurikuler' => [
            'id_pembina' => ['pembina', 'id_pembina', 'nama_pembina'],
        ],
        'pendaftaran_ekskul' => [
            'id_siswa'  => ['siswa', 'id_siswa', 'nama_siswa'],
            'id_ekskul' => ['ekstrakurikuler', 'id_ekskul', 'nama_ekskul'],
        ],
        'jadwal_ekskul' => [
            'id_ekskul' => ['ekstrakurikuler', 'id_ekskul', 'nama_ekskul'],
        ],
        'kehadiran_ekskul' => [
            'id_jadwal' => ['jadwal_ekskul', 'id_jadwal', 'tanggal'],
            'id_siswa'  => ['siswa', 'id_siswa', 'nama_siswa'],
        ],
    ];

    public string $reportTitle = 'Laporan Anggota dan Kehadiran Ekstrakurikuler';
}