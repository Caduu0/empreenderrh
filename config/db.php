<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'empreender_rh');
define('DB_USER', 'root'); // Altere se o usuário for diferente
define('DB_PASS', '');     // Altere se houver senha

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Evita SQL injection

} catch(PDOException $e) {
    // Em produção, registra o erro num log ao invés de na tela
    error_log("Erro de Conexão PDO: " . $e->getMessage());
    die("Falha na conexão com o banco de dados. Tente novamente mais tarde.");
}
?>