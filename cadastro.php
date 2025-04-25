<?php
session_start();
require_once 'config/conexao.php';

if(isset($_POST['cadastrar'])) {
    $nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $telefone = filter_var($_POST['telefone'], FILTER_SANITIZE_STRING);
    

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if($stmt->rowCount() > 0) {
        $erro = "Este email já está cadastrado";
    } else {
        $sql = "INSERT INTO usuarios (nome, email, senha, telefone, tipo) VALUES (?, ?, ?, ?, 'usuario')";
        $stmt = $conn->prepare($sql);
        
        if($stmt->execute([$nome, $email, $senha, $telefone])) {
            $_SESSION['mensagem'] = "Cadastro realizado com sucesso! Faça login para continuar.";
            header('Location: login.php');
            exit();
        } else {
            $erro = "Erro ao realizar cadastro";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Encontre e Doe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .cadastro-container {
            max-width: 600px;
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
        <div class="cadastro-container">
            <h2 class="text-center mb-4">Criar uma Conta</h2>
            <p class="text-center mb-4">Preencha os dados abaixo para se cadastrar</p>

            <?php if(isset($erro)): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>

            <form method="POST" action="" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="tel" class="form-control" id="telefone" name="telefone" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>

                <div class="mb-3">
                    <label for="confirma_senha" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="termos" required>
                    <label class="form-check-label" for="termos">
                        Li e aceito os <a href="termos.php" class="text-success">Termos de Uso</a> e 
                        <a href="privacidade.php" class="text-success">Política de Privacidade</a>
                    </label>
                </div>

                <button type="submit" name="cadastrar" class="btn btn-success w-100">Criar Conta</button>
            </form>

            <p class="text-center mt-3">
                Já tem uma conta? <a href="login.php" class="text-success">Fazer login</a>
            </p>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
      
        document.querySelector('form').addEventListener('submit', function(event) {
            if (!this.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.classList.add('was-validated');
        });

        document.getElementById('confirma_senha').addEventListener('input', function() {
            if(this.value !== document.getElementById('senha').value) {
                this.setCustomValidity('As senhas não conferem');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
