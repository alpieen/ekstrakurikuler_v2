<?php

namespace App\Controllers;

use App\Models\EkstrakurikulerModel;
use App\Models\LaporanModel;
use DateTime;

class Rekap extends BaseController
{
    protected LaporanModel $laporanModel;
    protected EkstrakurikulerModel $ekskulModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
        $this->ekskulModel  = new EkstrakurikulerModel();
    }

    /**
     * Halaman rekap + filter ekskul & rentang tanggal.
     */
    public function index()
    {
        $filter = $this->filters();

        return view('rekap/index', [
            'title'       => 'Rekap Anggota dan Kehadiran',
            'reportTitle' => config('Project')->reportTitle,
            'filter'      => $filter,
            'ekskul'      => $this->ekskulModel->orderBy('nama_ekskul', 'ASC')->findAll(),
            'rekap'       => $this->laporanModel->rekapAnggota($filter['ekskul'], $filter['dari'], $filter['sampai']),
        ]);
    }

    /**
     * Versi cetak (tanpa layout utama) untuk Ctrl+P / simpan PDF dari browser.
     */
    public function cetak()
    {
        $filter = $this->filters();

        return view('rekap/cetak', [
            'title'       => 'Cetak Laporan',
            'reportTitle' => config('Project')->reportTitle,
            'filter'      => $filter,
            'tanggal'     => date('d-m-Y H:i'),
            'pembina'     => (string) session()->get('name'),
            'rekap'       => $this->laporanModel->rekapAnggota($filter['ekskul'], $filter['dari'], $filter['sampai']),
        ]);
    }

    /**
     * Ekspor CSV (pemisah titik koma agar rapi di Excel versi Indonesia).
     */
    public function export()
    {
        $filter = $this->filters();
        $rekap  = $this->laporanModel->rekapAnggota($filter['ekskul'], $filter['dari'], $filter['sampai']);

        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, [
            'Ekstrakurikuler', 'NIS', 'Nama Siswa', 'Kelas', 'Tanggal Daftar', 'Status',
            'Hadir', 'Sakit', 'Izin', 'Alpa', 'Total Pertemuan', 'Persentase Hadir (%)',
        ], ';', '"', '');

        foreach ($rekap as $row) {
            fputcsv($handle, [
                $row['nama_ekskul'],
                $row['nis'],
                $row['nama_siswa'],
                $row['kelas'],
                $row['tanggal_daftar'],
                $row['status_pendaftaran'],
                $row['hadir'],
                $row['sakit'],
                $row['izin'],
                $row['alpa'],
                $row['total_pertemuan'],
                $row['persentase'],
            ], ';', '"', '');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        // BOM agar karakter UTF-8 terbaca benar di Excel.
        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="rekap-ekskul-' . date('Ymd-His') . '.csv"')
            ->setBody("\xEF\xBB\xBF" . $csv);
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    /**
     * Ambil & bersihkan filter dari query string.
     *
     * @return array{ekskul:?int, dari:?string, sampai:?string}
     */
    private function filters(): array
    {
        $idEkskul = (int) $this->request->getGet('ekskul');
        $dari     = $this->validDate($this->request->getGet('dari'));
        $sampai   = $this->validDate($this->request->getGet('sampai'));

        // Jika terbalik, tukar agar rentang tetap masuk akal.
        if ($dari !== null && $sampai !== null && $dari > $sampai) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        return [
            'ekskul' => $idEkskul > 0 ? $idEkskul : null,
            'dari'   => $dari,
            'sampai' => $sampai,
        ];
    }

    private function validDate(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $date = DateTime::createFromFormat('Y-m-d', $value);

        return ($date && $date->format('Y-m-d') === $value) ? $value : null;
    }
}