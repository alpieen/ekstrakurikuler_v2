<?php

namespace App\Models;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class LaporanModel
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // ------------------------------------------------------------------
    // Dashboard
    // ------------------------------------------------------------------

    /**
     * Total data utama untuk kartu ringkasan.
     */
    public function ringkasan(): array
    {
        return [
            'total_siswa'       => $this->db->table('siswa')->countAllResults(),
            'total_pembina'     => $this->db->table('pembina')->countAllResults(),
            'total_ekskul'      => $this->db->table('ekstrakurikuler')->countAllResults(),
            'pendaftaran_aktif' => $this->db->table('pendaftaran_ekskul')->where('status', 'Aktif')->countAllResults(),
            'total_jadwal'      => $this->db->table('jadwal_ekskul')->countAllResults(),
        ];
    }

    /**
     * Jadwal terdekat (hari ini dan seterusnya).
     */
    public function jadwalMendatang(int $limit = 5): array
    {
        return $this->db->table('jadwal_ekskul j')
            ->select('j.id_jadwal, j.tanggal, j.materi, j.tempat, e.nama_ekskul')
            ->join('ekstrakurikuler e', 'e.id_ekskul = j.id_ekskul')
            ->where('j.tanggal >=', date('Y-m-d'))
            ->orderBy('j.tanggal', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Jumlah anggota aktif per ekskul.
     */
    public function anggotaPerEkskul(): array
    {
        return $this->db->table('ekstrakurikuler e')
            ->select('e.id_ekskul, e.nama_ekskul, COUNT(p.id_pendaftaran) AS jumlah_anggota')
            ->join('pendaftaran_ekskul p', "p.id_ekskul = e.id_ekskul AND p.status = 'Aktif'", 'left')
            ->groupBy('e.id_ekskul, e.nama_ekskul')
            ->orderBy('jumlah_anggota', 'DESC')
            ->orderBy('e.nama_ekskul', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Total keseluruhan kehadiran per status (H, S, I, A).
     */
    public function ringkasanKehadiran(): array
    {
        $hasil = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];

        $rows = $this->db->table('kehadiran_ekskul')
            ->select('status, COUNT(*) AS total')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $hasil[$row['status']] = (int) $row['total'];
        }

        return $hasil;
    }

    // ------------------------------------------------------------------
    // Rekap / Laporan
    // ------------------------------------------------------------------

    /**
     * Rekap anggota dan kehadiran per siswa per ekskul.
     * Filter tanggal diterapkan pada jadwal, sehingga anggota tanpa
     * kehadiran pada rentang itu tetap muncul dengan nilai 0.
     *
     * @return list<array<string,mixed>>
     */
    public function rekapAnggota(?int $idEkskul = null, ?string $dari = null, ?string $sampai = null): array
    {
        $joinJadwal = 'j.id_ekskul = e.id_ekskul';

        if ($dari !== null) {
            $joinJadwal .= ' AND j.tanggal >= ' . $this->db->escape($dari);
        }

        if ($sampai !== null) {
            $joinJadwal .= ' AND j.tanggal <= ' . $this->db->escape($sampai);
        }

        $builder = $this->db->table('pendaftaran_ekskul p')
            ->select(
                "e.id_ekskul, e.nama_ekskul, s.nis, s.nama_siswa, s.kelas, "
                . "p.tanggal_daftar, p.status AS status_pendaftaran, "
                . "COALESCE(SUM(k.status = 'H'), 0) AS hadir, "
                . "COALESCE(SUM(k.status = 'S'), 0) AS sakit, "
                . "COALESCE(SUM(k.status = 'I'), 0) AS izin, "
                . "COALESCE(SUM(k.status = 'A'), 0) AS alpa, "
                . "COUNT(k.id_kehadiran) AS total_pertemuan",
                false
            )
            ->join('siswa s', 's.id_siswa = p.id_siswa')
            ->join('ekstrakurikuler e', 'e.id_ekskul = p.id_ekskul')
            ->join('jadwal_ekskul j', $joinJadwal, 'left')
            ->join('kehadiran_ekskul k', 'k.id_jadwal = j.id_jadwal AND k.id_siswa = s.id_siswa', 'left');

        if ($idEkskul !== null && $idEkskul > 0) {
            $builder->where('p.id_ekskul', $idEkskul);
        }

        $rows = $builder
            ->groupBy('p.id_pendaftaran, e.id_ekskul, e.nama_ekskul, s.id_siswa, s.nis, s.nama_siswa, s.kelas, p.tanggal_daftar, p.status')
            ->orderBy('e.nama_ekskul', 'ASC')
            ->orderBy('s.nama_siswa', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['hadir']           = (int) $row['hadir'];
            $row['sakit']           = (int) $row['sakit'];
            $row['izin']            = (int) $row['izin'];
            $row['alpa']            = (int) $row['alpa'];
            $row['total_pertemuan'] = (int) $row['total_pertemuan'];
            $row['persentase']      = $row['total_pertemuan'] > 0
                ? round($row['hadir'] / $row['total_pertemuan'] * 100, 1)
                : 0.0;
        }
        unset($row);

        return $rows;
    }
}