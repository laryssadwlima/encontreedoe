<?php
session_start();
require_once 'config/conexao.php';
require_once 'config/auth.php';

verificarUsuario();

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

$stmt = $conn->prepare("SELECT * FROM contatos WHERE email = ? ORDER BY data_envio DESC");
$stmt->execute([$usuario['email']]);
$mensagens = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM doacoes WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$total_doacoes = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT * FROM doacoes WHERE usuario_id = ? ORDER BY data_registro DESC");
$stmt->execute([$_SESSION['usuario_id']]);
$doacoes = $stmt->fetchAll();

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
    <?php include 'includes/head.php'; ?>
    <title>Minha Área - Encontre e Doe</title>
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
    <?php include 'includes/navbar.php'; ?>

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
                            <img src="<?php echo !empty($usuario['foto']) ? $usuario['foto'] : 'img/default-avatar.png'; ?>" 
                                 alt="Foto de Perfil" 
                                 class="profile-image mb-3" 
                                 id="previewFoto">
                            <label for="inputFoto" class="btn btn-sm btn-success position-absolute bottom-0 end-0">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>
                        <h4><?php echo $usuario['nome']; ?></h4>
                        <p class="text-muted"><?php echo $usuario['email']; ?></p>
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
                                    data-bs-target="#doacoes">
                                <i class="fas fa-hand-holding-heart me-2"></i> Minhas Doações
                            </button>
                            <button class="nav-link text-start p-3" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#mensagens">
                                <i class="fas fa-envelope me-2"></i> Mensagens
                            </button>
                            <button class="nav-link text-start p-3" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#perfil">
                                <i class="fas fa-user me-2"></i> Meu Perfil
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
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h3><?php echo $total_doacoes; ?></h3>
                                                <p class="mb-0">Doações Realizadas</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mt-4 mb-3">Atividade Recente</h6>
                                <div class="list-group">
                                    <div class="list-group-item text-center text-muted py-3">
                                        Nenhuma atividade recente
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="mensagens">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Minhas Mensagens</h5>
                                <button class="btn btn-success btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#novaMensagemModal">
                                    <i class="fas fa-plus"></i> Nova Mensagem
                                </button>
                            </div>
                            <div class="card-body">
                                <?php if(empty($mensagens)): ?>
                                    <p class="text-center text-muted py-3">
                                        Você ainda não enviou nenhuma mensagem
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
                                                <p class="mb-1 text-muted"><?php echo substr($msg['mensagem'], 0, 100); ?>...</p>
                                                <div class="mt-2">
                                                    <span class="badge bg-<?php echo $msg['respondido'] ? 'success' : 'warning'; ?> status-badge">
                                                        <?php echo $msg['respondido'] ? 'Respondido' : 'Aguardando resposta'; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="doacoes">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Minhas Doações</h5>
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#novaDoacaoModal">
                                    <i class="fas fa-plus"></i> Registrar Nova Doação
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>CEP</th>
                                                <th>Tipo</th>
                                                <th>Descrição</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($doacoes)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        Nenhuma doação registrada
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach($doacoes as $doacao): ?>
                                                    <tr>
                                                        <td><?php echo date('d/m/Y', strtotime($doacao['data_registro'])); ?></td>
                                                        <td><?php echo $doacao['cep']; ?></td>
                                                        <td>
                                                            <span class="badge bg-primary">
                                                                <?php echo ucfirst($doacao['tipo']); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo $doacao['descricao']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="favoritos">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Instituições Favoritas</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-center text-muted py-3">
                                    Você ainda não adicionou nenhuma instituição aos favoritos
                                </p>
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
                                                <img src="<?php echo !empty($usuario['foto']) ? $usuario['foto'] : 'img/default-avatar.png'; ?>" 
                                                     alt="Foto de Perfil" 
                                                     class="profile-image mb-3" 
                                                     id="previewFoto">
                                                <label for="inputFoto" class="btn btn-sm btn-success position-absolute bottom-0 end-0">
                                                    <i class="fas fa-camera"></i>
                                                </label>
                                            </div>
                                            <input type="file" class="d-none" id="inputFoto" name="foto" accept="image/*">
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
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editarPerfilModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editarPerfilForm">
                        <div class="mb-3">
                            <label class="form-label">Foto de Perfil</label>
                            <input type="file" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" class="form-control" value="<?php echo $usuario['nome']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="tel" class="form-control" value="<?php echo $usuario['telefone']; ?>">
                        </div>
                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="novaMensagemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="novaMensagemForm">
                        <div class="mb-3">
                            <label class="form-label">Assunto</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mensagem</label>
                            <textarea class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Enviar Mensagem</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="novaDoacaoModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Nova Doação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="processar_doacao.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">CEP do Local</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="cep" id="cep" maxlength="8" required>
                                <button type="button" class="btn btn-outline-success" onclick="buscarCep()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div id="endereco-resultado" class="form-text"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Doação</label>
                            <select class="form-select" name="tipo" required>
                                <option value="">Selecione...</option>
                                <option value="roupas">Roupas</option>
                                <option value="alimentos">Alimentos</option>
                                <option value="moveis">Móveis</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição da Doação</label>
                            <textarea class="form-control" name="descricao" rows="3" required 
                                    placeholder="Descreva os itens que você vai doar..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-heart me-2"></i>Registrar Doação
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.getElementById('editarPerfilForm').addEventListener('submit', function(e) {
            e.preventDefault();
        });

        document.getElementById('novaMensagemForm').addEventListener('submit', function(e) {
            e.preventDefault();
        });

        document.getElementById('configForm').addEventListener('submit', function(e) {
            e.preventDefault();
        
        });

    
        function buscarCep() {
            const cep = document.getElementById('cep').value.replace(/\D/g, '');
            const resultado = document.getElementById('endereco-resultado');
            
            if(cep.length !== 8) {
                resultado.innerHTML = 'CEP inválido';
                return;
            }
            
            resultado.innerHTML = 'Buscando...';
            
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if(data.erro) {
                        resultado.innerHTML = 'CEP não encontrado';
                    } else {
                        resultado.innerHTML = `${data.logradouro}, ${data.bairro}, ${data.localidade}/${data.uf}`;
                    }
                })
                .catch(() => {
                    resultado.innerHTML = 'Erro ao buscar CEP';
                });
        }

       
        document.getElementById('novaDoacaoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('processar_doacao.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(() => {
                alert('Erro ao processar solicitação');
            });
        });

       
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
                    const previewFotos = document.querySelectorAll('.profile-image');
                    previewFotos.forEach(img => {
                        img.src = e.target.result;
                    });
                }
                reader.readAsDataURL(file);
                
                
                const formData = new FormData();
                formData.append('foto', file);
                
                
                alert('Enviando foto...');
                
                fetch('processar_foto.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                     
                        const fotosUsuario = document.querySelectorAll('.profile-image');
                        fotosUsuario.forEach(img => {
                            img.src = data.foto_url + '?v=' + new Date().getTime();
                        });
                        
                      
                        alert('Foto atualizada com sucesso!');
                    } else {
                        alert(data.message || 'Erro ao atualizar foto');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao fazer upload da foto. Tente novamente.');
                });
            }
        });

    
        document.getElementById('editarPerfilForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('atualizar_perfil.php', {
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
                alert('Erro ao atualizar perfil. Tente novamente.');
            });
        });
    </script>
</body>
</html>
