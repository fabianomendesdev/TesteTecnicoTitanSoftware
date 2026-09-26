<?php view('layouts.base_top')->execute() ?>
<?php view('layouts.header')->execute() ?>

<main class="dashboard-main">
    <h1>CADASTRAR SERVIÇO</h1>
    <form id="form-service-create" method="POST">
        <div class="box-message">
            <p id="message"></p>
        </div>
        <div>
            <textarea name="description" id="description"></textarea>
            <div>
                <p id="error-description"></p>
            </div>
        </div>

        <div>
            <input
                type="numeric"
                step="0.1"
                name="price"
                id="price"
            >

            <div>
                <p id="error-price"></p>
            </div>
        </div>

        <button
            class="btn btn-primary"
            type="submit"
        >
            Cadastrar Serviço
        </button>
    </form>
</main>

<?php view('layouts.footer')->execute() ?>
<?php view('layouts.base_footer')->execute() ?>

<script>
$(document).ready(function() {
    $('#form-service-create').on('submit', function(e) {
        e.preventDefault();

        $('#error-description').text('');
        $('#error-price').text('');

        let description = $('#description').val();
        let price       = $('#price').val();

        $.post('<?= route('store.service')->getFullPath() ?>', { 
            description: description, 
            price: price 
        }, function(response) {
            if (response.success && response.redirect) {
                alert("Serviço cadastrado com sucesso!");
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
                    $('#error-description').text('');
                    $('#error-price').text('');
                    $('#description').val('');
                    $('#price').val('');
                }
            } else {
                // Erro generico
            }
        });
    });
});
</script>