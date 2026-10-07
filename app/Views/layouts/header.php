<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    
    <title><?= esc($pageTitle ?? config('Project')->title) ?></title>
    
    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.ico') ?>">
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.min.css') ?>" rel="stylesheet">
    
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
    </style>
</head>
<body data-sidebar="dark">