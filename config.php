<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "aniverssariantes";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
// Define charset para evitar problemas com acentuação
$conn->set_charset('utf8mb4');
// Define fuso horário padrão para o sistema
date_default_timezone_set('America/Sao_Paulo');
// Ajusta o fuso horário da sessão MySQL para São Paulo (garante CURDATE/NOW corretos)
$conn->query("SET time_zone = '-03:00'");
?>
