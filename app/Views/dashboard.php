<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$cards = [
    ['label' => 'Total Siswa',        'value' => $ringkasan['total_siswa'],       'icon' => 'mdi-account-group',   'color' => 'primary', 'url' => 'data/siswa'],
    ['label' => 'Total Pembina',      'value' => $ringkasan['total_pembina'],     'icon' => 'mdi-account-tie',     'color' => 'info',    'url' => 'data/pembina'],
    ['label' => 'Ekstrakurikuler',    'value' => $ringkasan['total_ekskul'],      'icon' => 'mdi-soccer',          'color' => 'success', 'url' => 'data/ekstrakurikuler'],
    ['label' => 'Pendaftaran Aktif',  'value' => $ringkasan['pendaftaran_aktif'], 'icon' => 'mdi-clipboard-check', 'color' => 'warning', 'url' => 'data/pendaftaran_ekskul'],
    ['label' => 'Total Jadwal',       'value' => $ringkasan['total_jadwal'],      'icon' => 'mdi-calendar-clock',  'color' => 'danger',  'url' => 'data/jadwal_ekskul'],
];

$labelKehadiran = ['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa'];
$totalKehadiran = array_sum($kehadiran);
$adaAnggota     = array_sum(array_column($ekskul, 'jumlah_anggota')) > 0;
?>

<!-- Judul halaman -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Kartu ringkasan -->
<div class="row">
    <?php foreach ($cards as $card): ?>
        <div class="col-xl col-md-4 col-sm-6">
            <a href="<?= site_url($card['url']) ?>" class="text-reset">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2"><?= esc($card['label']) ?></p>
                                <h4 class="mb-0"><?= number_format((int) $card['value'], 0, ',', '.') ?></h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-<?= $card['color'] ?>">
                                    <span class="avatar-title">
                                        <i class="mdi <?= $card['icon'] ?> font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row">
    <!-- Anggota aktif per ekskul -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Anggota Aktif per Ekstrakurikuler</h4>

                <?php if (! $adaAnggota): ?>
                    <p class="text-muted text-center my-5">Belum ada anggota aktif.</p>
                <?php else: ?>
                    <div id="chart-anggota"></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Ringkasan kehadiran -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Ringkasan Kehadiran</h4>

                <?php if ($totalKehadiran === 0): ?>
                    <p class="text-muted text-center my-5">Belum ada data kehadiran.</p>
                <?php else: ?>
                    <div id="chart-kehadiran"></div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php foreach ($labelKehadiran as $kode => $label): ?>
                                    <tr>
                                        <td><?= esc($label) ?></td>
                                        <td class="text-end"><?= number_format((int) $kehadiran[$kode], 0, ',', '.') ?></td>
                                        <td class="text-end text-muted" style="width: 70px;">
                                            <?= round($kehadiran[$kode] / $totalKehadiran * 100, 1) ?>%
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Jadwal mendatang -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="card-title mb-0">Jadwal Mendatang</h4>
                    <a href="<?= site_url('data/jadwal_ekskul') ?>" class="btn btn-sm btn-outline-primary">Lihat semua</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 130px;">Tanggal</th>
                                <th>Ekstrakurikuler</th>
                                <th>Materi</th>
                                <th>Tempat</th>
                                <th class="text-end" style="width: 160px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($jadwal)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada jadwal mendatang.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($jadwal as $j): ?>
                                    <tr>
                                        <td>
                                            <?= esc(date('d-m-Y', strtotime($j['tanggal']))) ?>
                                            <?php if ($j['tanggal'] === date('Y-m-d')): ?>
                                                <span class="badge bg-success ms-1">Hari ini</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($j['nama_ekskul']) ?></td>
                                        <td><?= esc($j['materi'] ?: '-') ?></td>
                                        <td><?= esc($j['tempat'] ?: '-') ?></td>
                                        <td class="text-end">
                                            <a href="<?= site_url('data/kehadiran_ekskul/input/' . $j['id_jadwal']) ?>"
                                               class="btn btn-sm btn-primary">
                                                <i class="mdi mdi-clipboard-check-outline"></i> Input Kehadiran
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if ($adaAnggota || $totalKehadiran > 0): ?>
    <script src="<?= base_url('assets/libs/apexcharts/apexcharts.min.js') ?>"></script>
    <script>
        <?php if ($adaAnggota): ?>
        new ApexCharts(document.querySelector('#chart-anggota'), {
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            series: [{
                name: 'Anggota aktif',
                data: <?= json_encode(array_map('intval', array_column($ekskul, 'jumlah_anggota'))) ?>
            }],
            xaxis: {
                categories: <?= json_encode(array_column($ekskul, 'nama_ekskul'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
            },
            yaxis: { labels: { formatter: function (v) { return Math.round(v); } } },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
            dataLabels: { enabled: false },
            colors: ['#556ee6']
        }).render();
        <?php endif; ?>

        <?php if ($totalKehadiran > 0): ?>
        new ApexCharts(document.querySelector('#chart-kehadiran'), {
            chart: { type: 'donut', height: 240 },
            series: <?= json_encode(array_map('intval', array_values($kehadiran))) ?>,
            labels: <?= json_encode(array_values($labelKehadiran)) ?>,
            colors: ['#34c38f', '#f1b44c', '#50a5f1', '#f46a6a'],
            legend: { show: false },
            dataLabels: { enabled: false }
        }).render();
        <?php endif; ?>
    </script>
<?php endif; ?>
<?= $this->endSection() ?>