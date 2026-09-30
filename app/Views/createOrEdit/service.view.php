<?php view('layouts.base_top')->execute() ?>
<?php view('layouts.header')->execute() ?>

<main class="dashboard-main">
    <h1><?= $type === 'update' ? 'EDITAR' : 'CADASTRAR' ?> SERVIÇO</h1>
    <form id="form-service-create" method="<?= $type === 'update' ? 'PUT' : 'POST' ?>">
        <div class="box-message">
            <p id="message"></p>
        </div>
        <div>
            <textarea name="description" id="description"><?=  $service->description ?? '' ?></textarea>
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
                value="<?=  $service->price ?? '' ?>"
            >

            <div>
                <p id="error-price"></p>
            </div>
        </div>

        <button
            class="btn btn-primary"
            type="submit"
        >
            <?= $type === 'update' ? 'Atualizar' : 'Cadastrar' ?> Serviço
        </button>
    </form>
</main>

<?php view('layouts.footer')->execute() ?>
<?php view('layouts.base_footer')->execute() ?>

<script>
$(document).ready(function() {
    $('#form-service-create').on('submit', function(e) {
        e.preventDefault();

        let type = '<?= $type ?>';
        let url  = '<?= ($type === 'update' && isset($service)) ? route("update.service", $service->id_service)->getFullPath() : route("store.service")->getFullPath() ?>';

        $('#error-description').text('');
        $('#error-price').text('');

        let formData = $(this).serialize();

        $.ajax({
            url: url,
            type: type === 'update' ? 'PUT' : 'POST',
            dataType: 'json',
            data: formData,
            success: function(response) {
                if (response.success && response.redirect) {
                    alert(`Serviço ${type === 'update' ? 'atualizado' : 'cadastrado'} com sucesso!`);
                    window.location.href = response.redirect;
                }
            },
            error: function(xhr, status, error) {
                let resJson = xhr.responseJSON;
                if (resJson) {
                    if (resJson?.errors) {
                        $.each(resJson.errors, function(field, messages) {
                            let text = messages[0];
                            $('#error-' + field).text(text);
                        });
                    } else if (resJson?.message) {
                        $('#message').text(resJson?.message);
                        $('#error-description').text('');
                        $('#error-price').text('');
                        $('#description').val('');
                        $('#price').val('');
                    } else {
                        console.log(response)
                    }
                }
            }
        });
    });
});
</script>