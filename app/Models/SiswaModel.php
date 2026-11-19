<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table         = 'siswa';
    protected $primaryKey    = 'id_siswa';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'nis',
        'nama_siswa',
        'kelas',
    ];

    /**
     * Cari siswa berdasarkan NIS, nama, atau kelas.
     */
    public function search(?string $keyword = null): self
    {
        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('nis', $keyword)
                ->orLike('nama_siswa', $keyword)
                ->orLike('kelas', $keyword)
                ->groupEnd();
        }

        return $this->orderBy('nama_siswa', 'ASC');
    }

    /**
     * Cek apakah siswa masih dipakai di tabel lain (FK),
     * supaya tidak error saat dihapus.
     */
    public function hasRelasi(int $id): bool
    {
        $db = $this->db;

        $pendaftaran = $db->table('pendaftaran_ekskul')->where('id_siswa', $id)->countAllResults();
        $kehadiran   = $db->table('kehadiran_ekskul')->where('id_siswa', $id)->countAllResults();

        return ($pendaftaran + $kehadiran) > 0;
    }
}