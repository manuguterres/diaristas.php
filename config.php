<?php
declare(strict_types=1);
session_start();

$dbFile = __DIR__ . '/database.db';
$firstRun = !file_exists($dbFile);
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('PRAGMA foreign_keys = ON');
} catch (Exception $e) {
    die("DB Error: " . $e->getMessage());
}

if ($firstRun) {
    $db->exec(<<<'SQL'
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT UNIQUE NOT NULL,
  telefone TEXT,
  password_hash TEXT NOT NULL,
  role TEXT NOT NULL CHECK(role IN ('CLIENTE','DIARISTA')),
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE cleaner_profiles (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER UNIQUE NOT NULL,
  bio TEXT,
  preco_hora REAL DEFAULT 0,
  bairros TEXT,
  disponibilidade TEXT,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE service_requests (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  cliente_id INTEGER NOT NULL,
  diarista_id INTEGER NOT NULL,
  data TEXT,
  hora_inicio TEXT,
  horas REAL DEFAULT 2.0,
  preco_total REAL DEFAULT 0.0,
  status TEXT DEFAULT 'PENDENTE',
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(cliente_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(diarista_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE payments (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  request_id INTEGER NOT NULL,
  status TEXT,
  valor REAL,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(request_id) REFERENCES service_requests(id) ON DELETE CASCADE
);
CREATE TABLE reviews (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  request_id INTEGER NOT NULL,
  rating INTEGER,
  comentario TEXT,
  autor_id INTEGER,
  criado_em TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(request_id) REFERENCES service_requests(id) ON DELETE CASCADE,
  FOREIGN KEY(autor_id) REFERENCES users(id) ON DELETE CASCADE
);
SQL
    );
  // garantir que o arquivo seja gravável
    @chmod($dbFile, 0666);
}

// Funções auxiliares
function is_logged(): bool {
    return !empty($_SESSION['user_id']);
}
function current_user(PDO $db) {
    if (!is_logged()) return null;
    $stmt = $db->prepare("SELECT id,name,email,role FROM users WHERE id=?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function flash_set($msg) {
    $_SESSION['flash'] = $msg;
}
function flash_get() {
    $m = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $m;
}
