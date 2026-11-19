<?= view('layouts/header', ['pageTitle' => $title ?? null]) ?>

<div id="layout-wrapper">
    <?= view('layouts/topbar') ?>
    <?= view('layouts/sidebar') ?>
    <?= view('layouts/body') ?>
</div>

<?= view('layouts/rightbar') ?>
<?= view('layouts/scripts') ?>

</body>
</html>