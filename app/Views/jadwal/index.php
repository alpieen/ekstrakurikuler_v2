<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$perPage = $pager->getPerPage();
$current = $pager->getCurrentPage();
$total   = $pager->getTotal();
$offset  = ($current - 1) * $perPage;
$hariIni = date('Y-m-d');
$filtered = $keyword !== '' || $idEkskul > 0;
?>

<!-- Judul halaman -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Jadwal Ekstrakurikuler</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Jadwal</li>
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
                    <div class="col-lg-8">
                        <form action="<?= site_url('data/jadwal_ekskul') ?>" method="get" class="row g-2">
                            <div class="col-md-5">
                                <input type="text" name="q" class="form-control"
                                       value="<?= esc($keyword) ?>" placeholder="Cari materi, tempat, atau ekskul...">
                            </div>
                            <div class="col-md-3">
                                <select name="ekskul" class="form-select">
                                    <option value="">Semua ekskul</option>
                                    <?php foreach ($ekskul as $e): ?>
                                        <option value="<?= $e['id_ekskul'] ?>" <?= $idEkskul === (int) $e['id_ekskul'] ? 'selected' : '' ?>>
                                            <?= esc($e['nama_ekskul']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-magnify"></i> Cari
                                </button>
                                <?php if ($filtered): ?>
                                    <a href="<?= site_url('data/jadwal_ekskul') ?>" class="btn btn-light">Reset</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="<?= site_url('data/jadwal_ekskul/create') ?>" class="btn btn-success">
                            <i class="mdi mdi-plus me-1"></i> Tambah Jadwal
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-hover table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Tanggal</th>
                                <th>Ekstrakurikuler</th>
                                <th>Materi</th>
                                <th>Tempat</th>
                                <th class="text-center">Kehadiran</th>
                                <th class="text-end" style="width: 300px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($jadwal)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <?= $filtered ? 'Data jadwal tidak ditemukan.' : 'Belum ada data jadwal.' ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($jadwal as $i => $row): ?>
                                    <?php $adaKehadiran = (int) $row['jumlah_kehadiran'] > 0; ?>
                                    <tr>
                                        <td><?= $offset + $i + 1 ?></td>
                                        <td>
                                            <?= esc(date('d-m-Y', strtotime($row['tanggal']))) ?>
                                            <?php if ($row['tanggal'] === $hariIni): ?>
                                                <span class="badge bg-success ms-1">Hari ini</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($row['nama_ekskul']) ?></td>
                                        <td><?= esc($row['materi'] ?: '-') ?></td>
                                        <td><?= esc($row['tempat'] ?: '-') ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $adaKehadiran ? 'bg-primary' : 'bg-secondary' ?>">
                                                <?= (int) $row['jumlah_kehadiran'] ?> tercatat
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= site_url('data/kehadiran_ekskul/input/' . $row['id_jadwal']) ?>"
                                               class="btn btn-sm btn-primary">
                                                <i class="mdi mdi-clipboard-check-outline"></i> Kehadiran
                                            </a>
                                            <a href="<?= site_url('data/jadwal_ekskul/edit/' . $row['id_jadwal']) ?>"
                                               class="btn btn-sm btn-warning">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </a>
                                            <?php if ($adaKehadiran): ?>
                                                <button type="button" class="btn btn-sm btn-danger" disabled
                                                        title="Tidak bisa dihapus karena sudah memiliki data kehadiran">
                                                    <i class="mdi mdi-delete"></i> Hapus
                                                </button>
                                            <?php else: ?>
                                                <form action="<?= site_url('data/jadwal_ekskul/delete/' . $row['id_jadwal']) ?>"
                                                      method="post" class="d-inline js-delete"
                                                      data-nama="<?= esc($row['nama_ekskul'] . ' - ' . date('d-m-Y', strtotime($row['tanggal'])), 'attr') ?>">
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
                            Menampilkan <?= $offset + 1 ?>&ndash;<?= $offset + count($jadwal) ?> dari <?= $total ?> data
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