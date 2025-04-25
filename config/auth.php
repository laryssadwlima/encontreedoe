<?php
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit();
    }
}

function verificarAdmin() {
    verificarLogin();
    if ($_SESSION['usuario_tipo'] !== 'admin') {
        header('Location: area-usuario.php');
        exit();
    }
}

function verificarUsuario() {
    verificarLogin();
    if ($_SESSION['usuario_tipo'] === 'admin') {
        header('Location: admin/dashboard.php');
        exit();
    }
}
