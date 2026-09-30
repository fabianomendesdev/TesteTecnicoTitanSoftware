<header class="dashboard-header">
    <div class="header-toogle"></div>
    <div class="header-right">
        <a href="#" id="btn-toggle-nav" aria-label="Menu de navegação">
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
        <div class="header-user-actions">
            <a class="btn-logout" href="<?= route('logout')->getPath() ?>">Sair</a>
        </div>
    </div>
</header>

<div class="dashboard-body">
    <aside class="dashboard-aside">
        <div class="user-info">
          <span class="user-welcome">Logado como: </span>
          <?php if (auth()->check()): ?>
          <div class="user-details">
            <strong class="user-name"></br><?=  auth()->user()->name ?? 'NÃO LOGADO' ?></strong>
            <?php if (auth()->user()->isAdmin()): ?>
              <span class="user-badge">Admin</span>
            <?php endif ?>
          </div>
          <?php endif ?>
        </div>
        <nav class="dashboard-nav">
            <ul class="dashboard-nav-list">
                <li>
                  <a
                    href="<?= route('dashboard')->getPath() ?>"
                    <?= isRoute('dashboard') ? 'class="active"' : '' ?>
                  >
                    Dashboard
                  </a>
                </li>
                <li>
                  <a
                    href="<?= route('create.service')->getPath() ?>"
                    <?= isRoute('create.service') ? 'class="active"' : '' ?>
                  >
                    Cadastrar Serviço
                  </a>
                </li>
            </ul>
        </nav>
    </aside>
<!-- MAIN ABAIXO -->