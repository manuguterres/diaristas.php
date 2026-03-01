<?php
session_start();

$db_path = __DIR__ . '/../casalimpa.sqlite';
$pdo = new PDO("sqlite:" . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "
CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    senha TEXT NOT NULL,
    tipo TEXT NOT NULL,
    especialidade TEXT,
    valor_hora REAL,
    foto TEXT,
    documento TEXT,
    verificado INTEGER DEFAULT 0,
    telefone TEXT,
    endereco TEXT,
    cidade TEXT
);

CREATE TABLE IF NOT EXISTS disponibilidades (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    diarista_id INTEGER,
    data_disp TEXT,
    horario TEXT,
    status TEXT DEFAULT 'Livre',
    FOREIGN KEY(diarista_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS agendamentos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cliente_id INTEGER,
    diarista_id INTEGER,
    data_servico TEXT,
    horario TEXT,
    status TEXT DEFAULT 'Pendente',
    FOREIGN KEY(cliente_id) REFERENCES usuarios(id),
    FOREIGN KEY(diarista_id) REFERENCES usuarios(id)
);
";
$pdo->exec($query);
?>