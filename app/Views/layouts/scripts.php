<!-- JAVASCRIPT -->
<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/metismenu/metisMenu.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/simplebar/simplebar.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/node-waves/waves.min.js') ?>"></script>

<!-- Plugin -->
<script src="<?= base_url('assets/libs/select2/js/select2.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.js') ?>"></script>

<script src="<?= base_url('assets/js/app.js') ?>"></script>

<script>
    $(function () {
        // Select2: aktif untuk semua <select class="select2">
        $('select.select2').each(function () {
            $(this).select2({
                width: '100%',
                placeholder: $(this).data('placeholder') || '',
                allowClear: $(this).data('allow-clear') === true
            });
        });

        // Konfirmasi hapus: <form class="js-delete" data-nama="...">
        $(document).on('submit', 'form.js-delete', function (e) {
            e.preventDefault();

            var form = this;
            var nama = $('<div>').text($(form).data('nama') || 'ini').html();

            Swal.fire({
                title: 'Hapus data?',
                html: 'Data <b>' + nama + '</b> akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#f46a6a',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

<?= $this->renderSection('scripts') ?>