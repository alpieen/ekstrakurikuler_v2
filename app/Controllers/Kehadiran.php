<?php

namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\KehadiranModel;
use App\Models\SiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Kehadiran extends BaseController
{
    protected KehadiranModel $kehadiranModel;
    protected JadwalModel $jadwalModel;
    protected SiswaModel $siswaModel;

    /** Kode status sesuai ENUM di database => label. */
    private const STATUS = [
        'H' => 'Hadir',
        'S' => 'Sakit',
        'I' => 'Izin',
        'A' => 'Alpa',
    ];

    public function __construct()
    {
        $this->kehadiranModel = new KehadiranModel();
        $this->jadwalModel    = new JadwalModel();
        $this->siswaModel     = new SiswaModel();
    }

    // ------------------------------------------------------------------
    // CRUD
    // ------------------------------------------------------------------

    /**
     * Daftar kehadiran + pencarian + filter jadwal & status + pagination.
     */
    public function index()
    {
        $keyword  = trim((string) $this->request->getGet('q'));
        $idJadwal = (int) $this->request->getGet('jadwal');
        $status   = trim((string) $this->request->getGet('status'));

        if (! array_key_exists($status, self::STATUS)) {
            $status = '';
        }

        $data = [
            'title'       => 'Kehadiran Ekstrakurikuler',
            'keyword'     => $keyword,
            'idJadwal'    => $idJadwal,
            'status'      => $status,
            'statusLabel' => self::STATUS,
            'jadwalList'  => $this->jadwalModel->options(),
            'kehadiran'   => $this->kehadiranModel->search($keyword, $idJadwal, $status)->paginate(10),
            'pager'       => $this->kehadiranModel->pager,
        ];

        return view('kehadiran/index', $data);
    }

    /**
     * Form tambah kehadiran (satu siswa).
     */
    public function create()
    {
        return view('kehadiran/form', $this->formData(
            'Tambah Kehadiran',
            null,
            site_url('data/kehadiran_ekskul/store')
        ));
    }

    /**
     * Simpan kehadiran baru.
     */
    public function store()
    {
        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = $this->payload();

        if ($error = $this->checkBusinessRules($payload)) {
            return redirect()->back()->withInput()->with('errors', $error);
        }

        $this->kehadiranModel->insert($payload);

        return redirect()->to('data/kehadiran_ekskul')->with('success', 'Data kehadiran berhasil ditambahkan.');
    }

    /**
     * Form edit kehadiran.
     */
    public function edit(int $id)
    {
        return view('kehadiran/form', $this->formData(
            'Edit Kehadiran',
            $this->findOrFail($id),
            site_url('data/kehadiran_ekskul/update/' . $id)
        ));
    }

    /**
     * Simpan perubahan kehadiran.
     */
    public function update(int $id)
    {
        $this->findOrFail($id);

        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = $this->payload();

        if ($error = $this->checkBusinessRules($payload, $id)) {
            return redirect()->back()->withInput()->with('errors', $error);
        }

        $this->kehadiranModel->update($id, $payload);

        return redirect()->to('data/kehadiran_ekskul')->with('success', 'Data kehadiran berhasil diperbarui.');
    }

    /**
     * Hapus kehadiran.
     */
    public function delete(int $id)
    {
        $this->findOrFail($id);

        $this->kehadiranModel->delete($id);

        return redirect()->to('data/kehadiran_ekskul')->with('success', 'Data kehadiran berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // Input massal per jadwal
    // ------------------------------------------------------------------

    /**
     * Halaman input kehadiran semua anggota aktif untuk satu jadwal.
     */
    public function input(int $idJadwal)
    {
        $jadwal = $this->jadwalModel->detail($idJadwal);

        if (! $jadwal) {
            throw PageNotFoundException::forPageNotFound('Data jadwal tidak ditemukan.');
        }

        return view('kehadiran/input', [
            'title'       => 'Input Kehadiran',
            'jadwal'      => $jadwal,
            'anggota'     => $this->kehadiranModel->anggotaJadwal($idJadwal),
            'statusLabel' => self::STATUS,
            'action'      => site_url('data/kehadiran_ekskul/simpan/' . $idJadwal),
        ]);
    }

    /**
     * Simpan kehadiran massal.
     * Input: status[id_siswa] dan keterangan[id_siswa].
     * Siswa tanpa status terpilih dilewati.
     */
    public function simpan(int $idJadwal)
    {
        if (! $this->jadwalModel->find($idJadwal)) {
            throw PageNotFoundException::forPageNotFound('Data jadwal tidak ditemukan.');
        }

        $statusInput     = (array) $this->request->getPost('status');
        $keteranganInput = (array) $this->request->getPost('keterangan');

        $rows = [];

        foreach ($this->kehadiranModel->anggotaJadwal($idJadwal) as $anggota) {
            $idSiswa = (int) $anggota['id_siswa'];
            $status  = trim((string) ($statusInput[$idSiswa] ?? ''));

            if ($status === '') {
                continue;
            }

            if (! array_key_exists($status, self::STATUS)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Status kehadiran untuk ' . $anggota['nama_siswa'] . ' tidak valid.');
            }

            $keterangan = trim((string) ($keteranganInput[$idSiswa] ?? ''));

            if (mb_strlen($keterangan) > 255) {
                return redirect()->back()->withInput()
                    ->with('error', 'Keterangan untuk ' . $anggota['nama_siswa'] . ' maksimal 255 karakter.');
            }

            $rows[] = [
                'id_siswa'   => $idSiswa,
                'status'     => $status,
                'keterangan' => $keterangan !== '' ? $keterangan : null,
            ];
        }

        if ($rows === []) {
            return redirect()->back()->withInput()->with('error', 'Belum ada status kehadiran yang dipilih.');
        }

        if (! $this->kehadiranModel->simpanMassal($idJadwal, $rows)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data kehadiran. Silakan coba lagi.');
        }

        return redirect()->to('data/kehadiran_ekskul/input/' . $idJadwal)
            ->with('success', count($rows) . ' data kehadiran berhasil disimpan.');
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    private function findOrFail(int $id): array
    {
        $kehadiran = $this->kehadiranModel->find($id);

        if (! $kehadiran) {
            throw PageNotFoundException::forPageNotFound('Data kehadiran tidak ditemukan.');
        }

        return $kehadiran;
    }

    private function formData(string $title, ?array $kehadiran, string $action): array
    {
        return [
            'title'          => $title,
            'kehadiran'      => $kehadiran,
            'action'         => $action,
            'jadwalList'     => $this->jadwalModel->options(),
            'siswa'          => $this->siswaModel->orderBy('nama_siswa', 'ASC')->findAll(),
            'statusLabel'    => self::STATUS,
            'selectedJadwal' => (int) $this->request->getGet('jadwal'),
        ];
    }

    /**
     * Aturan bisnis: siswa harus anggota aktif ekskul jadwal tsb,
     * dan tidak boleh ada catatan ganda pada jadwal yang sama.
     *
     * @return array<string,string>|null  pesan error, atau null jika lolos
     */
    private function checkBusinessRules(array $payload, ?int $exceptId = null): ?array
    {
        if (! $this->kehadiranModel->isAnggotaAktif($payload['id_jadwal'], $payload['id_siswa'])) {
            return ['id_siswa' => 'Siswa bukan anggota aktif dari ekstrakurikuler pada jadwal ini.'];
        }

        if ($this->kehadiranModel->isDuplicate($payload['id_jadwal'], $payload['id_siswa'], $exceptId)) {
            return ['id_siswa' => 'Kehadiran siswa ini pada jadwal tersebut sudah tercatat.'];
        }

        return null;
    }

    private function rules(): array
    {
        return [
            'id_jadwal'  => 'required|is_natural_no_zero|is_not_unique[jadwal_ekskul.id_jadwal]',
            'id_siswa'   => 'required|is_natural_no_zero|is_not_unique[siswa.id_siswa]',
            'status'     => 'required|in_list[' . implode(',', array_keys(self::STATUS)) . ']',
            'keterangan' => 'permit_empty|max_length[255]',
        ];
    }

    private function messages(): array
    {
        return [
            'id_jadwal' => [
                'required'           => 'Jadwal wajib dipilih.',
                'is_natural_no_zero' => 'Jadwal tidak valid.',
                'is_not_unique'      => 'Jadwal yang dipilih tidak ditemukan.',
            ],
            'id_siswa' => [
                'required'           => 'Siswa wajib dipilih.',
                'is_natural_no_zero' => 'Siswa tidak valid.',
                'is_not_unique'      => 'Siswa yang dipilih tidak ditemukan.',
            ],
            'status' => [
                'required' => 'Status kehadiran wajib dipilih.',
                'in_list'  => 'Status kehadiran tidak valid.',
            ],
            'keterangan' => [
                'max_length' => 'Keterangan maksimal 255 karakter.',
            ],
        ];
    }

    private function payload(): array
    {
        return [
            'id_jadwal'  => (int) $this->request->getPost('id_jadwal'),
            'id_siswa'   => (int) $this->request->getPost('id_siswa'),
            'status'     => trim((string) $this->request->getPost('status')),
            'keterangan' => trim((string) $this->request->getPost('keterangan')) ?: null,
        ];
    }
}