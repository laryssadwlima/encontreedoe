<?php
session_start();
require_once 'config/conexao.php';
require_once 'config/auth.php';

if (!file_exists('uploads/fotos/')) {
    mkdir('uploads/fotos/', 0777, true);
}
verificarLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto'])) {
    $response = ['success' => false, 'message' => ''];
    
    try {
        $arquivo = $_FILES['foto'];
        
    
        if ($arquivo['error'] !== 0) {
            throw new Exception('Erro no upload do arquivo');
        }
        
        
        $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($arquivo['type'], $tipos_permitidos)) {
            throw new Exception('Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF');
        }
        
      
        if ($arquivo['size'] > 5 * 1024 * 1024) {
            throw new Exception('Arquivo muito grande. Tamanho máximo: 5MB');
        }
        
        $diretorio = 'uploads/fotos/';
        if (!file_exists($diretorio)) {
            mkdir($diretorio, 0777, true);
        }
        
      
        $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $nome_arquivo = uniqid('foto_') . '.' . $extensao;
        $caminho_arquivo = $diretorio . $nome_arquivo;
        
        
        if (move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
            
            $stmt = $conn->prepare("SELECT foto FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['usuario_id']]);
            $foto_antiga = $stmt->fetch()['foto'];
            
            if ($foto_antiga && file_exists($foto_antiga)) {
                unlink($foto_antiga);
            }
            
          
            $stmt = $conn->prepare("UPDATE usuarios SET foto = ? WHERE id = ?");
            if ($stmt->execute([$caminho_arquivo, $_SESSION['usuario_id']])) {
                $response['success'] = true;
                $response['message'] = 'Foto atualizada com sucesso!';
                $response['foto_url'] = $caminho_arquivo;
            } else {
                throw new Exception('Erro ao atualizar foto no banco de dados');
            }
        } else {
            throw new Exception('Erro ao mover arquivo');
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
