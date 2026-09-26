<?php view('layouts.base_top')->execute() ?>

<main class="main" id="login">
    <section id="section-login">
        <h1 class="title-login">Sistema de Controle de Serviços</h1>

        <form id="form-login" method="POST">
            <div class="box-message">
                <p id="message"></p>
            </div>
            <div class="login-input">
                <input 
                    class="form-input"
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Email"
                    required
                    value="<?= $email ?>"
                    autocomplete="email"
                >
                <div class="box-errors">
                    <p id="error-email"></p>
                </div>
            </div>

            <div class="login-input">
                <input
                    class="form-input"
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Senha"
                    required
                    autocomplete="password"
                >
                <div class="box-errors">
                    <p id="error-password"></p>
                </div>
            </div>

            <div class="login-actions">
                <div class="box-button">
                    <button class="btn btn-primary" id="btn-login" type="submit">Entrar</button>
                </div>

                <div class="box-create-account">
                    <a href="<?= route('register')->getFullPath() ?>">Cadastrar usuário</a>
                </div>
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

            if (response) {
                if (response?.errors) {
                    $.each(response.errors, function(field, messages) {
                        let text = messages[0];
                        $('#error-' + field).text(text);
                        $('#error-password').text('');
                        $('#password').val('');
                    });
                } else if (response?.message) {
                    $('#message').text(response?.message);
                    $('#error-email').text('');
                    $('#error-password').text('');
                    $('#email').val('');
                    $('#password').val('');
                }
            } else {
                // Erro generico
            }
        });
    });
});
</script>