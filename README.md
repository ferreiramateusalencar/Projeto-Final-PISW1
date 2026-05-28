# Projeto-Final-PISW1
Projeto Final da Disciplina de Projeto de Implementação de Sistemas para Web I: O objetivo deste projeto é consolidar os conceitos discutidos na disciplina relacionados a conexão entre aplicações e bancos de dados, segurança, desempenho, transações, observabilidade e boas práticas de engenharia de software.

Co-athor


# ReservaFácil Hotel

Sistema web simples de reservas de quartos, desenvolvido em PHP com PDO e MySQL.

## Requisitos

- PHP 8 ou superior
- MySQL 8 ou superior
- Servidor local, como XAMPP, WAMP ou Laragon

## Como executar

1. Crie o banco executando o arquivo:

```sql
sql/banco.sql
```

2. Copie o arquivo `.env.example` e renomeie para `.env`.

3. Ajuste as credenciais no arquivo `.env` conforme o seu MySQL:

```env
DB_HOST=localhost
DB_NAME=hotel_reservas
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
```

4. Coloque a pasta do projeto dentro do diretório do servidor local, por exemplo:

```text
htdocs/reservafacil_hotel
```

5. Acesse no navegador:

```text
http://localhost/reservafacil_hotel/public/index.php
```

## Funcionalidades

- Cadastro de reserva de quarto;
- Cadastro/atualização de hóspede por e-mail;
- Validação de datas;
- Validação da capacidade do quarto;
- Bloqueio de reserva duplicada para o mesmo quarto e período;
- Listagem de reservas;
- Cancelamento de reservas;
- Uso de transação com commit e rollback;
- Registro de erros em `logs/app.log`.
