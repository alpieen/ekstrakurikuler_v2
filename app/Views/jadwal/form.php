<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$errors = session()->getFlashdata('errors') ?? [];
$isEdit = $jadwal !== null;

$selectedEkskul = (string) old('id_ekskul', $jadwal['id_ekskul'] ?? '');
$tanggal        = (string) old('tanggal', $jadwal['tanggal'] ?? $today);
?>

<!-- Judul halaman -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= esc($title) ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('data/jadwal_ekskul') ?>">Jadwal</a></li>
                    <li class="breadcrumb-item active"><?= $isEdit ? 'Edit' : 'Tambah' ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card">
            <div class="card-body">

                <form action="<?= esc($action, 'attr') ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="id_ekskul" class="form-label">Ekstrakurikuler <span class="text-danger">*</span></label>
                        <select id="id_ekskul" name="id_ekskul"
                                class="form-select select2 <?= isset($errors['id_ekskul']) ? 'is-invalid' : '' ?>"
                                data-placeholder="-- Pilih ekstrakurikuler --" required>
                            <option value=""></option>
                            <?php foreach ($ekskul as $e): ?>
                                <option value="<?= $e['id_ekskul'] ?>"
                                    <?= $selectedEkskul === (string) $e['id_ekskul'] ? 'selected' : '' ?>>
                                    <?= esc($e['nama_ekskul']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['id_ekskul'])): ?>
                            <div class="invalid-feedback d-block"><?= esc($errors['id_ekskul']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" id="tanggal" name="tanggal"
                               class="form-control <?= isset($errors['tanggal']) ? 'is-invalid' : '' ?>"
                               value="<?= esc($tanggal) ?>" required>
                        <?php if (isset($errors['tanggal'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['tanggal']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="materi" class="form-label">Materi</label>
                        <input type="text" id="materi" name="materi" maxlength="150"
                               class="form-control <?= isset($errors['materi']) ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('materi', $jadwal['materi'] ?? '')) ?>"
                               placeholder="Contoh: Latihan baris-berbaris">
                        <?php if (isset($errors['materi'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['materi']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="tempat" class="form-label">Tempat</label>
                        <input type="text" id="tempat" name="tempat" maxlength="100"
                               class="form-control <?= isset($errors['tempat']) ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('tempat', $jadwal['tempat'] ?? '')) ?>"
                               placeholder="Contoh: Lapangan sekolah">
                        <?php if (isset($errors['tempat'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['tempat']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Simpan
                        </button>
                        <a href="<?= site_url('data/jadwal_ekskul') ?>" class="btn btn-light">Batal</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>