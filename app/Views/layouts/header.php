<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title><?= esc(! empty($pageTitle) ? $pageTitle . ' | ' . config('Project')->title : config('Project')->title) ?></title>

    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.ico') ?>">

    <!-- Tema -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.min.css') ?>" rel="stylesheet">

    <!-- Plugin -->
    <link href="<?= base_url('assets/libs/select2/css/select2.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet">

    <style>
        .logo-text {
            font-weight: 700;
            font-size: 18px;
        }
        .menu-title-small {
            font-size: 11px;
            text-transform: uppercase;
            color: #8791a5;
            padding: 10px 24px;
        }
        .simple-icon {
            display: inline-block;
            width: 22px;
            text-align: center;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .content-page {
            min-height: 100vh;
        }

        /* Select2 disamakan dengan form-control Bootstrap 5 */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        select.is-invalid ~ .select2-container .select2-selection--single {
            border-color: #dc3545;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body data-sidebar="dark">