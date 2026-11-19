<?php
$project = config('Project');
$uri     = uri_string();

// Ikon per menu (dripicons bawaan template)
$icons = [
    'siswa'              => 'dripicons-user-group',
    'pembina'            => 'dripicons-user',
    'ekstrakurikuler'    => 'dripicons-trophy',
    'pendaftaran_ekskul' => 'dripicons-document',
    'jadwal_ekskul'      => 'dripicons-calendar',
    'kehadiran_ekskul'   => 'dripicons-checklist',
];

$isActive = static fn (string $path): bool => $uri === $path || str_starts_with($uri, $path . '/');
?>
<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">

        <div class="user-sidebar text-center">
            <div class="user-img">
                <img src="<?= base_url('assets/images/users/avatar-7.jpg') ?>" alt="" class="rounded-circle">
                <span class="avatar-online bg-success"></span>
            </div>
            <div class="user-info">
                <h5 class="mt-3 font-size-16 text-white"><?= esc(session('name') ?? session('username')) ?></h5>
                <span class="font-size-13 text-white-50">Pembina</span>
            </div>
        </div>

        <!-- Sidemenu -->
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>

                <li class="<?= $isActive('dashboard') ? 'mm-active' : '' ?>">
                    <a href="<?= site_url('dashboard') ?>" class="waves-effect <?= $isActive('dashboard') ? 'active' : '' ?>">
                        <i class="dripicons-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-title">Data Master</li>

                <?php foreach ($project->resources as $table => $r): ?>
                    <?php $active = $isActive('data/' . $table); ?>
                    <li class="<?= $active ? 'mm-active' : '' ?>">
                        <a href="<?= site_url('data/' . $table) ?>" class="waves-effect <?= $active ? 'active' : '' ?>">
                            <i class="<?= $icons[$table] ?? 'dripicons-document' ?>"></i>
                            <span><?= esc($r['title']) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>

                <?php if ($project->stage >= 11): ?>
                    <li class="menu-title">Laporan</li>

                    <li class="<?= $isActive('rekap') ? 'mm-active' : '' ?>">
                        <a href="<?= site_url('rekap') ?>" class="waves-effect <?= $isActive('rekap') ? 'active' : '' ?>">
                            <i class="dripicons-graph-bar"></i>
                            <span><?= $project->stage >= 12 ? 'Laporan' : 'Detail & JOIN' ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <!-- Sidebar -->

    </div>
</div>
<!-- Left Sidebar End -->