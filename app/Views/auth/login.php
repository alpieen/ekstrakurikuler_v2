<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | <?= esc(config('Project')->title) ?></title>

    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.ico') ?>">

    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" id="bootstrap-style" rel="stylesheet" type="text/css">
    <link href="<?= base_url('assets/css/icons.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="<?= base_url('assets/css/app.min.css') ?>" id="app-style" rel="stylesheet" type="text/css">
</head>

<body class="authentication-bg bg-primary">
    <div class="home-center">
        <div class="home-desc-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">

                        <div class="card">
                            <div class="card-body">
                                <div class="px-2 py-3">

                                    <div class="text-center">
                                        <img src="<?= base_url('assets/images/logo-dark.png') ?>" height="22" alt="Logo">

                                        <h5 class="text-primary mb-2 mt-4">Selamat Datang</h5>
                                        <p class="text-muted">Masuk untuk melanjutkan ke <?= esc(config('Project')->title) ?>.</p>
                                    </div>

                                    <?php if (session()->getFlashdata('error')): ?>
                                        <div class="alert alert-danger mt-3 mb-0" role="alert">
                                            <?= esc(session()->getFlashdata('error')) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (session()->getFlashdata('success')): ?>
                                        <div class="alert alert-success mt-3 mb-0" role="alert">
                                            <?= esc(session()->getFlashdata('success')) ?>
                                        </div>
                                    <?php endif; ?>

                                    <form class="form-horizontal mt-4 pt-2" action="<?= site_url('login') ?>" method="post">
                                        <?= csrf_field() ?>

                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                value="<?= esc(old('username')) ?>" placeholder="Masukkan username"
                                                autocomplete="username" required autofocus>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="Masukkan password" autocomplete="current-password" required>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">
                                                Masuk
                                            </button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>

                        <div class="mt-5 text-center text-white">
                            <p>&copy; <script>document.write(new Date().getFullYear())</script> <?= esc(config('Project')->title) ?></p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/node-waves/waves.min.js') ?>"></script>
</body>

</html>