<?php
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/logger.php';

carregarEnv(__DIR__ . '/../.env');

function conectarBanco(): PDO
{
    $host = env('DB_HOST');
    $dbname = env('DB_NAME');
    $usuario = env('DB_USER');
    $senha = env('DB_PASS', '');
    $charset = env('DB_CHARSET', 'utf8mb4');

    $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

    try {
        return new PDO($dsn, $usuario, $senha, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    } catch (PDOException $erro) {
        registrarErro($erro);
        die('Não foi possível conectar ao banco de dados. Verifique as configurações.');
    }
}
