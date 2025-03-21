<?php
$host = "82.197.82.29"; // Endereço do servidor MySQL
$dbname = "u489002097_joao"; // Nome do banco de dados
$username = "u489002097_joao"; // Nome de usuário do MySQL
$password = "Eunapolis@2025"; // Sua senha MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Conexão bem-sucedida!";
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
?>
