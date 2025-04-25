<?php
session_start();
require_once 'config/conexao.php';

if(isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    
    if($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];
        
        if($usuario['tipo'] == 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: area-usuario.php');
        }
        exit();
    } else {
        $erro = "Email ou senha inválidos";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Login - Encontre e Doe</title>
    <style>
        .login-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .social-login {
            margin: 20px 0;
            text-align: center;
        }
        .social-btn {
            width: 200px;
            margin: 5px;
            padding: 10px;
            border-radius: 20px;
        }
        .google-btn {
            background: white;
            border: 1px solid #ddd;
        }
        .apple-btn {
            background: black;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="login-container">
            <h2 class="text-center mb-4">Bem Vindo de Volta!</h2>
            <p class="text-center mb-4">Insira suas credenciais para acessar sua conta</p>

            <?php if(isset($erro)): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Insira seu email" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" placeholder="Insira sua senha" required>
                    <div class="text-end mt-1">
                        <a href="esqueci-senha.php" class="text-decoration-none text-muted">esqueci minha senha</a>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="lembre" name="lembre">
                    <label class="form-check-label" for="lembre">Lembre de mim</label>
                </div>

                <button type="submit" name="login" class="btn btn-success w-100">Entrar</button>
            </form>

            <div class="social-login">
                <p class="text-center text-muted">Ou</p>
                <button class="btn google-btn social-btn">
                    <img src="img/google-icon.png" alt="Google" width="20"> Entrar com Google
                </button>
                <button class="btn apple-btn social-btn">
                    <i class="fab fa-apple"></i> Entrar com Apple
                </button>
            </div>

            <p class="text-center mt-3">
                Não tem uma conta? <a href="cadastro.php" class="text-success">Criar uma conta</a>
            </p>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
