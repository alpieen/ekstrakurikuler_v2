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
            <h4 class="mb-sm-0 font-size-18">Data Ekstrakurikuler</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Ekstrakurikuler</li>
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
                        <form action="<?= site_url('data/ekstrakurikuler') ?>" method="get" class="d-flex gap-2">
                            <input type="text" name="q" class="form-control"
                                   value="<?= esc($keyword) ?>" placeholder="Cari ekskul, pembina, atau hari...">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                            <?php if ($keyword !== ''): ?>
                                <a href="<?= site_url('data/ekstrakurikuler') ?>" class="btn btn-light">Reset</a>
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="<?= site_url('data/ekstrakurikuler/create') ?>" class="btn btn-success">
                            <i class="mdi mdi-plus me-1"></i> Tambah Ekstrakurikuler
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-hover table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Nama Ekstrakurikuler</th>
                                <th>Pembina</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th class="text-end" style="width: 160px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ekskul)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <?= $keyword !== '' ? 'Data ekstrakurikuler tidak ditemukan.' : 'Belum ada data ekstrakurikuler.' ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ekskul as $i => $row): ?>
                                    <tr>
                                        <td><?= $offset + $i + 1 ?></td>
                                        <td><?= esc($row['nama_ekskul']) ?></td>
                                        <td><?= esc($row['nama_pembina']) ?></td>
                                        <td><?= esc($row['hari'] ?: '-') ?></td>
                                        <td><?= ! empty($row['jam']) ? esc(substr($row['jam'], 0, 5)) : '-' ?></td>
                                        <td class="text-end">
                                            <a href="<?= site_url('data/ekstrakurikuler/edit/' . $row['id_ekskul']) ?>"
                                               class="btn btn-sm btn-warning">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </a>
                                            <form action="<?= site_url('data/ekstrakurikuler/delete/' . $row['id_ekskul']) ?>"
                                                  method="post" class="d-inline js-delete"
                                                  data-nama="<?= esc($row['nama_ekskul'], 'attr') ?>">
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
                            Menampilkan <?= $offset + 1 ?>&ndash;<?= $offset + count($ekskul) ?> dari <?= $total ?> data
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