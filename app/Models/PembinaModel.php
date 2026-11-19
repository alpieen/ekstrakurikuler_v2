<?php

namespace App\Models;

use CodeIgniter\Model;

class PembinaModel extends Model
{
    protected $table         = 'pembina';
    protected $primaryKey    = 'id_pembina';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'nama_pembina',
        'no_hp',
        'username',
        'password',
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password otomatis sebelum disimpan.
     * - Jika password kosong saat update, field dibuang (password lama tetap).
     * - Jika sudah berupa hash, tidak di-hash ulang.
     */
    protected function hashPassword(array $data): array
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        $password = (string) $data['data']['password'];

        if ($password === '') {
            unset($data['data']['password']);

            return $data;
        }

        if (password_get_info($password)['algoName'] === 'unknown') {
            $data['data']['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        return $data;
    }

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Cari pembina berdasarkan nama, no HP, atau username.
     */
    public function search(?string $keyword = null): self
    {
        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('nama_pembina', $keyword)
                ->orLike('no_hp', $keyword)
                ->orLike('username', $keyword)
                ->groupEnd();
        }

        return $this->orderBy('nama_pembina', 'ASC');
    }

    /**
     * Cek apakah pembina masih membina ekstrakurikuler (FK).
     */
    public function hasRelasi(int $id): bool
    {
        return $this->db->table('ekstrakurikuler')
            ->where('id_pembina', $id)
            ->countAllResults() > 0;
    }
}