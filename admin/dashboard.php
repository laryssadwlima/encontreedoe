<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';

verificarAdmin();

$stmt = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$total_usuarios = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM contatos WHERE respondido = 0");
$mensagens_pendentes = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM locais");
$total_locais = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

$stmt = $conn->query("SELECT COUNT(*) as total FROM doacoes");
$total_doacoes = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT * FROM contatos ORDER BY data_envio DESC");
$mensagens = $stmt->fetchAll();

if(isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $tipo_mensagem = $_SESSION['tipo_mensagem'];
    unset($_SESSION['mensagem']);
    unset($_SESSION['tipo_mensagem']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include '../includes/head.php'; ?>
    <title>Painel Administrativo - Encontre e Doe</title>
    <style>
        .user-area-container {
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
        .position-relative {
            display: inline-block;
        }
        .btn-success.position-absolute {
            padding: 5px 8px;
            border-radius: 50%;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <?php if(isset($mensagem)): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
            <?php echo $mensagem; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="container user-area-container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="position-relative">
                            <img src="<?php echo !empty($usuario['foto']) ? '../'.$usuario['foto'] : '../img/default-avatar.png'; ?>" 
                                 alt="Foto de Perfil" 
                                 class="profile-image mb-3" 
                                 id="previewFoto">
                            <label for="inputFoto" class="btn btn-sm btn-success position-absolute bottom-0 end-0">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>
                        <input type="file" class="d-none" id="inputFoto" name="foto" accept="image/*">
                        <h4><?php echo $usuario['nome']; ?></h4>
                        <p class="text-muted">Administrador</p>
                        <button class="btn btn-outline-success btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editarPerfilModal">
                            <i class="fas fa-edit"></i> Editar Perfil
                        </button>
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
                                    data-bs-target="#locais">
                                <i class="fas fa-map-marker-alt me-2"></i> Locais de Doação
                            </button>
                            <button class="nav-link text-start p-3" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#mensagens">
                                <i class="fas fa-envelope me-2"></i> Mensagens
                                <?php if($mensagens_pendentes > 0): ?>
                                    <span class="badge bg-danger float-end"><?php echo $mensagens_pendentes; ?></span>
                                <?php endif; ?>
                            </button>
                            <button class="nav-link text-start p-3" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#usuarios">
                                <i class="fas fa-users me-2"></i> Usuários
                            </button>
                            <button class="nav-link text-start p-3" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#perfil">
                                <i class="fas fa-user me-2"></i> Meu Perfil
                            </button>
                            <a href="../logout.php" class="nav-link text-start p-3 text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Sair
                            </a>
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

                                <h6 class="mt-4 mb-3">Atividade Recente</h6>
                                <div class="list-group">
                                    <?php
                                    $stmt = $conn->query("
                                        SELECT d.*, u.nome as usuario_nome 
                                        FROM doacoes d 
                                        JOIN usuarios u ON d.usuario_id = u.id 
                                        ORDER BY d.data_registro DESC 
                                        LIMIT 5
                                    ");
                                    $doacoes_recentes = $stmt->fetchAll();
                                    
                                    if(empty($doacoes_recentes)): ?>
                                        <div class="list-group-item text-center text-muted py-3">
                                            Nenhuma atividade recente
                                        </div>
                                    <?php else: 
                                        foreach($doacoes_recentes as $doacao): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-1">Nova doação de <?php echo $doacao['usuario_nome']; ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y', strtotime($doacao['data_registro'])); ?>
                                                    </small>
                                                </div>
                                                <p class="mb-1">Tipo: <?php echo ucfirst($doacao['tipo']); ?></p>
                                                <p class="mb-0 text-muted"><?php echo $doacao['descricao']; ?></p>
                                            </div>
                                        <?php endforeach;
                                    endif; ?>
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
                                                    <span class="badge bg-<?php echo $msg['respondido'] ? 'success' : 'warning'; ?> status-badge">
                                                        <?php echo $msg['respondido'] ? 'Respondido' : 'Aguardando resposta'; ?>
                                                    </span>
                                                    <?php if(!$msg['respondido']): ?>
                                                        <button class="btn btn-sm btn-success ms-2" onclick="responderMensagem(<?php echo $msg['id']; ?>)">
                                                            Responder
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="perfil">
                        <div class="card">
                            <div class="card-header border-bottom-success">
                                <h5 class="mb-0">Meu Perfil</h5>
                            </div>
                            <div class="card-body">
                                <form id="editarPerfilForm">
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-center">
                                            <div class="position-relative">
                                                <img src="<?php echo !empty($usuario['foto']) ? '../'.$usuario['foto'] : '../img/default-avatar.png'; ?>" 
                                                     alt="Foto de Perfil" 
                                                     class="profile-image mb-3" 
                                                     id="previewFoto2">
                                                <label for="inputFoto" class="btn btn-sm btn-success position-absolute bottom-0 end-0">
                                                    <i class="fas fa-camera"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label class="form-label">Nome</label>
                                                <input type="text" class="form-control" name="nome" 
                                                       value="<?php echo $usuario['nome']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" value="<?php echo $usuario['email']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Telefone</label>
                                                <input type="tel" class="form-control" name="telefone" 
                                                       value="<?php echo $usuario['telefone']; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <h6 class="mb-3">Alterar Senha</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Senha Atual</label>
                                                <input type="password" class="form-control" name="senha_atual">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Nova Senha</label>
                                                <input type="password" class="form-control" name="nova_senha">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Confirmar Senha</label>
                                                <input type="password" class="form-control" name="confirma_senha">
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Salvar Alterações
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="locais">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Locais de Doação</h5>
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#novoLocalModal">
                                    <i class="fas fa-plus"></i> Novo Local
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Cidade</th>
                                                <th>Tipos de Doação</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $stmt = $conn->query("SELECT * FROM locais_doacao ORDER BY nome");
                                            while($local = $stmt->fetch()):
                                            ?>
                                            <tr>
                                                <td><?php echo $local['nome']; ?></td>
                                                <td><?php echo $local['cidade']; ?></td>
                                                <td><?php echo $local['tipos_doacao']; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $local['status'] == 'ativo' ? 'success' : 'danger'; ?>">
                                                        <?php echo ucfirst($local['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="editarLocal(<?php echo $local['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="excluirLocal(<?php echo $local['id']; ?>)">
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

                    <div class="modal fade" id="novoLocalModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Novo Local de Doação</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="formLocal">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nome</label>
                                                <input type="text" class="form-control" name="nome" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">CEP</label>
                                                <input type="text" class="form-control" name="cep" id="cepLocal" maxlength="8" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Endereço</label>
                                                <input type="text" class="form-control" name="endereco" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Cidade</label>
                                                <input type="text" class="form-control" name="cidade" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Estado</label>
                                                <input type="text" class="form-control" name="estado" maxlength="2" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Telefone</label>
                                                <input type="tel" class="form-control" name="telefone">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tipos de Doação Aceitos</label>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="tipos_doacao[]" value="roupas">
                                                <label class="form-check-label">Roupas</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="tipos_doacao[]" value="alimentos">
                                                <label class="form-check-label">Alimentos</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="tipos_doacao[]" value="moveis">
                                                <label class="form-check-label">Móveis</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="tipos_doacao[]" value="eletronicos">
                                                <label class="form-check-label">Eletrônicos</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Horário de Funcionamento</label>
                                            <textarea class="form-control" name="horario_funcionamento" rows="3"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-success" onclick="salvarLocal()">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="responderMensagemModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Responder Mensagem</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="formResposta">
                                        <input type="hidden" name="mensagem_id" id="mensagem_id">
                                        <div class="mb-3">
                                            <label class="form-label">Mensagem Original</label>
                                            <div id="mensagemOriginal" class="form-control bg-light"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Sua Resposta</label>
                                            <textarea class="form-control" name="resposta" rows="5" required></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-success" onclick="enviarResposta()">Enviar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer2.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Upload de foto
        document.getElementById('inputFoto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('O arquivo é muito grande. Tamanho máximo permitido: 5MB');
                    return;
                }

                if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
                    alert('Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewFoto').src = e.target.result;
                    document.getElementById('previewFoto2').src = e.target.result;
                }
                reader.readAsDataURL(file);
                
                const formData = new FormData();
                formData.append('foto', file);
                
                fetch('../processar_foto.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Foto atualizada com sucesso!');
                    } else {
                        alert(data.message || 'Erro ao atualizar foto');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao fazer upload da foto');
                });
            }
        });


        document.getElementById('editarPerfilForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../atualizar_perfil.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Perfil atualizado com sucesso!');
                    location.reload();
                } else {
                    alert(data.message || 'Erro ao atualizar perfil');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao atualizar perfil');
            });
        });

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
            fetch(`buscar_mensagem.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('mensagem_id').value = id;
                    document.getElementById('mensagemOriginal').textContent = data.mensagem;
                    new bootstrap.Modal(document.getElementById('responderMensagemModal')).show();
                });
        }

        function enviarResposta() {
            const form = document.getElementById('formResposta');
            const formData = new FormData(form);
            
            fetch('processar_resposta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Resposta enviada com sucesso!');
                    location.reload();
                } else {
                    alert(data.message || 'Erro ao enviar resposta');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao enviar resposta');
            });
        }

        function salvarLocal() {
            const form = document.getElementById('formLocal');
            const formData = new FormData(form);
            
            fetch('processar_local.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Local salvo com sucesso!');
                    location.reload();
                } else {
                    alert(data.message || 'Erro ao salvar local');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao salvar local');
            });
        }

        document.getElementById('cepLocal').addEventListener('blur', async function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                try {
                    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                    const data = await response.json();
                    if (!data.erro) {
                        document.querySelector('[name="endereco"]').value = data.logradouro;
                        document.querySelector('[name="cidade"]').value = data.localidade;
                        document.querySelector('[name="estado"]').value = data.uf;
                    }
                } catch (error) {
                    console.error('Erro ao buscar CEP:', error);
                }
            }
        });
    </script>
</body>
</html>
