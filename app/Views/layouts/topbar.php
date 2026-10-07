<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <div class="navbar-brand-box">
                <a href="<?= site_url('dashboard') ?>" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="<?= base_url('assets/images/logo-sm.png') ?>" height="26">
                    </span>
                    <span class="logo-lg logo-text text-white"><?= esc(config('Project')->title) ?></span>
                </a>
            </div>
            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">☰</button>
        </div>
        
        <div class="d-flex align-items-center pe-3">
            <span class="me-3 d-none d-md-inline"><?= esc(session('name') ?? session('username')) ?></span>
            <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</header>