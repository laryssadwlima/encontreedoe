<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encontre e Doe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="img/icon.ico">

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="img/logo.png" alt="Encontre e Doe Logo" height="50">
            </a>
            <div class="navbar-nav mx-auto">
                <a class="nav-link mx-3" href="index.php">Início</a>
                <a class="nav-link mx-3" href="onde_doar.php">Onde Doar</a>
                <a class="nav-link mx-3" href="sobre_nos.php">Sobre Nós</a>
                <a class="nav-link mx-3" href="contato.php">Contato</a>
            </div>
            <a href="login.php" class="btn btn-success rounded-pill px-4">Entrar</a>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="main-content">
                <div class="content-left">
                    <h1 class="mb-3">Bem-vindo(a) ao <span class="text-success">Encontre e Doe</span></h1>
                    <p class="mb-4">Conectando doadores a quem mais precisa.</p>
                    
                    <div class="mb-4">
                        <h3 class="text-success">Como Funciona?</h3>
                        <p>O Encontre e Doe é uma plataforma colaborativa que facilita a doação de roupas, alimentos, móveis e outros itens essenciais para instituições e pessoas em situação de vulnerabilidade. Nosso objetivo é tornar o processo de doação mais acessível e eficiente, ajudando comunidades e organizações a receberem o que realmente precisam.</p>
                        <p>Através do nosso sistema, você consegue encontrar locais que aceitam doações de forma rápida e prática.</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-success">Como Doar?</h3>
                        <p>Se você tem itens em bom estado e deseja doá-los, siga estes passos:</p>
                        <p>📌 Procure um ponto de doação – Filtre por categoria e encontre o local mais adequado para sua doação.</p>
                        <p>📌 Tire suas dúvidas – Entre em contato com as comunidades e organizações através de seus perfis sociais para mais informações.</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-success">Como Receber?</h3>
                        <p>Se você representa uma instituição que precisa de doações, o processo é simples:</p>
                        <p>📌 Clique no botão "Entrar" e cadastre uma conta.</p>
                        <p>📌 Informe as informações necessárias – Horários de recebimento, local de doação e outros dados importantes.</p>
                    </div>

                    <p class="mb-4">Nosso objetivo é garantir que os donativos cheguem a quem mais precisa de forma rápida e eficiente.</p>
                    <p>Junte-se a nós nessa corrente do bem! 💚✨</p>

                    <a href="onde_doar.php" class="btn btn-success btn-lg rounded-pill">
                        <i class="fas fa-search me-2"></i>
                        Busque um ponto de doação próximo usando seu CEP e filtros
                    </a>
                </div>
                <div class="content-right">
                    <img src="img/pag1.png" alt="Ilustração" class="hero-image">
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
