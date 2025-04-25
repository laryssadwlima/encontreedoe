<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';

verificarAdmin();


$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();


$stmt = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'usuario'");
$total_usuarios = $stmt->fetch()['total'];


$stmt = $conn->query("SELECT COUNT(*) as total FROM contatos WHERE respondido = 0");
$mensagens_pendentes = $stmt->fetch()['total'];


$stmt = $conn->query("SELECT COUNT(*) as total FROM doacoes");
$total_doacoes = $stmt->fetch()['total'];


$stmt = $conn->query("SELECT * FROM contatos ORDER BY data_envio DESC LIMIT 5");
$mensagens = $stmt->fetchAll();


$stmt = $conn->query("SELECT * FROM usuarios WHERE tipo = 'usuario' ORDER BY data_cadastro DESC LIMIT 5");
$usuarios_recentes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include '../includes/head.php'; ?>
    <title>Painel Administrativo - Encontre e Doe</title>
    <style>
        .admin-container {
            margin-top: 30px;
            margin-bottom: 40px;
        }
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #1DC48C;
            padding: 15px 20px;
        }
        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 60px;
            object-fit: cover;
            border: 3px solid #1DC48C;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        .nav-pills .nav-link.active {
            background-color: #1DC48C;
        }
        .nav-pills .nav-link {
            color: #666;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container admin-container">
        <div class="row">
        
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="<?php echo !empty($usuario['foto']) ? '../'.$usuario['foto'] : '../img/default-avatar.png'; ?>" 
                             alt="Foto de Perfil" 
                             class="profile-image mb-3">
                        <h4><?php echo $usuario['nome']; ?></h4>
                        <p class="text-muted">Administrador</p>
                    </div>
                </div>

             
                <div class="card">
                    <div class="card-body p-0">
                        <div class="nav flex-column nav-pills">
                            <button class="nav-link active text-start p-3" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#dashboard">
                                <i class="fas fa-home me-2"></i> Dashboard
                            </button>
                            <button class="nav-link text-start p-3" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#usuarios">
                                <i class="fas fa-users me-2"></i> Usuários
                            </button>
                            <button class="nav-link text-start p-3" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#mensagens">
                                <i class="fas fa-envelope me-2"></i> Mensagens
                                <?php if($mensagens_pendentes > 0): ?>
                                    <span class="badge bg-danger float-end"><?php echo $mensagens_pendentes; ?></span>
                                <?php endif; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-md-8">
                <div class="tab-content">
                  
                    <div class="tab-pane fade show active" id="dashboard">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Dashboard</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h3><?php echo $total_usuarios; ?></h3>
                                                <p class="mb-0">Usuários Cadastrados</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body text-center">
                                                <h3><?php echo $mensagens_pendentes; ?></h3>
                                                <p class="mb-0">Mensagens Pendentes</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body text-center">
                                                <h3><?php echo $total_doacoes; ?></h3>
                                                <p class="mb-0">Total de Doações</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mt-4 mb-3">Usuários Recentes</h6>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Email</th>
                                                <th>Data Cadastro</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($usuarios_recentes as $user): ?>
                                                <tr>
                                                    <td><?php echo $user['nome']; ?></td>
                                                    <td><?php echo $user['email']; ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($user['data_cadastro'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

               
                    <div class="tab-pane fade" id="usuarios">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Usuários Cadastrados</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Foto</th>
                                                <th>Nome</th>
                                                <th>Email</th>
                                                <th>Doações</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $stmt = $conn->query("
                                                SELECT u.*, COUNT(d.id) as total_doacoes 
                                                FROM usuarios u 
                                                LEFT JOIN doacoes d ON u.id = d.usuario_id 
                                                WHERE u.tipo = 'usuario' 
                                                GROUP BY u.id 
                                                ORDER BY u.nome
                                            ");
                                            while($user = $stmt->fetch()):
                                            ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo !empty($user['foto']) ? '../'.$user['foto'] : '../img/default-avatar.png'; ?>" 
                                                         class="rounded-circle" width="40" height="40">
                                                </td>
                                                <td><?php echo $user['nome']; ?></td>
                                                <td><?php echo $user['email']; ?></td>
                                                <td><?php echo $user['total_doacoes']; ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger" onclick="excluirUsuario(<?php echo $user['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                
                    <div class="tab-pane fade" id="mensagens">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Mensagens Recebidas</h5>
                            </div>
                            <div class="card-body">
                                <?php if(empty($mensagens)): ?>
                                    <p class="text-center text-muted py-3">
                                        Nenhuma mensagem recebida
                                    </p>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach($mensagens as $msg): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-1"><?php echo $msg['assunto']; ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y', strtotime($msg['data_envio'])); ?>
                                                    </small>
                                                </div>
                                                <p class="mb-1">De: <?php echo $msg['email']; ?></p>
                                                <p class="mb-1 text-muted"><?php echo $msg['mensagem']; ?></p>
                                                <div class="mt-2">
                                                    <button class="btn btn-sm btn-success" onclick="responderMensagem(<?php echo $msg['id']; ?>)">
                                                        Responder
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function excluirUsuario(id) {
            if(confirm('Tem certeza que deseja excluir este usuário?')) {
                fetch('excluir_usuario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({id: id})
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        alert('Usuário excluído com sucesso!');
                        location.reload();
                    } else {
                        alert(data.message || 'Erro ao excluir usuário');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao excluir usuário');
                });
            }
        }

        function responderMensagem(id) {
            
            alert('Funcionalidade em desenvolvimento');
        }
    </script>
</body>
</html>
