<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="../img/logo.png" alt="Encontre e Doe Logo" height="50">
        </a>
        
        <?php if(!isset($_SESSION['usuario_id'])): ?>
        <div class="navbar-nav mx-auto">
            <a class="nav-link mx-3" href="index.php">Início</a>
            <a class="nav-link mx-3" href="onde_doar.php">Onde Doar</a>
            <a class="nav-link mx-3" href="sobre_nos.php">Sobre Nós</a>
            <a class="nav-link mx-3" href="contato.php">Contato</a>
        </div>
        <a href="login.php" class="btn btn-success rounded-pill px-4">Entrar</a>
        <?php else: ?>
        <div class="ms-auto">
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-2"></i><?php echo $_SESSION['usuario_nome']; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php if($_SESSION['usuario_tipo'] == 'admin'): ?>
                        <li><a class="dropdown-item" href="admin/dashboard.php">Painel Admin</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="area-usuario.php">Minha Área</a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item" href="alterar-senha.php">Alterar Senha</a></li>
                    <li><h  r class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</nav>


<div style="margin-top: 76px;"></div>