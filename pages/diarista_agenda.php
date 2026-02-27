<?php
require_once '../config/database.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'diarista') header("Location: ../index.php");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Agenda - CasaLimpa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="app-header purple">Profissional</header>

    <main class="container">
        <div class="page-header"><h2>Minha Agenda</h2></div>

        <div class="card mb-20">
            <h4 style="margin-bottom: 12px;">Novo Horário Livre</h4>
            <form action="../actions/gerenciar.php" method="POST" class="flex-col">
                <input type="hidden" name="acao" value="add_disponibilidade">
                <input type="date" name="data_disp" class="input-control" required>
                <input type="time" name="horario" class="input-control" required>
                <button type="submit" class="btn btn-purple">Cadastrar Horário</button>
            </form>
        </div>

        <h3 style="margin-bottom: 16px; font-size: 18px;">Horários Abertos</h3>
        <div class="cards-grid">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM disponibilidades WHERE diarista_id = ? ORDER BY data_disp ASC");
            $stmt->execute([$_SESSION['usuario_id']]);
            $disponibilidades = $stmt->fetchAll();

            foreach ($disponibilidades as $d): ?>
                <div class="card flex" style="justify-content: space-between; align-items: center;">
                    <div>
                        <h4 style="font-size: 15px;"><?= date('d/m/y', strtotime($d['data_disp'])) ?></h4>
                        <p class="text-muted" style="font-size: 13px;">🕒 <?= $d['horario'] ?></p>
                        <span class="badge <?= $d['status'] == 'Livre' ? 'badge-success' : 'badge-warning' ?> mt-10" style="display:inline-block;"><?= $d['status'] ?></span>
                    </div>
                    <?php if($d['status'] == 'Livre'): ?>
                    <form action="../actions/gerenciar.php" method="POST">
                        <input type="hidden" name="acao" value="excluir_disponibilidade">
                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                        <button type="submit" class="btn btn-danger" style="padding: 10px 14px; font-size: 20px;">🗑</button>
                    </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <nav class="bottom-nav">
        <a href="diarista.php" class="nav-item">
            <span class="nav-icon">🔔</span><span>Pedidos</span>
        </a>
        <a href="diarista_agenda.php" class="nav-item active purple">
            <span class="nav-icon">📅</span><span>Agenda</span>
        </a>
        <a href="diarista_perfil.php" class="nav-item">
            <span class="nav-icon">👤</span><span>Perfil</span>
        </a>
    </nav>
</body>
</html>