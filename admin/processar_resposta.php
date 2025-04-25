<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';

verificarAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $mensagem_id = $_POST['mensagem_id'];
        $resposta = $_POST['resposta'];

        
        $stmt = $conn->prepare("SELECT email, assunto FROM contatos WHERE id = ?");
        $stmt->execute([$mensagem_id]);
        $contato = $stmt->fetch();

        
        $to = $contato['email'];
        $subject = "Re: " . $contato['assunto'];
        $headers = "From: seu-email@dominio.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        mail($to, $subject, $resposta, $headers);

        
        $stmt = $conn->prepare("UPDATE contatos SET respondido = 1 WHERE id = ?");
        $stmt->execute([$mensagem_id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
