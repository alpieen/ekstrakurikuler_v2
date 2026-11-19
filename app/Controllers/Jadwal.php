<?php

namespace App\Controllers;

use App\Models\EkstrakurikulerModel;
use App\Models\JadwalModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Jadwal extends BaseController
{
    protected JadwalModel $jadwalModel;
    protected EkstrakurikulerModel $ekskulModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
        $this->ekskulModel = new EkstrakurikulerModel();
    }

    /**
     * Daftar jadwal + pencarian + filter ekskul + pagination.
     */
    public function index()
    {
        $keyword  = trim((string) $this->request->getGet('q'));
        $idEkskul = (int) $this->request->getGet('ekskul');

        $data = [
            'title'     => 'Jadwal Ekstrakurikuler',
            'keyword'   => $keyword,
            'idEkskul'  => $idEkskul,
            'ekskul'    => $this->ekskulModel->orderBy('nama_ekskul', 'ASC')->findAll(),
            'jadwal'    => $this->jadwalModel->search($keyword, $idEkskul)->paginate(10),
            'pager'     => $this->jadwalModel->pager,
        ];

        return view('jadwal/index', $data);
    }

    /**
     * Form tambah jadwal.
     */
    public function create()
    {
        return view('jadwal/form', $this->formData(
            'Tambah Jadwal',
            null,
            site_url('data/jadwal_ekskul/store')
        ));
    }

    /**
     * Simpan jadwal baru.
     */
    public function store()
    {
        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->jadwalModel->insert($this->payload());

        return redirect()->to('data/jadwal_ekskul')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    /**
     * Form edit jadwal.
     */
    public function edit(int $id)
    {
        return view('jadwal/form', $this->formData(
            'Edit Jadwal',
            $this->findOrFail($id),
            site_url('data/jadwal_ekskul/update/' . $id)
        ));
    }

    /**
     * Simpan perubahan jadwal.
     */
    public function update(int $id)
    {
        $this->findOrFail($id);

        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->jadwalModel->update($id, $this->payload());

        return redirect()->to('data/jadwal_ekskul')->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Hapus jadwal (ditolak jika sudah ada data kehadiran).
     */
    public function delete(int $id)
    {
        $this->findOrFail($id);

        if ($this->jadwalModel->hasRelasi($id)) {
            return redirect()->to('data/jadwal_ekskul')
                ->with('error', 'Jadwal tidak bisa dihapus karena sudah memiliki data kehadiran.');
        }

        $this->jadwalModel->delete($id);

        return redirect()->to('data/jadwal_ekskul')->with('success', 'Jadwal berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    private function findOrFail(int $id): array
    {
        $jadwal = $this->jadwalModel->find($id);

        if (! $jadwal) {
            throw PageNotFoundException::forPageNotFound('Data jadwal tidak ditemukan.');
        }

        return $jadwal;
    }

    private function formData(string $title, ?array $jadwal, string $action): array
    {
        return [
            'title'  => $title,
            'jadwal' => $jadwal,
            'action' => $action,
            'ekskul' => $this->ekskulModel->orderBy('nama_ekskul', 'ASC')->findAll(),
            'today'  => date('Y-m-d'),
        ];
    }

    private function rules(): array
    {
        return [
            'id_ekskul' => 'required|is_natural_no_zero|is_not_unique[ekstrakurikuler.id_ekskul]',
            'tanggal'   => 'required|valid_date[Y-m-d]',
            'materi'    => 'permit_empty|max_length[150]',
            'tempat'    => 'permit_empty|max_length[100]',
        ];
    }

    private function messages(): array
    {
        return [
            'id_ekskul' => [
                'required'           => 'Ekstrakurikuler wajib dipilih.',
                'is_natural_no_zero' => 'Ekstrakurikuler tidak valid.',
                'is_not_unique'      => 'Ekstrakurikuler yang dipilih tidak ditemukan.',
            ],
            'tanggal' => [
                'required'   => 'Tanggal wajib diisi.',
                'valid_date' => 'Format tanggal tidak valid.',
            ],
            'materi' => [
                'max_length' => 'Materi maksimal 150 karakter.',
            ],
            'tempat' => [
                'max_length' => 'Tempat maksimal 100 karakter.',
            ],
        ];
    }

    private function payload(): array
    {
        return [
            'id_ekskul' => (int) $this->request->getPost('id_ekskul'),
            'tanggal'   => trim((string) $this->request->getPost('tanggal')),
            'materi'    => trim((string) $this->request->getPost('materi')) ?: null,
            'tempat'    => trim((string) $this->request->getPost('tempat')) ?: null,
        ];
    }
}