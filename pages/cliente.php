<?php
require_once '../config/database.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente') header("Location: ../index.php");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pedidos - CasaLimpa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="app-header blue">CasaLimpa</header>

    <main class="container">
        <div class="page-header">
            <h2>Agendamentos</h2>
            <p class="text-muted small-text">Acompanhe seus agendamentos</p>
        </div>

        <div class="cards-grid">
            <?php
            $stmt = $pdo->prepare("SELECT a.*, u.nome as diarista_nome FROM agendamentos a JOIN usuarios u ON a.diarista_id = u.id WHERE a.cliente_id = ? ORDER BY a.data_servico DESC");
            $stmt->execute([$_SESSION['usuario_id']]);
            $meus = $stmt->fetchAll();

            foreach ($meus as $m): 
                $status_class = 'warning';
                if ($m['status'] == 'Confirmado') $status_class = 'success';
                if ($m['status'] == 'Recusado') $status_class = 'danger';
            ?>
            <div class="card">
                <div class="card-header">
                    <div>
                        <h4 class="text-primary"><?= date('d/m/Y', strtotime($m['data_servico'])) ?></h4>
                        <p class="text-muted" style="font-size:13px;">Horário: <?= $m['horario'] ?></p>
                    </div>
                    <span class="badge badge-<?= $status_class ?>"><?= $m['status'] ?></span>
                </div>
                <hr class="divider">
                <div class="avatar-wrap">
                    <div class="avatar-placeholder"><?= strtoupper(substr($m['diarista_nome'], 0, 1)) ?></div>
                    <div>
                        <p style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($m['diarista_nome']) ?></p>
                        <p class="text-muted" style="font-size: 12px;">Profissional</p>
                    </div>
                </div>
            </div>
            <?php endforeach; if(!$meus) echo "<p class='text-muted text-center mt-20'>Nenhum agendamento realizado.</p>"; ?>
        </div>
    </main>

    <nav class="bottom-nav">
        <a href="cliente.php" class="nav-item active blue">
            <span class="nav-icon">📋</span><span>Agendamentos</span>
        </a>
        <a href="cliente_buscar.php" class="nav-item">
            <span class="nav-icon">🔍</span><span>Buscar</span>
        </a>
        <a href="cliente_perfil.php" class="nav-item">
            <span class="nav-icon">👤</span><span>Perfil</span>
        </a>
    </nav>
</body>
</html>