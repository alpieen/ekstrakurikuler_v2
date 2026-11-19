<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Siswa extends BaseController
{
    protected SiswaModel $siswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
    }

    /**
     * Daftar siswa + pencarian + pagination.
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('q'));

        $data = [
            'title'   => 'Data Siswa',
            'keyword' => $keyword,
            'siswa'   => $this->siswaModel->search($keyword)->paginate(10),
            'pager'   => $this->siswaModel->pager,
        ];

        return view('siswa/index', $data);
    }

    /**
     * Form tambah siswa.
     */
    public function create()
    {
        return view('siswa/form', [
            'title'  => 'Tambah Siswa',
            'siswa'  => null,
            'action' => site_url('data/siswa/store'),
        ]);
    }

    /**
     * Simpan siswa baru.
     */
    public function store()
    {
        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->siswaModel->insert($this->payload());

        return redirect()->to('data/siswa')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Form edit siswa.
     */
    public function edit(int $id)
    {
        return view('siswa/form', [
            'title'  => 'Edit Siswa',
            'siswa'  => $this->findOrFail($id),
            'action' => site_url('data/siswa/update/' . $id),
        ]);
    }

    /**
     * Simpan perubahan siswa.
     */
    public function update(int $id)
    {
        $this->findOrFail($id);

        if (! $this->validate($this->rules($id), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->siswaModel->update($id, $this->payload());

        return redirect()->to('data/siswa')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Hapus siswa (ditolak jika sudah punya pendaftaran/kehadiran).
     */
    public function delete(int $id)
    {
        $this->findOrFail($id);

        if ($this->siswaModel->hasRelasi($id)) {
            return redirect()->to('data/siswa')
                ->with('error', 'Siswa tidak bisa dihapus karena sudah memiliki data pendaftaran atau kehadiran.');
        }

        $this->siswaModel->delete($id);

        return redirect()->to('data/siswa')->with('success', 'Data siswa berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    private function findOrFail(int $id): array
    {
        $siswa = $this->siswaModel->find($id);

        if (! $siswa) {
            throw PageNotFoundException::forPageNotFound('Data siswa tidak ditemukan.');
        }

        return $siswa;
    }

    private function rules(?int $id = null): array
    {
        $nisUnique = $id === null
            ? 'is_unique[siswa.nis]'
            : "is_unique[siswa.nis,id_siswa,{$id}]";

        return [
            'nis'        => "required|max_length[20]|{$nisUnique}",
            'nama_siswa' => 'required|max_length[120]',
            'kelas'      => 'required|max_length[30]',
        ];
    }

    private function messages(): array
    {
        return [
            'nis' => [
                'required'   => 'NIS wajib diisi.',
                'max_length' => 'NIS maksimal 20 karakter.',
                'is_unique'  => 'NIS sudah terdaftar.',
            ],
            'nama_siswa' => [
                'required'   => 'Nama siswa wajib diisi.',
                'max_length' => 'Nama siswa maksimal 120 karakter.',
            ],
            'kelas' => [
                'required'   => 'Kelas wajib diisi.',
                'max_length' => 'Kelas maksimal 30 karakter.',
            ],
        ];
    }

    private function payload(): array
    {
        return [
            'nis'        => trim((string) $this->request->getPost('nis')),
            'nama_siswa' => trim((string) $this->request->getPost('nama_siswa')),
            'kelas'      => trim((string) $this->request->getPost('kelas')),
        ];
    }
}