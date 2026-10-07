<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <li>
                    <a href="<?= site_url('dashboard') ?>">
                        <span class="simple-icon">⌂</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <?php foreach(config('Project')->resources as $table => $r): ?>
                    <li>
                        <a href="<?= site_url('data/'.$table) ?>">
                            <span class="simple-icon">›</span>
                            <span><?= esc($r['title']) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                
                <?php if(config('Project')->stage >= 11): ?>
                    <li>
                        <a href="<?= site_url('rekap') ?>">
                            <span class="simple-icon">≡</span>
                            <span><?= config('Project')->stage >= 12 ? 'Laporan' : 'Detail & JOIN' ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>