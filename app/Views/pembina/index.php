<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$perPage = $pager->getPerPage();
$current = $pager->getCurrentPage();
$total   = $pager->getTotal();
$offset  = ($current - 1) * $perPage;

$loginUsername = session()->get('username');
$loginTable    = session()->get('auth_table');
?>

<!-- Judul halaman -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Data Pembina</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Pembina</li>
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
                        <form action="<?= site_url('data/pembina') ?>" method="get" class="d-flex gap-2">
                            <input type="text" name="q" class="form-control"
                                   value="<?= esc($keyword) ?>" placeholder="Cari nama, no HP, atau username...">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                            <?php if ($keyword !== ''): ?>
                                <a href="<?= site_url('data/pembina') ?>" class="btn btn-light">Reset</a>
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="<?= site_url('data/pembina/create') ?>" class="btn btn-success">
                            <i class="mdi mdi-plus me-1"></i> Tambah Pembina
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-hover table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Nama Pembina</th>
                                <th>No HP</th>
                                <th>Username</th>
                                <th class="text-end" style="width: 160px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pembina)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <?= $keyword !== '' ? 'Data pembina tidak ditemukan.' : 'Belum ada data pembina.' ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pembina as $i => $row): ?>
                                    <?php $isMe = $loginTable === 'pembina' && $loginUsername === $row['username']; ?>
                                    <tr>
                                        <td><?= $offset + $i + 1 ?></td>
                                        <td>
                                            <?= esc($row['nama_pembina']) ?>
                                            <?php if ($isMe): ?>
                                                <span class="badge bg-success ms-1">Anda</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($row['no_hp'] ?: '-') ?></td>
                                        <td><?= esc($row['username']) ?></td>
                                        <td class="text-end">
                                            <a href="<?= site_url('data/pembina/edit/' . $row['id_pembina']) ?>"
                                               class="btn btn-sm btn-warning">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </a>
                                            <?php if (! $isMe): ?>
                                                <form action="<?= site_url('data/pembina/delete/' . $row['id_pembina']) ?>"
                                                      method="post" class="d-inline js-delete"
                                                      data-nama="<?= esc($row['nama_pembina'], 'attr') ?>">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="mdi mdi-delete"></i> Hapus
                                                    </button>
                                                </form>
                                            <?php endif; ?>
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
                            Menampilkan <?= $offset + 1 ?>&ndash;<?= $offset + count($pembina) ?> dari <?= $total ?> data
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