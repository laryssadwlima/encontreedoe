<?php
session_start();
require_once 'config/conexao.php';
require_once 'config/auth.php';

verificarLogin();

if(isset($_POST['alterar'])) {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];
    
    if($nova_senha !== $confirma_senha) {
        $erro = "As senhas não conferem";
    } else {
        $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $usuario = $stmt->fetch();
        
        if(password_verify($senha_atual, $usuario['senha'])) {
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            if($stmt->execute([$nova_senha_hash, $_SESSION['usuario_id']])) {
                $sucesso = "Senha alterada com sucesso!";
            } else {
                $erro = "Erro ao alterar senha";
            }
        } else {
            $erro = "Senha atual incorreta";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Alterar Senha - Encontre e Doe</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Alterar Senha</h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($sucesso)): ?>
                            <div class="alert alert-success"><?php echo $sucesso; ?></div>
                        <?php endif; ?>

                        <?php if(isset($erro)): ?>
                            <div class="alert alert-danger"><?php echo $erro; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Senha Atual</label>
                                <input type="password" class="form-control" name="senha_atual" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nova Senha</label>
                                <input type="password" class="form-control" name="nova_senha" required 
                                       minlength="6" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}">
                                <small class="text-muted">
                                    Mínimo 6 caracteres, incluindo maiúsculas, minúsculas e números
                                </small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" name="confirma_senha" required>
                            </div>
                            <button type="submit" name="alterar" class="btn btn-success">
                                Alterar Senha
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
