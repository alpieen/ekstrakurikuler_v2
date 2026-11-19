<?php

namespace App\Models;

use CodeIgniter\Model;

class KehadiranModel extends Model
{
    protected $table         = 'kehadiran_ekskul';
    protected $primaryKey    = 'id_kehadiran';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'id_jadwal',
        'id_siswa',
        'status',
        'keterangan',
    ];

    /**
     * Daftar kehadiran + siswa, jadwal, dan ekskul (JOIN),
     * dengan pencarian, filter jadwal, dan filter status opsional.
     */
    public function search(?string $keyword = null, ?int $idJadwal = null, ?string $status = null): self
    {
        $this->select(
            'kehadiran_ekskul.*, siswa.nis, siswa.nama_siswa, siswa.kelas, '
            . 'jadwal_ekskul.tanggal, jadwal_ekskul.materi, ekstrakurikuler.nama_ekskul'
        )
            ->join('siswa', 'siswa.id_siswa = kehadiran_ekskul.id_siswa')
            ->join('jadwal_ekskul', 'jadwal_ekskul.id_jadwal = kehadiran_ekskul.id_jadwal')
            ->join('ekstrakurikuler', 'ekstrakurikuler.id_ekskul = jadwal_ekskul.id_ekskul');

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('siswa.nis', $keyword)
                ->orLike('siswa.nama_siswa', $keyword)
                ->orLike('siswa.kelas', $keyword)
                ->orLike('ekstrakurikuler.nama_ekskul', $keyword)
                ->groupEnd();
        }

        if ($idJadwal !== null && $idJadwal > 0) {
            $this->where('kehadiran_ekskul.id_jadwal', $idJadwal);
        }

        if ($status !== null && $status !== '') {
            $this->where('kehadiran_ekskul.status', $status);
        }

        return $this->orderBy('jadwal_ekskul.tanggal', 'DESC')
            ->orderBy('siswa.nama_siswa', 'ASC');
    }

    /**
     * Cek apakah siswa sudah punya catatan kehadiran di jadwal yang sama.
     */
    public function isDuplicate(int $idJadwal, int $idSiswa, ?int $exceptId = null): bool
    {
        $builder = $this->db->table($this->table)
            ->where('id_jadwal', $idJadwal)
            ->where('id_siswa', $idSiswa);

        if ($exceptId !== null) {
            $builder->where('id_kehadiran !=', $exceptId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Cek apakah siswa adalah anggota AKTIF dari ekskul milik jadwal tersebut.
     */
    public function isAnggotaAktif(int $idJadwal, int $idSiswa): bool
    {
        $sql = 'SELECT COUNT(*) AS total
                FROM jadwal_ekskul j
                JOIN pendaftaran_ekskul p ON p.id_ekskul = j.id_ekskul
                WHERE j.id_jadwal = ? AND p.id_siswa = ? AND p.status = ?';

        $row = $this->db->query($sql, [$idJadwal, $idSiswa, 'Aktif'])->getRowArray();

        return (int) ($row['total'] ?? 0) > 0;
    }

    /**
     * Anggota aktif dari ekskul sebuah jadwal, lengkap dengan
     * status kehadiran yang sudah tercatat (jika ada).
     */
    public function anggotaJadwal(int $idJadwal): array
    {
        $sql = 'SELECT s.id_siswa, s.nis, s.nama_siswa, s.kelas,
                       k.id_kehadiran, k.status, k.keterangan
                FROM jadwal_ekskul j
                JOIN pendaftaran_ekskul p ON p.id_ekskul = j.id_ekskul AND p.status = ?
                JOIN siswa s ON s.id_siswa = p.id_siswa
                LEFT JOIN kehadiran_ekskul k ON k.id_jadwal = j.id_jadwal AND k.id_siswa = s.id_siswa
                WHERE j.id_jadwal = ?
                GROUP BY s.id_siswa, s.nis, s.nama_siswa, s.kelas, k.id_kehadiran, k.status, k.keterangan
                ORDER BY s.nama_siswa ASC';

        return $this->db->query($sql, ['Aktif', $idJadwal])->getResultArray();
    }

    /**
     * Simpan kehadiran banyak siswa sekaligus (insert baru / update yang sudah ada).
     *
     * @param array<int, array{id_siswa:int, status:string, keterangan:?string}> $rows
     */
    public function simpanMassal(int $idJadwal, array $rows): bool
    {
        $this->db->transStart();

        foreach ($rows as $row) {
            $data = [
                'id_jadwal'  => $idJadwal,
                'id_siswa'   => $row['id_siswa'],
                'status'     => $row['status'],
                'keterangan' => $row['keterangan'],
            ];

            $existing = $this->where('id_jadwal', $idJadwal)
                ->where('id_siswa', $row['id_siswa'])
                ->first();

            if ($existing) {
                $this->update($existing['id_kehadiran'], $data);
            } else {
                $this->insert($data);
            }
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}