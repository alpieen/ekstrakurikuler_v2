<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$perPage = $pager->getPerPage();
$current = $pager->getCurrentPage();
$total   = $pager->getTotal();
$offset  = ($current - 1) * $perPage;
?>

<!-- Judul halaman -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Data Siswa</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Siswa</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <form action="<?= site_url('data/siswa') ?>" method="get" class="d-flex gap-2">
                            <input type="text" name="q" class="form-control"
                                   value="<?= esc($keyword) ?>" placeholder="Cari NIS, nama, atau kelas...">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                            <?php if ($keyword !== ''): ?>
                                <a href="<?= site_url('data/siswa') ?>" class="btn btn-light">Reset</a>
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="<?= site_url('data/siswa/create') ?>" class="btn btn-success">
                            <i class="mdi mdi-plus me-1"></i> Tambah Siswa
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-hover table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th class="text-end" style="width: 160px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($siswa)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <?= $keyword !== '' ? 'Data siswa tidak ditemukan.' : 'Belum ada data siswa.' ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($siswa as $i => $row): ?>
                                    <tr>
                                        <td><?= $offset + $i + 1 ?></td>
                                        <td><?= esc($row['nis']) ?></td>
                                        <td><?= esc($row['nama_siswa']) ?></td>
                                        <td><?= esc($row['kelas']) ?></td>
                                        <td class="text-end">
                                            <a href="<?= site_url('data/siswa/edit/' . $row['id_siswa']) ?>"
                                               class="btn btn-sm btn-warning">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </a>
                                            <form action="<?= site_url('data/siswa/delete/' . $row['id_siswa']) ?>"
                                                  method="post" class="d-inline js-delete"
                                                  data-nama="<?= esc($row['nama_siswa'], 'attr') ?>">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="mdi mdi-delete"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total > 0): ?>
                    <div class="row align-items-center mt-3">
                        <div class="col-md-6 text-muted">
                            Menampilkan <?= $offset + 1 ?>&ndash;<?= $offset + count($siswa) ?> dari <?= $total ?> data
                        </div>
                        <div class="col-md-6">
                            <?= $pager->links('default', 'bootstrap_full') ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>