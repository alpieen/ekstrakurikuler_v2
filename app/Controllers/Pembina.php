<?php

namespace App\Controllers;

use App\Models\PembinaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Pembina extends BaseController
{
    protected PembinaModel $pembinaModel;

    public function __construct()
    {
        $this->pembinaModel = new PembinaModel();
    }

    /**
     * Daftar pembina + pencarian + pagination.
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('q'));

        $data = [
            'title'   => 'Data Pembina',
            'keyword' => $keyword,
            'pembina' => $this->pembinaModel->search($keyword)->paginate(10),
            'pager'   => $this->pembinaModel->pager,
        ];

        return view('pembina/index', $data);
    }

    /**
     * Form tambah pembina.
     */
    public function create()
    {
        return view('pembina/form', [
            'title'   => 'Tambah Pembina',
            'pembina' => null,
            'action'  => site_url('data/pembina/store'),
        ]);
    }

    /**
     * Simpan pembina baru (password di-hash oleh model).
     */
    public function store()
    {
        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->pembinaModel->insert($this->payload(true));

        return redirect()->to('data/pembina')->with('success', 'Data pembina berhasil ditambahkan.');
    }

    /**
     * Form edit pembina.
     */
    public function edit(int $id)
    {
        return view('pembina/form', [
            'title'   => 'Edit Pembina',
            'pembina' => $this->findOrFail($id),
            'action'  => site_url('data/pembina/update/' . $id),
        ]);
    }

    /**
     * Simpan perubahan pembina.
     * Password kosong = password lama tidak diubah.
     */
    public function update(int $id)
    {
        $pembina = $this->findOrFail($id);

        if (! $this->validate($this->rules($id), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = $this->payload(false);

        $this->pembinaModel->update($id, $payload);

        // Jika pembina yang diedit adalah akun yang sedang login, sinkronkan session.
        if ($this->isCurrentUser($pembina)) {
            session()->set([
                'username' => $payload['username'],
                'name'     => $payload['nama_pembina'],
            ]);
        }

        return redirect()->to('data/pembina')->with('success', 'Data pembina berhasil diperbarui.');
    }

    /**
     * Hapus pembina.
     * Ditolak jika akun sendiri atau masih membina ekstrakurikuler.
     */
    public function delete(int $id)
    {
        $pembina = $this->findOrFail($id);

        if ($this->isCurrentUser($pembina)) {
            return redirect()->to('data/pembina')
                ->with('error', 'Akun yang sedang digunakan tidak bisa dihapus.');
        }

        if ($this->pembinaModel->hasRelasi($id)) {
            return redirect()->to('data/pembina')
                ->with('error', 'Pembina tidak bisa dihapus karena masih membina ekstrakurikuler.');
        }

        $this->pembinaModel->delete($id);

        return redirect()->to('data/pembina')->with('success', 'Data pembina berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    private function findOrFail(int $id): array
    {
        $pembina = $this->pembinaModel->find($id);

        if (! $pembina) {
            throw PageNotFoundException::forPageNotFound('Data pembina tidak ditemukan.');
        }

        return $pembina;
    }

    private function isCurrentUser(array $pembina): bool
    {
        return session()->get('auth_table') === 'pembina'
            && session()->get('username') === $pembina['username'];
    }

    private function rules(?int $id = null): array
    {
        $usernameUnique = $id === null
            ? 'is_unique[pembina.username]'
            : "is_unique[pembina.username,id_pembina,{$id}]";

        // Saat tambah password wajib, saat edit boleh kosong.
        $passwordRule = $id === null
            ? 'required|min_length[6]|max_length[255]'
            : 'permit_empty|min_length[6]|max_length[255]';

        return [
            'nama_pembina'        => 'required|max_length[120]',
            'no_hp'               => 'permit_empty|numeric|max_length[20]',
            'username'            => "required|alpha_numeric|min_length[4]|max_length[50]|{$usernameUnique}",
            'password'            => $passwordRule,
            'konfirmasi_password' => 'matches[password]',
        ];
    }

    private function messages(): array
    {
        return [
            'nama_pembina' => [
                'required'   => 'Nama pembina wajib diisi.',
                'max_length' => 'Nama pembina maksimal 120 karakter.',
            ],
            'no_hp' => [
                'numeric'    => 'No HP hanya boleh berisi angka.',
                'max_length' => 'No HP maksimal 20 digit.',
            ],
            'username' => [
                'required'      => 'Username wajib diisi.',
                'alpha_numeric' => 'Username hanya boleh huruf dan angka.',
                'min_length'    => 'Username minimal 4 karakter.',
                'max_length'    => 'Username maksimal 50 karakter.',
                'is_unique'     => 'Username sudah digunakan.',
            ],
            'password' => [
                'required'   => 'Password wajib diisi.',
                'min_length' => 'Password minimal 6 karakter.',
            ],
            'konfirmasi_password' => [
                'matches' => 'Konfirmasi password tidak sama.',
            ],
        ];
    }

    /**
     * Data yang akan disimpan.
     * Pada edit, password kosong tidak disertakan.
     */
    private function payload(bool $withPassword): array
    {
        $data = [
            'nama_pembina' => trim((string) $this->request->getPost('nama_pembina')),
            'no_hp'        => trim((string) $this->request->getPost('no_hp')) ?: null,
            'username'     => trim((string) $this->request->getPost('username')),
        ];

        $password = (string) $this->request->getPost('password');

        if ($withPassword || $password !== '') {
            $data['password'] = $password;
        }

        return $data;
    }
}