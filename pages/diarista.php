<?php
require_once '../config/database.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'diarista') header("Location: ../index.php");
$diarista_id = $_SESSION['usuario_id'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Painel - CasaLimpa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="app-header purple">Profissional</header>

    <main class="container">
        <div class="page-header"><h2>Solicitações</h2></div>

        <div class="cards-grid mb-20">
            <?php
            $stmt = $pdo->prepare("SELECT a.*, u.nome as cliente_nome, u.endereco, u.cidade FROM agendamentos a JOIN usuarios u ON a.cliente_id = u.id WHERE a.diarista_id = ? AND a.status = 'Pendente'");
            $stmt->execute([$diarista_id]);
            $pendentes = $stmt->fetchAll();

            foreach ($pendentes as $p): ?>
                <div class="card" style="border-left: 4px solid var(--warning-text);">
                    <div class="card-header">
                        <h4 style="font-size: 15px;"><?= htmlspecialchars($p['cliente_nome']) ?></h4>
                        <span class="badge badge-warning">Pendente</span>
                    </div>
                    <div style="background: var(--bg-color); padding: 12px; border-radius: 8px; margin-bottom: 12px;">
                        <p style="font-size: 13px; margin-bottom: 4px;">📅 <?= date('d/m/y', strtotime($p['data_servico'])) ?> às <?= $p['horario'] ?></p>
                        <p style="font-size: 13px; color: var(--text-muted);">📍 <?= htmlspecialchars($p['endereco']) ?> - <?= htmlspecialchars($p['cidade']) ?></p>
                    </div>
                    <form action="../actions/gerenciar.php" method="POST" class="flex gap-10">
                        <input type="hidden" name="acao" value="responder_agendamento">
                        <input type="hidden" name="agendamento_id" value="<?= $p['id'] ?>">
                        <button type="submit" name="resposta" value="Confirmado" class="btn btn-success" style="flex:1;">Aceitar</button>
                        <button type="submit" name="resposta" value="Recusado" class="btn btn-danger" style="flex:1;">Recusar</button>
                    </form>
                </div>
            <?php endforeach; if(!$pendentes) echo "<p class='text-muted text-center'>Nenhum pedido novo.</p>"; ?>
        </div>

        <h2 style="font-size: 22px; font-weight: 700; margin: 30px 0 20px 0;">Confirmados</h2>
        <div class="cards-grid">
            <?php
            $stmt = $pdo->prepare("SELECT a.*, u.nome as cliente_nome, u.endereco, u.cidade FROM agendamentos a JOIN usuarios u ON a.cliente_id = u.id WHERE a.diarista_id = ? AND a.status = 'Confirmado' ORDER BY a.data_servico ASC");
            $stmt->execute([$diarista_id]);
            $confirmados = $stmt->fetchAll();

            foreach ($confirmados as $c): ?>
                <div class="card" style="border-left: 4px solid var(--success-text);">
                    <div class="card-header">
                        <h4 class="text-purple"><?= date('d/m/y', strtotime($c['data_servico'])) ?> às <?= $c['horario'] ?></h4>
                        <span class="badge badge-success">Agendado</span>
                    </div>
                    <p style="font-size: 14px; margin-bottom: 4px;"><strong>Cliente:</strong> <?= htmlspecialchars($c['cliente_nome']) ?></p>
                    <p style="font-size: 13px; color: var(--text-muted);">📍 <?= htmlspecialchars($c['endereco']) ?> - <?= htmlspecialchars($c['cidade']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <nav class="bottom-nav">
        <a href="diarista.php" class="nav-item active purple">
            <span class="nav-icon">🔔</span><span>Solicitações</span>
        </a>
        <a href="diarista_agenda.php" class="nav-item">
            <span class="nav-icon">📅</span><span>Agenda</span>
        </a>
        <a href="diarista_perfil.php" class="nav-item">
            <span class="nav-icon">👤</span><span>Perfil</span>
        </a>
    </nav>
</body>
</html>