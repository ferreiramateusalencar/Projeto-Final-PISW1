<?php
require_once __DIR__ . '/../config/database.php';

$pdo = conectarBanco();

try {
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        throw new Exception('Reserva inválida.');
    }

    $stmt = $pdo->prepare('UPDATE reservas SET status = "cancelada" WHERE id = :id AND status = "ativa"');
    $stmt->execute([':id' => $id]);

    header('Location: reservas.php?tipo=sucesso&mensagem=Reserva cancelada com sucesso.');
    exit;
} catch (Throwable $erro) {
    registrarErro($erro);
    header('Location: reservas.php?tipo=erro&mensagem=Não foi possível cancelar a reserva.');
    exit;
}
