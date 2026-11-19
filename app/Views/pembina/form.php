<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$errors = session()->getFlashdata('errors') ?? [];
$isEdit = $pembina !== null;
?>

<!-- Judul halaman -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= esc($title) ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('data/pembina') ?>">Data Pembina</a></li>
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
                        <label for="nama_pembina" class="form-label">Nama Pembina <span class="text-danger">*</span></label>
                        <input type="text" id="nama_pembina" name="nama_pembina" maxlength="120"
                               class="form-control <?= isset($errors['nama_pembina']) ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('nama_pembina', $pembina['nama_pembina'] ?? '')) ?>"
                               placeholder="Nama lengkap pembina" required>
                        <?php if (isset($errors['nama_pembina'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['nama_pembina']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No HP</label>
                        <input type="text" id="no_hp" name="no_hp" maxlength="20" inputmode="numeric"
                               class="form-control <?= isset($errors['no_hp']) ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('no_hp', $pembina['no_hp'] ?? '')) ?>"
                               placeholder="Contoh: 081234567890">
                        <?php if (isset($errors['no_hp'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['no_hp']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" id="username" name="username" maxlength="50" autocomplete="off"
                               class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('username', $pembina['username'] ?? '')) ?>"
                               placeholder="Huruf dan angka, minimal 4 karakter" required>
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['username']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password <?= $isEdit ? '' : '<span class="text-danger">*</span>' ?>
                        </label>
                        <input type="password" id="password" name="password" maxlength="255" autocomplete="new-password"
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                               placeholder="<?= $isEdit ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 6 karakter' ?>"
                               <?= $isEdit ? '' : 'required' ?>>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" id="konfirmasi_password" name="konfirmasi_password" maxlength="255"
                               autocomplete="new-password"
                               class="form-control <?= isset($errors['konfirmasi_password']) ? 'is-invalid' : '' ?>"
                               placeholder="Ulangi password">
                        <?php if (isset($errors['konfirmasi_password'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['konfirmasi_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Simpan
                        </button>
                        <a href="<?= site_url('data/pembina') ?>" class="btn btn-light">Batal</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>