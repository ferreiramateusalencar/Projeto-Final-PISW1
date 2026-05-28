<?php
require_once __DIR__ . '/../config/database.php';

$pdo = conectarBanco();

$stmt = $pdo->prepare('
    SELECT
        r.id,
        h.nome,
        h.email,
        q.numero,
        q.tipo,
        r.data_entrada,
        r.data_saida,
        r.quantidade_pessoas,
        r.status
    FROM reservas r
    INNER JOIN hospedes h ON h.id = r.hospede_id
    INNER JOIN quartos q ON q.id = r.quarto_id
    ORDER BY r.data_entrada DESC, r.id DESC
');
$stmt->execute();
$reservas = $stmt->fetchAll();

$mensagem = $_GET['mensagem'] ?? '';
$tipoMensagem = $_GET['tipo'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas cadastradas</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <h1>Reservas cadastradas</h1>
        <p>Consulta simples das reservas do hotel</p>
    </header>

    <nav>
        <a href="index.php">Nova reserva</a>
        <a href="reservas.php">Reservas cadastradas</a>
    </nav>

    <?php if ($mensagem): ?>
        <div class="alerta <?php echo htmlspecialchars($tipoMensagem); ?>">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php endif; ?>

    <main class="container" style="grid-template-columns: 1fr;">
        <section class="card">
            <h2>Lista de reservas</h2>

            <table class="tabela">
                <thead>
                    <tr>
                        <th>Hóspede</th>
                        <th>E-mail</th>
                        <th>Quarto</th>
                        <th>Entrada</th>
                        <th>Saída</th>
                        <th>Pessoas</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($reservas) === 0): ?>
                        <tr>
                            <td colspan="8">Nenhuma reserva cadastrada.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reserva['nome']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['email']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['numero'] . ' - ' . $reserva['tipo']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reserva['data_entrada'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reserva['data_saida'])); ?></td>
                            <td><?php echo (int) $reserva['quantidade_pessoas']; ?></td>
                            <td><?php echo htmlspecialchars($reserva['status']); ?></td>
                            <td>
                                <?php if ($reserva['status'] === 'ativa'): ?>
                                    <a class="link-cancelar" href="cancelar_reserva.php?id=<?php echo (int) $reserva['id']; ?>">
                                        Cancelar
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>Projeto final - Sistema de Reservas com PHP, PDO e MySQL</p>
    </footer>
</body>
</html>
