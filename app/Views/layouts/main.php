<?= view('layout/header',['pageTitle'=>$pageTitle??null]) ?>

<div id="layout-wrapper">
    <?= view('layout/topbar') ?>
    <?= view('layout/sidebar') ?>
    <?= view('layout/body') ?>
</div>

<?= view('layout/rightbar') ?>