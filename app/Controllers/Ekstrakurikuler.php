<?php

namespace App\Controllers;

use App\Models\EkstrakurikulerModel;
use App\Models\PembinaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Ekstrakurikuler extends BaseController
{
    protected EkstrakurikulerModel $ekskulModel;
    protected PembinaModel $pembinaModel;

    /** Pilihan hari untuk dropdown & validasi. */
    private const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function __construct()
    {
        $this->ekskulModel  = new EkstrakurikulerModel();
        $this->pembinaModel = new PembinaModel();
    }

    /**
     * Daftar ekskul + pencarian + pagination.
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('q'));

        $data = [
            'title'   => 'Data Ekstrakurikuler',
            'keyword' => $keyword,
            'ekskul'  => $this->ekskulModel->search($keyword)->paginate(10),
            'pager'   => $this->ekskulModel->pager,
        ];

        return view('ekstrakurikuler/index', $data);
    }

    /**
     * Form tambah ekskul.
     */
    public function create()
    {
        return view('ekstrakurikuler/form', $this->formData('Tambah Ekstrakurikuler', null, site_url('data/ekstrakurikuler/store')));
    }

    /**
     * Simpan ekskul baru.
     */
    public function store()
    {
        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->ekskulModel->insert($this->payload());

        return redirect()->to('data/ekstrakurikuler')->with('success', 'Data ekstrakurikuler berhasil ditambahkan.');
    }

    /**
     * Form edit ekskul.
     */
    public function edit(int $id)
    {
        $ekskul = $this->findOrFail($id);

        // Format jam untuk input type="time" (HH:MM).
        if (! empty($ekskul['jam'])) {
            $ekskul['jam'] = substr($ekskul['jam'], 0, 5);
        }

        return view('ekstrakurikuler/form', $this->formData('Edit Ekstrakurikuler', $ekskul, site_url('data/ekstrakurikuler/update/' . $id)));
    }

    /**
     * Simpan perubahan ekskul.
     */
    public function update(int $id)
    {
        $this->findOrFail($id);

        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->ekskulModel->update($id, $this->payload());

        return redirect()->to('data/ekstrakurikuler')->with('success', 'Data ekstrakurikuler berhasil diperbarui.');
    }

    /**
     * Hapus ekskul (ditolak jika sudah punya pendaftaran/jadwal).
     */
    public function delete(int $id)
    {
        $this->findOrFail($id);

        if ($this->ekskulModel->hasRelasi($id)) {
            return redirect()->to('data/ekstrakurikuler')
                ->with('error', 'Ekstrakurikuler tidak bisa dihapus karena sudah memiliki data pendaftaran atau jadwal.');
        }

        $this->ekskulModel->delete($id);

        return redirect()->to('data/ekstrakurikuler')->with('success', 'Data ekstrakurikuler berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    private function findOrFail(int $id): array
    {
        $ekskul = $this->ekskulModel->find($id);

        if (! $ekskul) {
            throw PageNotFoundException::forPageNotFound('Data ekstrakurikuler tidak ditemukan.');
        }

        return $ekskul;
    }

    private function formData(string $title, ?array $ekskul, string $action): array
    {
        return [
            'title'   => $title,
            'ekskul'  => $ekskul,
            'action'  => $action,
            'pembina' => $this->pembinaModel->orderBy('nama_pembina', 'ASC')->findAll(),
            'hari'    => self::HARI,
        ];
    }

    private function rules(): array
    {
        return [
            'nama_ekskul' => 'required|max_length[120]',
            'id_pembina'  => 'required|is_natural_no_zero|is_not_unique[pembina.id_pembina]',
            'hari'        => 'permit_empty|in_list[' . implode(',', self::HARI) . ']',
            'jam'         => 'permit_empty|regex_match[/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/]',
        ];
    }

    private function messages(): array
    {
        return [
            'nama_ekskul' => [
                'required'   => 'Nama ekstrakurikuler wajib diisi.',
                'max_length' => 'Nama ekstrakurikuler maksimal 120 karakter.',
            ],
            'id_pembina' => [
                'required'          => 'Pembina wajib dipilih.',
                'is_natural_no_zero' => 'Pembina tidak valid.',
                'is_not_unique'     => 'Pembina yang dipilih tidak ditemukan.',
            ],
            'hari' => [
                'in_list' => 'Hari tidak valid.',
            ],
            'jam' => [
                'regex_match' => 'Format jam tidak valid (contoh: 15:30).',
            ],
        ];
    }

    private function payload(): array
    {
        $jam = trim((string) $this->request->getPost('jam'));

        return [
            'nama_ekskul' => trim((string) $this->request->getPost('nama_ekskul')),
            'id_pembina'  => (int) $this->request->getPost('id_pembina'),
            'hari'        => trim((string) $this->request->getPost('hari')) ?: null,
            'jam'         => $jam !== '' ? $jam : null,
        ];
    }
}