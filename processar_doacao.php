<?php
session_start();
require_once 'config/conexao.php';
require_once 'config/auth.php';

verificarLogin();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        
        $cep = htmlspecialchars(trim($_POST['cep']));
        $tipo = htmlspecialchars(trim($_POST['tipo']));
        $descricao = htmlspecialchars(trim($_POST['descricao']));
        
       
        if(strlen($cep) !== 8 || !is_numeric($cep)) {
            throw new Exception('CEP inválido');
        }

      
        $tipos_permitidos = ['roupas', 'alimentos', 'moveis', 'outros'];
        if(!in_array($tipo, $tipos_permitidos)) {
            throw new Exception('Tipo de doação inválido');
        }

        if(empty($descricao)) {
            throw new Exception('A descrição é obrigatória');
        }
        
        $stmt = $conn->prepare("
            INSERT INTO doacoes (usuario_id, cep, tipo, descricao) 
            VALUES (?, ?, ?, ?)
        ");
        
        if($stmt->execute([$_SESSION['usuario_id'], $cep, $tipo, $descricao])) {
            $_SESSION['mensagem'] = 'Doação registrada com sucesso!';
            $_SESSION['tipo_mensagem'] = 'success';
        } else {
            throw new Exception('Erro ao registrar doação no banco de dados');
        }
        
    } catch(Exception $e) {
        $_SESSION['mensagem'] = $e->getMessage();
        $_SESSION['tipo_mensagem'] = 'danger';
    }
    
    header('Location: area-usuario.php');
    exit;
}
