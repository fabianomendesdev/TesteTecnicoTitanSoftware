<header class="dashboard-header">
    <div class="header-toogle">
    </div>
    <div class="header-right">
        <a href="#" id="btn-toggle-nav">
            <img 
                class="open"
                src="<?= assets('/img/menu-open-icon.svg') ?>"
                alt="Abrir menu"
            >
            <img 
                class="close"
                src="<?= assets('/img/menu-close-icon.svg') ?>"
                alt="Fechar menu"
            >
        </a>
        <div>
            <a href="<?= route('logout')->getPath() ?>">Sair</a>
        </div>
    </div>
</header>

<div class="dashboard-body">
    <aside class="dashboard-aside">
        <div class="user-info">
            <span>Logado como: </span>
            <?php if (auth()->check()): ?>
                <span><?=  auth()->user()->name ?? 'NÃO LOGADO' ?></span>
                <?php if (auth()->user()->isAdmin()): ?>
                    <span> (Admin)</span>
                <?php endif ?>
            <?php endif ?>
        </div>
        <nav class="dashboard-nav">
            <ul class="dashboard-nav-list">
                <li><a href="<?= route('dashboard')->getPath() ?>" class="active">Dashboard</a></li>
                <li><a href="<?= route('create.service')->getPath() ?>">Cadastrar Serviço</a></li>
            </ul>
        </nav>
    </aside>
<!-- MAIN ABAIXO -->