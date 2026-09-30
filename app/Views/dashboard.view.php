<?php view('layouts.base_top')->execute() ?>
<?php view('layouts.header')->execute() ?>

<main class="dashboard-main" id="dashboard">
    <h1>DASHBOARD</h1>

    <!-- Valor total serviço -->
    <div class="dashboard-card-total">
        <h2>Valor total serviço</h2>
        <div>
            R$ <?= number_format($totService, 2, ',', '.') ?>
        </div>
    </div>
    <!-- FIM Valor total serviço -->

    <!-- Tabelas de Últimos Serviços e Serviços Pendentes -->
    <div class="box-mini-table">
        <div class="mini-table">
            <h3 class="mini-table-title">Últimos Serviços</h3>
            <?php if (count($recentServices) > 0): ?>
            <ul class="mini-table-service-list">
                <?php foreach ($recentServices as $i => $service): ?>
                <li>
                    <span class="service-id"><?= str_pad($service->id_service, 7, '0', STR_PAD_LEFT) ?></span>
                    <span class="service-name"><?= $service->description ?></span>
                </li>
                <?php endforeach ?>
            </ul>
            <?php else: ?>
                <p>Não há últimos serviços.</p>
            <?php endif ?>
        </div>
        <div class="mini-table">
            <h3 class="mini-table-title">Serviços Pendentes</h3>
            <?php if (count($pendentServices) > 0): ?>
            <ul class="mini-table-service-list">
                <?php foreach ($pendentServices as $i => $service): ?>
                <li>
                    <span class="service-id"><?= str_pad($service->id_service, 7, '0', STR_PAD_LEFT) ?></span>
                    <span class="service-name"><?= $service->description ?></span>
                </li>
                <?php endforeach ?>
            </ul>
            <?php else: ?>
                <p>Não há serviços pendentes.</p>
            <?php endif ?>
        </div>
    </div>
    <!-- FIM Tabelas de Últimos Serviços e Serviços Pendentes -->

    <?php if(auth()->user()->isAdmin()): ?>
    <div class="box-filter">
        <form method="get">
            <?php
                $descricao = $_GET['descricao'] ?? '';
                $status    = $_GET['status']    ?? '';
                $dataIni   = $_GET['data_ini']  ?? '';
                $dataFim   = $_GET['data_fim']  ?? '';
                $userId    = $_GET['id_user']   ?? '';
            ?>
            <input
                class="filter-by-name"
                type="text"
                name="descricao"
                placeholder="Descrição"
                value="<?= $descricao ?>" 
            >
            <div class="group-1">
                <div class="group-2">
                    <input
                        type="date"
                        name="data_ini"
                        value="<?= $_GET['data_ini'] ?? '' ?>"
                    >
                    <input
                        type="date"
                        name="data_fim"
                        value="<?= $_GET['data_fim'] ?? '' ?>"
                    >
                    <select name="status">
                        <option value="" <?= !in_array($status, ['P', 'F']) ? 'selected' : '' ?>>TODOS</option>
                        <option value="P" <?= $status == 'P' ? 'selected' : '' ?>>PENDENTE</option>
                        <option value="F" <?= $status == 'F' ? 'selected' : '' ?>>FINALIZADO</option>
                    </select>
                </div>
                <select name="id_user">
                    <?php
                        $userExists = !empty(array_filter($users, fn($u) => $u->id_user == $userId));
                    ?>
                    <option value="" <?= !$userExists ? 'selected' : '' ?>>TODOS</option>
                    
                    <?php foreach ($users as $user): ?>
                    <option value="<?= $user->id_user ?>" <?= $user->id_user == $userId ? 'selected' : '' ?>>
                        <?= $user->name ?>
                    </option>
                    <?php endforeach ?>
                </select>

                <button class="btn btn-primary" type="submit">Filtrar</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="service-table">
            <thead>
                <tr>
                    <th style="text-align: start;">ID</th>
                    <th style="text-align: start;">DESCRIÇÃO</th>
                    <th style="text-align: center;">STATUS</th>
                    <th style="text-align: center;">VALOR</th>
                    <th style="text-align: start;">USUÁRIO</th>
                    <th style="text-align: start;">OPÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allServices as $i => $service): ?>
                <tr>
                    <td style="text-align: start;"><?= str_pad($service->id_service, 7, '0', STR_PAD_LEFT) ?></td>
                    <td style="text-align: start;"><?= $service->description ?></td>
                    <td style="text-align: center;"><?= $service->finished_at ? 'FINALIZADO' : 'PENDENTE' ?></td>
                    <td style="text-align: center;">R$ <?= number_format($service->price, 2, ',', '.') ?></td>
                    <td style="text-align: start;"><?= $service?->user?->name ?></td>
                    <td style="text-align: start;">
                        <div class="table-actions">
                            <button
                                class="btn btn-danger"
                                onclick="deleteItem(<?= $service->id_service ?>)"
                                title="EXCLUIR"
                            >
                                EXCLUIR
                            </button>
                            <button
                                class="btn btn-warning"
                                onclick="updateItem('<?= route('edit.service', $service->id_service)->getFullPath() ?>')"
                                title="ALTERAR"
                            >
                                ALTERAR
                            </button>
                            <?php if(!$service->finished_at): ?>
                            <button
                                class="btn btn-primary"
                                onclick="finishItem(<?= $service->id_service ?>)"
                                title="FINALIZAR"
                            >
                                FINALIZAR
                            </button>
                            <?php endif ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?php endif ?>
</main>

<?php view('layouts.footer')->execute() ?>
<?php view('layouts.base_footer')->execute() ?>

<script>
function deleteItem(serviceId) {
    let formattedCode = String(serviceId).padStart(7, '0');

    if (confirm(`Você tem certeza que deseja excluir o serviço '${formattedCode}'`)) {
        let url = '<?= route('destroy.service')->getFullPath() ?>';
        url = url.replace(/\/([^\/]+)$/, '/' + serviceId);

        $.ajax({
            url: url,
            type: 'DELETE',
            dataType: 'json',
            success: function(response) {
                if (response && response.message) {
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                let resJson = xhr.responseJSON;
                if (resJson && resJson.message) {
                    alert(`Mensagem de ERRO: ${resJson.message}'`);
                } else {
                    alert(`O servidor não retornou um JSON válido. ${xhr.responseText}`);
                }
            }
        });
    }
}

function updateItem(href) {
    location.href = href || '#';
}

function finishItem(serviceId) {
    let formattedCode = String(serviceId).padStart(7, '0');

    if (confirm(`Você tem certeza que deseja finalizar o serviço '${formattedCode}'`)) {
        let url = '<?= route('finish.service')->getFullPath() ?>';
        url = url.replace(/\/([^\/]+)$/, '/' + serviceId);

        $.ajax({
            url: url,
            type : 'POST',
            dataType: 'json',
            success: function(response) {
                if (response && response.message) {
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                let resJson = xhr.responseJSON;
                if (resJson && resJson.message) {
                    alert(`Mensagem de ERRO: ${resJson.message}'`);
                } else {
                    alert(`O servidor não retornou um JSON válido. ${xhr.responseText}`);
                }
            }
        });
    }
}
</script>