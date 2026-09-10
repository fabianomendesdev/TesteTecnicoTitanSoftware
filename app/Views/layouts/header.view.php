<header class="dashboard-header">
    <div class="header-toogle show">
    </div>
    <div class="header-right show">
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
            <?php if (isset($_SESSION['user'])): ?>
                <span><?=  $_SESSION['user']->name ?? 'NÃO LOGADO' ?></span>
            <?php endif ?>
            <a href="<?= route('logout')->getPath() ?>">Sair</a>
        </div>
    </div>
</header>

<div class="dashboard-body">
    <aside class="dashboard-aside show">
        <nav class="dashboard-nav">
            <ul class="dashboard-nav-list">
                <li><a href="<?= route('dashboard')->getPath() ?>" class="active">Dashboard</a></li>
                <li><a href="<?= route('services')->getPath() ?>">Serviços</a></li>
                <li><a href="<?= route('employees')->getPath() ?>">Funcionários</a></li>
            </ul>
        </nav>
    </aside>
<!-- MAIN ABAIXO -->