<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$errors = session()->getFlashdata('errors') ?? [];
$isEdit = $siswa !== null;
?>

<!-- Judul halaman -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= esc($title) ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('data/siswa') ?>">Data Siswa</a></li>
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
                        <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                        <input type="text" id="nis" name="nis" maxlength="20"
                               class="form-control <?= isset($errors['nis']) ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('nis', $siswa['nis'] ?? '')) ?>" placeholder="Contoh: 2324001" required>
                        <?php if (isset($errors['nis'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['nis']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="nama_siswa" class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                        <input type="text" id="nama_siswa" name="nama_siswa" maxlength="120"
                               class="form-control <?= isset($errors['nama_siswa']) ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('nama_siswa', $siswa['nama_siswa'] ?? '')) ?>" placeholder="Nama lengkap siswa" required>
                        <?php if (isset($errors['nama_siswa'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['nama_siswa']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                        <input type="text" id="kelas" name="kelas" maxlength="30"
                               class="form-control <?= isset($errors['kelas']) ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('kelas', $siswa['kelas'] ?? '')) ?>" placeholder="Contoh: XII RPL 1" required>
                        <?php if (isset($errors['kelas'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['kelas']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Simpan
                        </button>
                        <a href="<?= site_url('data/siswa') ?>" class="btn btn-light">Batal</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>