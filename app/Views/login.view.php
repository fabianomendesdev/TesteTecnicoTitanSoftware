<?php view('layouts.base_top')->execute() ?>

<main class="main" id="login">
    <section id="section-login">
        <h1>Teste Titan Software</h1>

        <form id="form-login" method="POST">
            <div class="login-input">
                <input 
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Email"
                    required
                >
                <div>
                    <p id="error-email"></p>
                </div>
            </div>

            <div class="login-input">
                <input 
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Password"
                    required
                >
                <div>
                    <p id="error-password"></p>
                </div>
            </div>

            <div>
                <a href="<?= route('register')->getFullPath() ?>">Criar conta</a>
            </div>
            
            <div class="box-button">
                <button type="submit" id="btn-login">Login</button>
            </div>
        </form>
    </section>
</main>

<?php view('layouts.base_footer')->execute() ?>

<script>
$(document).ready(function() {
    $('#form-login').on('submit', function(e) {
        e.preventDefault();

        $('#error-email').text('');
        $('#error-password').text('');

        let email    = $('#email').val();
        let password = $('#password').val();

        $.post('<?= route('store.login')->getFullPath() ?>', { 
            email: email, 
            password: password 
        }, function(response) {
            if (response.success && response.redirect) {
                window.location.href = response.redirect;
            }
        })
        .fail(function(xhr) {
            let response = xhr.responseJSON;

            console.log(response);

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