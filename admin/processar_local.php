<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';

verificarAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = $_POST['nome'];
        $cep = $_POST['cep'];
        $endereco = $_POST['endereco'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];
        $tipos_doacao = isset($_POST['tipos_doacao']) ? implode(', ', $_POST['tipos_doacao']) : '';
        $horario_funcionamento = $_POST['horario_funcionamento'];

        
        $endereco_completo = urlencode("$endereco, $cidade, $estado, Brasil");
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=$endereco_completo";
        $response = file_get_contents($url);
        $data = json_decode($response);

        $latitude = null;
        $longitude = null;
        if (!empty($data)) {
            $latitude = $data[0]->lat;
            $longitude = $data[0]->lon;
        }

        $stmt = $conn->prepare("
            INSERT INTO locais_doacao 
            (nome, endereco, cidade, estado, cep, telefone, email, tipos_doacao, 
             horario_funcionamento, latitude, longitude) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $nome, $endereco, $cidade, $estado, $cep, $telefone, $email, 
            $tipos_doacao, $horario_funcionamento, $latitude, $longitude
        ]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
