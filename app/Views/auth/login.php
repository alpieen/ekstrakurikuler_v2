<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login - <?= esc($title) ?></title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.min.css') ?>" rel="stylesheet">
</head>
<body class="bg-primary bg-pattern">
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        
                        <div class="bg-login text-center">
                            <div class="bg-login-overlay"></div>
                            <div class="position-relative">
                                <h5 class="text-white font-size-20"><?= esc($title) ?></h5>
                                <p class="text-white-50 mb-0">Mini Project Kelas XII RPL</p>
                            </div>
                        </div>
                        
                        <div class="card-body pt-5">
                            <div class="p-2">
                                
                                <?php if(session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger">
                                        <?= esc(session()->getFlashdata('error')) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if(session()->getFlashdata('success')): ?>
                                    <div class="alert alert-success">
                                        <?= esc(session()->getFlashdata('success')) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="post" action="<?= site_url('login') ?>">
                                    <?= csrf_field() ?>
                                    
                                    <div class="mb-3">
                                        <label>Username</label>
                                        <input class="form-control" name="username" value="<?= old('username') ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label>Password</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                    
                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary" type="submit">Login</button>
                                    </div>
                                </form>
                                
                                <div class="mt-4 text-center text-muted small">
                                    Akun contoh: admin / admin123
                                </div>
                                
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>