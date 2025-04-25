<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Encontre e Doe</title>
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
            <div class="contact-container">
                <h1>Entre em Contato</h1>
                <p class="mb-4">Tem dúvidas ou sugestões? Fale conosco! <span class="emoji">💚</span></p>

                <div class="contact-info mb-5">
                    <p><span class="emoji">📍</span> Endereço: Rua da Solidariedade, 123 - Centro, São Paulo, SP</p>
                    <p><span class="emoji">📞</span> Telefone: (11) 98765-4321</p>
                    <p><span class="emoji">📧</span> E-mail: contato@encontreeDoe.com.br</p>
                    <p><span class="emoji">🕒</span> Horário de Atendimento: Segunda a Sexta - 9h às 18h</p>
                </div>

                <div class="social-media mb-4">
                    <h3><span class="emoji">📱</span> Redes Sociais</h3>
                    <p>@EncontreeDoe</p>
                    <div class="social-icons">
                        <a href="#" class="text-success me-3"><i class="fab fa-instagram fa-2x"></i></a>
                        <a href="#" class="text-success me-3"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-success"><i class="fab fa-tiktok fa-2x"></i></a>
                    </div>
                </div>

                <div class="contact-form">
                    <h3>Fale Conosco!</h3>
                    <p class="mb-4">Preencha nosso formulário e responderemos o mais rápido possível. <span class="emoji">😊</span><span class="emoji">✨</span></p>
                    
                    <form class="contact-form-container">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Nome:" required>
                            </div>
                            <div class="col-md-6">
                                <input type="tel" class="form-control" placeholder="Contato:" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Email:" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Assunto:" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="5" placeholder="Mensagem:" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success rounded-pill px-4">Enviar</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
