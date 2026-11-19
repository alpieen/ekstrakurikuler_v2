<?php

namespace App\Models;

use CodeIgniter\Model;

class PendaftaranModel extends Model
{
    protected $table         = 'pendaftaran_ekskul';
    protected $primaryKey    = 'id_pendaftaran';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'id_siswa',
        'id_ekskul',
        'tanggal_daftar',
        'status',
    ];

    /**
     * Daftar pendaftaran + data siswa & ekskul (JOIN),
     * dengan pencarian dan filter status opsional.
     */
    public function search(?string $keyword = null, ?string $status = null): self
    {
        $this->select('pendaftaran_ekskul.*, siswa.nis, siswa.nama_siswa, siswa.kelas, ekstrakurikuler.nama_ekskul')
            ->join('siswa', 'siswa.id_siswa = pendaftaran_ekskul.id_siswa')
            ->join('ekstrakurikuler', 'ekstrakurikuler.id_ekskul = pendaftaran_ekskul.id_ekskul');

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('siswa.nis', $keyword)
                ->orLike('siswa.nama_siswa', $keyword)
                ->orLike('siswa.kelas', $keyword)
                ->orLike('ekstrakurikuler.nama_ekskul', $keyword)
                ->groupEnd();
        }

        if ($status !== null && $status !== '') {
            $this->where('pendaftaran_ekskul.status', $status);
        }

        return $this->orderBy('pendaftaran_ekskul.tanggal_daftar', 'DESC')
            ->orderBy('pendaftaran_ekskul.id_pendaftaran', 'DESC');
    }

    /**
     * Cek apakah siswa sudah terdaftar di ekskul yang sama.
     * $exceptId dipakai saat edit agar data sendiri tidak dihitung.
     */
    public function isDuplicate(int $idSiswa, int $idEkskul, ?int $exceptId = null): bool
    {
        $builder = $this->db->table($this->table)
            ->where('id_siswa', $idSiswa)
            ->where('id_ekskul', $idEkskul);

        if ($exceptId !== null) {
            $builder->where('id_pendaftaran !=', $exceptId);
        }

        return $builder->countAllResults() > 0;
    }
}