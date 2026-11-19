<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table         = 'jadwal_ekskul';
    protected $primaryKey    = 'id_jadwal';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'id_ekskul',
        'tanggal',
        'materi',
        'tempat',
    ];

    /**
     * Daftar jadwal + nama ekskul + jumlah kehadiran yang sudah tercatat.
     */
    public function search(?string $keyword = null, ?int $idEkskul = null): self
    {
        $this->select(
            'jadwal_ekskul.*, ekstrakurikuler.nama_ekskul, '
            . '(SELECT COUNT(*) FROM kehadiran_ekskul k WHERE k.id_jadwal = jadwal_ekskul.id_jadwal) AS jumlah_kehadiran',
            false
        )->join('ekstrakurikuler', 'ekstrakurikuler.id_ekskul = jadwal_ekskul.id_ekskul');

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('jadwal_ekskul.materi', $keyword)
                ->orLike('jadwal_ekskul.tempat', $keyword)
                ->orLike('ekstrakurikuler.nama_ekskul', $keyword)
                ->groupEnd();
        }

        if ($idEkskul !== null && $idEkskul > 0) {
            $this->where('jadwal_ekskul.id_ekskul', $idEkskul);
        }

        return $this->orderBy('jadwal_ekskul.tanggal', 'DESC')
            ->orderBy('jadwal_ekskul.id_jadwal', 'DESC');
    }

    /**
     * Detail satu jadwal beserta nama ekskul.
     */
    public function detail(int $id): ?array
    {
        return $this->select('jadwal_ekskul.*, ekstrakurikuler.nama_ekskul')
            ->join('ekstrakurikuler', 'ekstrakurikuler.id_ekskul = jadwal_ekskul.id_ekskul')
            ->where('jadwal_ekskul.id_jadwal', $id)
            ->first();
    }

    /**
     * Daftar ringkas jadwal untuk dropdown (terbaru dulu).
     */
    public function options(): array
    {
        return $this->select('jadwal_ekskul.id_jadwal, jadwal_ekskul.tanggal, jadwal_ekskul.materi, ekstrakurikuler.nama_ekskul')
            ->join('ekstrakurikuler', 'ekstrakurikuler.id_ekskul = jadwal_ekskul.id_ekskul')
            ->orderBy('jadwal_ekskul.tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Cek apakah jadwal sudah punya data kehadiran (FK).
     */
    public function hasRelasi(int $id): bool
    {
        return $this->db->table('kehadiran_ekskul')
            ->where('id_jadwal', $id)
            ->countAllResults() > 0;
    }
}