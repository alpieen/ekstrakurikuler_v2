<?php

namespace App\Controllers;

use App\Models\EkstrakurikulerModel;
use App\Models\PendaftaranModel;
use App\Models\SiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Pendaftaran extends BaseController
{
    protected PendaftaranModel $pendaftaranModel;
    protected SiswaModel $siswaModel;
    protected EkstrakurikulerModel $ekskulModel;

    /** Pilihan status sesuai ENUM di database. */
    private const STATUS = ['Aktif', 'Tidak Aktif'];

    public function __construct()
    {
        $this->pendaftaranModel = new PendaftaranModel();
        $this->siswaModel       = new SiswaModel();
        $this->ekskulModel      = new EkstrakurikulerModel();
    }

    /**
     * Daftar pendaftaran + pencarian + filter status + pagination.
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $status  = trim((string) $this->request->getGet('status'));

        if (! in_array($status, self::STATUS, true)) {
            $status = '';
        }

        $data = [
            'title'       => 'Pendaftaran Ekstrakurikuler',
            'keyword'     => $keyword,
            'status'      => $status,
            'statusList'  => self::STATUS,
            'pendaftaran' => $this->pendaftaranModel->search($keyword, $status)->paginate(10),
            'pager'       => $this->pendaftaranModel->pager,
        ];

        return view('pendaftaran/index', $data);
    }

    /**
     * Form tambah pendaftaran.
     */
    public function create()
    {
        return view('pendaftaran/form', $this->formData(
            'Tambah Pendaftaran',
            null,
            site_url('data/pendaftaran_ekskul/store')
        ));
    }

    /**
     * Simpan pendaftaran baru.
     */
    public function store()
    {
        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = $this->payload();

        if ($this->pendaftaranModel->isDuplicate($payload['id_siswa'], $payload['id_ekskul'])) {
            return redirect()->back()->withInput()->with('errors', [
                'id_siswa' => 'Siswa ini sudah terdaftar di ekstrakurikuler tersebut. Ubah statusnya jika ingin mengaktifkan kembali.',
            ]);
        }

        $this->pendaftaranModel->insert($payload);

        return redirect()->to('data/pendaftaran_ekskul')->with('success', 'Data pendaftaran berhasil ditambahkan.');
    }

    /**
     * Form edit pendaftaran.
     */
    public function edit(int $id)
    {
        return view('pendaftaran/form', $this->formData(
            'Edit Pendaftaran',
            $this->findOrFail($id),
            site_url('data/pendaftaran_ekskul/update/' . $id)
        ));
    }

    /**
     * Simpan perubahan pendaftaran.
     */
    public function update(int $id)
    {
        $this->findOrFail($id);

        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = $this->payload();

        if ($this->pendaftaranModel->isDuplicate($payload['id_siswa'], $payload['id_ekskul'], $id)) {
            return redirect()->back()->withInput()->with('errors', [
                'id_siswa' => 'Siswa ini sudah terdaftar di ekstrakurikuler tersebut.',
            ]);
        }

        $this->pendaftaranModel->update($id, $payload);

        return redirect()->to('data/pendaftaran_ekskul')->with('success', 'Data pendaftaran berhasil diperbarui.');
    }

    /**
     * Hapus pendaftaran.
     * Tidak ada tabel lain yang mengacu ke pendaftaran, jadi aman dihapus.
     */
    public function delete(int $id)
    {
        $this->findOrFail($id);

        $this->pendaftaranModel->delete($id);

        return redirect()->to('data/pendaftaran_ekskul')->with('success', 'Data pendaftaran berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    private function findOrFail(int $id): array
    {
        $pendaftaran = $this->pendaftaranModel->find($id);

        if (! $pendaftaran) {
            throw PageNotFoundException::forPageNotFound('Data pendaftaran tidak ditemukan.');
        }

        return $pendaftaran;
    }

    private function formData(string $title, ?array $pendaftaran, string $action): array
    {
        return [
            'title'       => $title,
            'pendaftaran' => $pendaftaran,
            'action'      => $action,
            'siswa'       => $this->siswaModel->orderBy('nama_siswa', 'ASC')->findAll(),
            'ekskul'      => $this->ekskulModel->orderBy('nama_ekskul', 'ASC')->findAll(),
            'statusList'  => self::STATUS,
            'today'       => date('Y-m-d'),
        ];
    }

    private function rules(): array
    {
        return [
            'id_siswa'       => 'required|is_natural_no_zero|is_not_unique[siswa.id_siswa]',
            'id_ekskul'      => 'required|is_natural_no_zero|is_not_unique[ekstrakurikuler.id_ekskul]',
            'tanggal_daftar' => 'required|valid_date[Y-m-d]',
            'status'         => 'required|in_list[' . implode(',', self::STATUS) . ']',
        ];
    }

    private function messages(): array
    {
        return [
            'id_siswa' => [
                'required'           => 'Siswa wajib dipilih.',
                'is_natural_no_zero' => 'Siswa tidak valid.',
                'is_not_unique'      => 'Siswa yang dipilih tidak ditemukan.',
            ],
            'id_ekskul' => [
                'required'           => 'Ekstrakurikuler wajib dipilih.',
                'is_natural_no_zero' => 'Ekstrakurikuler tidak valid.',
                'is_not_unique'      => 'Ekstrakurikuler yang dipilih tidak ditemukan.',
            ],
            'tanggal_daftar' => [
                'required'   => 'Tanggal daftar wajib diisi.',
                'valid_date' => 'Format tanggal tidak valid.',
            ],
            'status' => [
                'required' => 'Status wajib dipilih.',
                'in_list'  => 'Status tidak valid.',
            ],
        ];
    }

    private function payload(): array
    {
        return [
            'id_siswa'       => (int) $this->request->getPost('id_siswa'),
            'id_ekskul'      => (int) $this->request->getPost('id_ekskul'),
            'tanggal_daftar' => trim((string) $this->request->getPost('tanggal_daftar')),
            'status'         => trim((string) $this->request->getPost('status')),
        ];
    }
}