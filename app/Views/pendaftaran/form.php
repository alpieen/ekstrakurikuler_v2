<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$errors = session()->getFlashdata('errors') ?? [];
$isEdit = $pendaftaran !== null;

$selectedSiswa  = (string) old('id_siswa', $pendaftaran['id_siswa'] ?? '');
$selectedEkskul = (string) old('id_ekskul', $pendaftaran['id_ekskul'] ?? '');
$selectedStatus = (string) old('status', $pendaftaran['status'] ?? 'Aktif');
$tanggalDaftar  = (string) old('tanggal_daftar', $pendaftaran['tanggal_daftar'] ?? $today);
?>

<!-- Judul halaman -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= esc($title) ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('data/pendaftaran_ekskul') ?>">Pendaftaran</a></li>
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
                        <label for="id_siswa" class="form-label">Siswa <span class="text-danger">*</span></label>
                        <select id="id_siswa" name="id_siswa"
                                class="form-select select2 <?= isset($errors['id_siswa']) ? 'is-invalid' : '' ?>"
                                data-placeholder="-- Pilih siswa --" required>
                            <option value=""></option>
                            <?php foreach ($siswa as $s): ?>
                                <option value="<?= $s['id_siswa'] ?>"
                                    <?= $selectedSiswa === (string) $s['id_siswa'] ? 'selected' : '' ?>>
                                    <?= esc($s['nama_siswa'] . ' (' . $s['nis'] . ') - ' . $s['kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['id_siswa'])): ?>
                            <div class="invalid-feedback d-block"><?= esc($errors['id_siswa']) ?></div>
                        <?php endif; ?>
                    </div>

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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_daftar" class="form-label">Tanggal Daftar <span class="text-danger">*</span></label>
                            <input type="date" id="tanggal_daftar" name="tanggal_daftar"
                                   class="form-control <?= isset($errors['tanggal_daftar']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc($tanggalDaftar) ?>" required>
                            <?php if (isset($errors['tanggal_daftar'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['tanggal_daftar']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select id="status" name="status"
                                    class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" required>
                                <?php foreach ($statusList as $st): ?>
                                    <option value="<?= esc($st, 'attr') ?>" <?= $selectedStatus === $st ? 'selected' : '' ?>>
                                        <?= esc($st) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['status']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Simpan
                        </button>
                        <a href="<?= site_url('data/pendaftaran_ekskul') ?>" class="btn btn-light">Batal</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>