<?php view('layouts.base_top')->execute() ?>

<main class="main" id="register">
    <section id="section-register">
        <h1 class="title-register">Cadastrar Novo Usuário</h1>

        <form id="form-register" method="POST">
            <div class="box-message">
                <p id="message"></p>
            </div>
            <div class="register-input">
                <input 
                    class="form-input"
                    type="text"
                    name="name"
                    id="name"
                    placeholder="Nome"
                    max="120"
                    required
                    autocomplete="name"
                >
                <div class="box-errors">
                    <p id="error-name"></p>
                </div>
            </div>
            <div class="register-input">
                <input 
                    class="form-input"
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Email"
                    max="120"
                    required
                    autocomplete="email"
                >
                <div class="box-errors">
                    <p id="error-email"></p>
                </div>
            </div>
            <div class="register-input">
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

            <div class="register-actions">
                <div class="box-button">
                    <button class="btn btn-primary" id="btn-register" type="submit">Cadastrar</button>
                </div>
                <div class="box-login">
                    <a href="<?= route('login')->getFullPath() ?>">Entrar</a>
                </div>
            </div>
        </form>
    </section>
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

        $.post('<?= route('store.register')->getFullPath() ?>', { 
            name: name,
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
                    });
                } else if (response?.message) {
                    $('#message').text(response?.message);
                    $('#error-name').text('');
                    $('#error-email').text('');
                    $('#error-password').text('');
                    $('#name').val('');
                    $('#email').val('');
                    $('#password').val('');
                } else {
                    console.log(response)
                }
            }
        });
    });
});
</script>