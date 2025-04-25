<?php
session_start();
require_once 'config/conexao.php';

if(isset($_POST['recuperar'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
   
    $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    
    if($usuario) {
       
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
        

        $stmt = $conn->prepare("INSERT INTO recuperacao_senha (usuario_id, token, expira) VALUES (?, ?, ?)");
        $stmt->execute([$usuario['id'], $token, $expira]);
        
     
        $link = "http://" . $_SERVER['HTTP_HOST'] . "/redefinir-senha.php?token=" . $token;
        $to = $email;
        $subject = "Recuperação de Senha - Encontre e Doe";
        $message = "
        <html>
        <head>
            <title>Recuperação de Senha</title>
        </head>
        <body>
            <h2>Olá {$usuario['nome']},</h2>
            <p>Você solicitou a recuperação de senha da sua conta no Encontre e Doe.</p>
            <p>Clique no link abaixo para criar uma nova senha:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p>Este link é válido por 1 hora.</p>
            <p>Se você não solicitou esta recuperação, ignore este email.</p>
            <br>
            <p>Atenciosamente,<br>Equipe Encontre e Doe</p>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Encontre e Doe <noreply@encontreedoe.com.br>' . "\r\n";
        
        if(mail($to, $subject, $message, $headers)) {
            $sucesso = "Um email foi enviado com as instruções para recuperar sua senha.";
        } else {
            $erro = "Erro ao enviar email. Tente novamente mais tarde.";
        }
    } else {
        $erro = "Email não encontrado em nossa base de dados.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Recuperar Senha - Encontre e Doe</title>
    <style>
        .recuperacao-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .icon-lock {
            font-size: 3rem;
            color: #1DC48C;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="recuperacao-container">
            <div class="text-center">
                <i class="fas fa-lock icon-lock"></i>
                <h2 class="mb-4">Recuperar Senha</h2>
                <p class="text-muted mb-4">Digite seu email para receber as instruções de recuperação</p>
            </div>

            <?php if(isset($sucesso)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $sucesso; ?>
                </div>
            <?php endif; ?>

            <?php if(isset($erro)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <button type="submit" name="recuperar" class="btn btn-success w-100">
                    Enviar Link de Recuperação
                </button>
            </form>

            <p class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left"></i> Voltar para o Login
                </a>
            </p>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
