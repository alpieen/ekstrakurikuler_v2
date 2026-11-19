<?php

namespace App\Models;

use CodeIgniter\Model;

class EkstrakurikulerModel extends Model
{
    protected $table         = 'ekstrakurikuler';
    protected $primaryKey    = 'id_ekskul';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'nama_ekskul',
        'id_pembina',
        'hari',
        'jam',
    ];

    /**
     * Daftar ekskul + nama pembina (JOIN), dengan pencarian opsional.
     */
    public function search(?string $keyword = null): self
    {
        $this->select('ekstrakurikuler.*, pembina.nama_pembina')
            ->join('pembina', 'pembina.id_pembina = ekstrakurikuler.id_pembina');

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('ekstrakurikuler.nama_ekskul', $keyword)
                ->orLike('pembina.nama_pembina', $keyword)
                ->orLike('ekstrakurikuler.hari', $keyword)
                ->groupEnd();
        }

        return $this->orderBy('ekstrakurikuler.nama_ekskul', 'ASC');
    }

    /**
     * Cek apakah ekskul masih dipakai di pendaftaran atau jadwal (FK).
     */
    public function hasRelasi(int $id): bool
    {
        $pendaftaran = $this->db->table('pendaftaran_ekskul')->where('id_ekskul', $id)->countAllResults();
        $jadwal      = $this->db->table('jadwal_ekskul')->where('id_ekskul', $id)->countAllResults();

        return ($pendaftaran + $jadwal) > 0;
    }
}