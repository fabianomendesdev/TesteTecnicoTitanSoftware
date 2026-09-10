<?php view('layouts.base_top')->execute() ?>

<main class="main" id="login">
    REGISTER
</main>

<?php view('layouts.base_footer')->execute() ?>

<script>
$(document).ready(function() {
    $('#form-register').on('submit', function(e) {
        e.preventDefault();

        $('#error-name').text('');
        $('#error-email').text('');
        $('#error-password').text('');

        let name     = $('#name').val();
        let email    = $('#email').val();
        let password = $('#password').val();

        $.post('<?= route('store.login')->getFullPath() ?>', { 
            email: name,
            email: email,
            password: password 
        }, function(response) {
            if (response.success && response.redirect) {
                window.location.href = response.redirect;
            }
        })
        .fail(function(xhr) {
            let response = xhr.responseJSON;

            if (response) {
                if (response.errors) {
                    $.each(response.errors, function(field, messages) {
                        let text = messages[0];
                        $('#error-' + field).text(text);
                    });
                }
            } else if (response.message) {
            }
        });
    });
});
</script>