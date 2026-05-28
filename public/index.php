<?php
require_once __DIR__ . '/../config/database.php';

$pdo = conectarBanco();

$stmt = $pdo->prepare('SELECT id, numero, tipo, preco_diaria, capacidade FROM quartos WHERE ativo = 1 ORDER BY numero');
$stmt->execute();
$quartos = $stmt->fetchAll();

$mensagem = $_GET['mensagem'] ?? '';
$tipoMensagem = $_GET['tipo'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReservaFácil Hotel</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <h1>ReservaFácil Hotel</h1>
        <p>Sistema simples para cadastro de reservas de quartos</p>
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

    <main class="container">
        <section class="card">
            <h2>Nova reserva</h2>
            <p>Preencha os dados abaixo para solicitar a reserva de um quarto.</p>

            <form action="salvar_reserva.php" method="POST">
                <label for="nome">Nome do hóspede</label>
                <input type="text" id="nome" name="nome" required>

                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>

                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" required>

                <label for="quarto_id">Quarto</label>
                <select id="quarto_id" name="quarto_id" required>
                    <option value="">Selecione um quarto</option>
                    <?php foreach ($quartos as $quarto): ?>
                        <option value="<?php echo $quarto['id']; ?>">
                            Quarto <?php echo htmlspecialchars($quarto['numero']); ?> -
                            <?php echo htmlspecialchars($quarto['tipo']); ?> -
                            R$ <?php echo number_format($quarto['preco_diaria'], 2, ',', '.'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="quantidade_pessoas">Quantidade de pessoas</label>
                <input type="number" id="quantidade_pessoas" name="quantidade_pessoas" min="1" required>

                <label for="data_entrada">Data de entrada</label>
                <input type="date" id="data_entrada" name="data_entrada" required>

                <label for="data_saida">Data de saída</label>
                <input type="date" id="data_saida" name="data_saida" required>

                <button type="submit">Confirmar reserva</button>
            </form>
        </section>

        <aside class="card">
            <h3>Quartos disponíveis no sistema</h3>

            <?php foreach ($quartos as $quarto): ?>
                <div class="quarto">
                    <strong>Quarto <?php echo htmlspecialchars($quarto['numero']); ?></strong><br>
                    Tipo: <?php echo htmlspecialchars($quarto['tipo']); ?><br>
                    Capacidade: <?php echo (int) $quarto['capacidade']; ?> pessoa(s)<br>
                    Diária: R$ <?php echo number_format($quarto['preco_diaria'], 2, ',', '.'); ?>
                </div>
            <?php endforeach; ?>
        </aside>
    </main>

    <footer>
        <p>Projeto final - Sistema de Reservas com PHP, PDO e MySQL</p>
    </footer>
</body>
</html>
