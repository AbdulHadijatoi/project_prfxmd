<script>
    $('.ajaxForm').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var formData = $form.serialize();
        var action = $form.find('[name="action"]').val();
        $.ajax({
            url: `/admin/pamm/${action}`,
            type: 'POST',
            data: formData,
            beforeSend: function () {
                $form.find('button[type="submit"]').prop('disabled', true);
                Swal.fire({
                    title: 'Submitting...',
                    text: 'Please wait while we process your request.',
                    icon: 'info',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (response) {
                if (response) {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                    }
                }
                if (response.status == 'success') {
                    $('.modal').modal('hide');
                    Swal.fire({
                        title: 'Success!',
                        text: 'Request submitted successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        Swal.fire({
                            title: 'Please hold on',
                            text: 'The page is refreshing. This may take a moment.',
                            icon: 'info',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // location.reload();
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an issue with the request.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    // location.reload();
                });
            },
            complete: function () {
                $form.find('button[type="submit"]').prop('disabled', false);
            }
        });
    });
</script>
