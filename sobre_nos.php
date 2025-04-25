<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Encontre e Doe</title>
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
                    <h1 class="mb-3">Sobre Nós - <span class="text-success">Encontre e Doe</span></h1>
                    <p class="mb-4">Conectando doadores a quem mais precisa.</p>

                    <div class="mb-4">
                        <p>O Encontre e Doe nasceu com a missão de facilitar o acesso a informações sobre pontos de doação em todo o Brasil. Sabemos que muitas pessoas querem ajudar, mas nem sempre sabem onde e como doar. Nossa plataforma resolve esse problema ao conectar doadores a instituições, ONGs e centros de arrecadação de forma simples e eficiente.</p>
                        
                        <p>Aqui, você encontra locais próximos que aceitam doações de roupas, alimentos, móveis, eletrônicos e outros itens essenciais. Tudo isso através de um mapa interativo, onde você pode filtrar por categoria e localização, garantindo que sua doação chegue a quem realmente precisa.</p>
                    </div>

                    <div class="mb-4">
                        <p class="alert alert-success">
                            <span class="emoji">💚</span> Importante: O Encontre e Doe não realiza doações diretamente nem intermedia adoções ou transações financeiras. Somos apenas uma ferramenta que facilita a conexão entre quem quer doar e os locais que recebem doações.
                        </p>
                    </div>

                    <div class="mb-4">
                        <p>Se você deseja contribuir com um mundo mais solidário, explore nosso site, encontre um ponto de arrecadação próximo e faça a diferença!</p>
                    </div>

                    <div class="contact-info">
                        <p class="mb-2">
                            <span class="emoji">📝</span> Tem dúvidas ou sugestões? Entre em 
                            <a href="contato.php" class="text-success text-decoration-none">contato</a> 
                            conosco. Juntos, podemos ajudar ainda mais! <span class="emoji">✨</span>
                        </p>
                    </div>
                </div>
                <div class="content-right1">
                    <img src="img/pag3.png" alt="Ilustração" class="hero-image">
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
