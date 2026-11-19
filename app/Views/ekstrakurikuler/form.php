<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$errors = session()->getFlashdata('errors') ?? [];
$isEdit = $ekskul !== null;

$selectedPembina = (string) old('id_pembina', $ekskul['id_pembina'] ?? '');
$selectedHari    = (string) old('hari', $ekskul['hari'] ?? '');
?>

<!-- Judul halaman -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= esc($title) ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('data/ekstrakurikuler') ?>">Ekstrakurikuler</a></li>
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
                        <label for="nama_ekskul" class="form-label">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <input type="text" id="nama_ekskul" name="nama_ekskul" maxlength="120"
                               class="form-control <?= isset($errors['nama_ekskul']) ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('nama_ekskul', $ekskul['nama_ekskul'] ?? '')) ?>"
                               placeholder="Contoh: Pramuka" required>
                        <?php if (isset($errors['nama_ekskul'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['nama_ekskul']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="id_pembina" class="form-label">Pembina <span class="text-danger">*</span></label>
                        <select id="id_pembina" name="id_pembina"
                                class="form-select select2 <?= isset($errors['id_pembina']) ? 'is-invalid' : '' ?>"
                                data-placeholder="-- Pilih pembina --" required>
                            <option value=""></option>
                            <?php foreach ($pembina as $p): ?>
                                <option value="<?= $p['id_pembina'] ?>"
                                    <?= $selectedPembina === (string) $p['id_pembina'] ? 'selected' : '' ?>>
                                    <?= esc($p['nama_pembina']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['id_pembina'])): ?>
                            <div class="invalid-feedback d-block"><?= esc($errors['id_pembina']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="hari" class="form-label">Hari</label>
                            <select id="hari" name="hari"
                                    class="form-select <?= isset($errors['hari']) ? 'is-invalid' : '' ?>">
                                <option value="">-- Pilih hari --</option>
                                <?php foreach ($hari as $h): ?>
                                    <option value="<?= esc($h, 'attr') ?>" <?= $selectedHari === $h ? 'selected' : '' ?>>
                                        <?= esc($h) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['hari'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['hari']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jam" class="form-label">Jam</label>
                            <input type="time" id="jam" name="jam"
                                   class="form-control <?= isset($errors['jam']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc(old('jam', $ekskul['jam'] ?? '')) ?>">
                            <?php if (isset($errors['jam'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['jam']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Simpan
                        </button>
                        <a href="<?= site_url('data/ekstrakurikuler') ?>" class="btn btn-light">Batal</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>