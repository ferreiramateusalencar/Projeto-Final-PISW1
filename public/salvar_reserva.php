<?php
require_once __DIR__ . '/../config/database.php';

$pdo = conectarBanco();

try {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $quartoId = (int) ($_POST['quarto_id'] ?? 0);
    $quantidadePessoas = (int) ($_POST['quantidade_pessoas'] ?? 0);
    $dataEntrada = $_POST['data_entrada'] ?? '';
    $dataSaida = $_POST['data_saida'] ?? '';

    if ($nome === '' || $email === '' || $telefone === '' || $quartoId <= 0 || $quantidadePessoas <= 0 || $dataEntrada === '' || $dataSaida === '') {
        throw new Exception('Preencha todos os campos obrigatórios.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Informe um e-mail válido.');
    }

    if ($dataSaida <= $dataEntrada) {
        throw new Exception('A data de saída deve ser maior que a data de entrada.');
    }

    $pdo->beginTransaction();

    // Valida se o quarto existe e se suporta a quantidade de pessoas
    $stmtQuarto = $pdo->prepare('SELECT id, capacidade FROM quartos WHERE id = :id AND ativo = 1');
    $stmtQuarto->execute([':id' => $quartoId]);
    $quarto = $stmtQuarto->fetch();

    if (!$quarto) {
        throw new Exception('Quarto não encontrado.');
    }

    if ($quantidadePessoas > (int) $quarto['capacidade']) {
        throw new Exception('A quantidade de pessoas é maior que a capacidade do quarto.');
    }

    // Verifise se há conflito de reserva no mesmo quarto e período.
    $stmtConflito = $pdo->prepare('
        SELECT COUNT(*) AS total
        FROM reservas
        WHERE quarto_id = :quarto_id
          AND status = "ativa"
          AND (:data_entrada < data_saida AND :data_saida > data_entrada)
    ');
    $stmtConflito->execute([
        ':quarto_id' => $quartoId,
        ':data_entrada' => $dataEntrada,
        ':data_saida' => $dataSaida
    ]);

    $conflito = $stmtConflito->fetch();

    if ((int) $conflito['total'] > 0) {
        throw new Exception('Este quarto já possui reserva ativa para o período informado.');
    }

    // Procura o hóspede pelo e-mail. Se existir, atualiza. Se não existir, cadastra.
    $stmtHospede = $pdo->prepare('SELECT id FROM hospedes WHERE email = :email');
    $stmtHospede->execute([':email' => $email]);
    $hospede = $stmtHospede->fetch();

    if ($hospede) {
        $hospedeId = (int) $hospede['id'];

        $stmtAtualizaHospede = $pdo->prepare('
            UPDATE hospedes
            SET nome = :nome, telefone = :telefone
            WHERE id = :id
        ');
        $stmtAtualizaHospede->execute([
            ':nome' => $nome,
            ':telefone' => $telefone,
            ':id' => $hospedeId
        ]);
    } else {
        $stmtInsereHospede = $pdo->prepare('
            INSERT INTO hospedes (nome, email, telefone)
            VALUES (:nome, :email, :telefone)
        ');
        $stmtInsereHospede->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone
        ]);

        $hospedeId = (int) $pdo->lastInsertId();
    }

    // Cadastro da reserva
    $stmtReserva = $pdo->prepare('
        INSERT INTO reservas (hospede_id, quarto_id, data_entrada, data_saida, quantidade_pessoas)
        VALUES (:hospede_id, :quarto_id, :data_entrada, :data_saida, :quantidade_pessoas)
    ');
    $stmtReserva->execute([
        ':hospede_id' => $hospedeId,
        ':quarto_id' => $quartoId,
        ':data_entrada' => $dataEntrada,
        ':data_saida' => $dataSaida,
        ':quantidade_pessoas' => $quantidadePessoas
    ]);

    $pdo->commit();

    header('Location: index.php?tipo=sucesso&mensagem=Reserva cadastrada com sucesso.');
    exit;
} catch (Throwable $erro) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    registrarErro($erro);

    $mensagem = $erro instanceof Exception
        ? $erro->getMessage()
        : 'Não foi possível concluir a reserva.';

    header('Location: index.php?tipo=erro&mensagem=' . urlencode($mensagem));
    exit;
}
