<?php
session_start();
require_once 'config/conexao.php';

if(!isset($_GET['token'])) {
    header('Location: login.php');
    exit();
}

$token = $_GET['token'];
$agora = date('Y-m-d H:i:s');


$stmt = $conn->prepare("
    SELECT r.*, u.email 
    FROM recuperacao_senha r 
    JOIN usuarios u ON r.usuario_id = u.id 
    WHERE r.token = ? AND r.expira > ? AND r.usado = 0
");
$stmt->execute([$token, $agora]);
$recuperacao = $stmt->fetch();

if(!$recuperacao) {
    $_SESSION['erro'] = "Link de recuperação inválido ou expirado.";
    header('Location: login.php');
    exit();
}

if(isset($_POST['redefinir'])) {
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];
    
    if($senha !== $confirma_senha) {
        $erro = "As senhas não conferem.";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        try {
            $conn->beginTransaction();
            
          
            $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $stmt->execute([$senha_hash, $recuperacao['usuario_id']]);
            
          
            $stmt = $conn->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE token = ?");
            $stmt->execute([$token]);
            
            $conn->commit();
            
            $_SESSION['sucesso'] = "Senha redefinida com sucesso! Faça login com sua nova senha.";
            header('Location: login.php');
            exit();
            
        } catch(Exception $e) {
            $conn->rollBack();
            $erro = "Erro ao redefinir senha. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Redefinir Senha - Encontre e Doe</title>
    <style>
        .redefinir-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="redefinir-container">
            <h2 class="text-center mb-4">Redefinir Senha</h2>
            
            <?php if(isset($erro)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Nova Senha</label>
                    <input type="password" class="form-control" name="senha" required 
                           minlength="6" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}">
                    <small class="text-muted">
                        Mínimo 6 caracteres, incluindo maiúsculas, minúsculas e números
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmar Nova Senha</label>
                    <input type="password" class="form-control" name="confirma_senha" required>
                </div>

                <button type="submit" name="redefinir" class="btn btn-success w-100">
                    Redefinir Senha
                </button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>