<?php
require_once '../config/database.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente') header("Location: ../index.php");
$filtro_especialidade = $_GET['especialidade'] ?? '';
$query_sql = "SELECT * FROM usuarios WHERE tipo = 'diarista'";
if ($filtro_especialidade) $query_sql .= " AND especialidade LIKE :esp";
$stmt_diaristas = $pdo->prepare($query_sql);
if ($filtro_especialidade) $stmt_diaristas->bindValue(':esp', '%' . $filtro_especialidade . '%');
$stmt_diaristas->execute();
$diaristas = $stmt_diaristas->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Buscar - CasaLimpa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="app-header blue">CasaLimpa</header>

    <main class="container">
        <div class="page-header">
            <h2>Profissionais</h2>
            <p class="text-muted small-text">Encontre a profissional ideal</p>
        </div>

        <form method="GET" class="flex-col mb-20">
            <input type="text" name="especialidade" class="input-control" placeholder="🔍 Ex: Limpeza Pesada..." value="<?= htmlspecialchars($filtro_especialidade) ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <div class="cards-grid">
            <?php foreach ($diaristas as $d):
                $stmt = $pdo->prepare("SELECT * FROM disponibilidades WHERE diarista_id = ? AND status = 'Livre' ORDER BY data_disp ASC");
                $stmt->execute([$d['id']]);
                $horarios = $stmt->fetchAll();
            ?>
            <div class="card">
                <div class="card-header" style="align-items: center;">
                    <div class="avatar-wrap">
                        <?php if($d['foto']): ?>
                            <img src="../uploads/<?= $d['foto'] ?>" class="avatar-img">
                        <?php else: ?>
                            <div class="avatar-placeholder"><?= strtoupper(substr($d['nome'], 0, 1)) ?></div>
                        <?php endif; ?>
                        <div>
                            <h4 class="text-primary"><?= htmlspecialchars($d['nome']) ?></h4>
                            <?php if($d['verificado']): ?>
                                <span class="badge badge-info" style="font-size:9px;">✓ Verificado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;"><strong>Esp:</strong> <?= htmlspecialchars($d['especialidade'] ?? 'Geral') ?></p>
                
                <?php if (count($horarios) > 0): ?>
                    <form action="../actions/gerenciar.php" method="POST" class="flex-col">
                        <input type="hidden" name="acao" value="agendar">
                        <select name="disponibilidade_id" required class="form-select">
                            <option value="">Escolha um horário...</option>
                            <?php foreach($horarios as $hl): ?>
                                <option value="<?= $hl['id'] ?>"><?= date('d/m/y', strtotime($hl['data_disp'])) ?> às <?= $hl['horario'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">Agendar Serviço</button>
                    </form>
                <?php else: ?>
                    <div style="background: var(--bg-color); padding: 12px; border-radius: 8px; text-align: center;">
                        <p class="text-muted" style="font-size: 13px;">Sem agenda livre</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <nav class="bottom-nav">
        <a href="cliente.php" class="nav-item">
            <span class="nav-icon">📋</span><span>Pedidos</span>
        </a>
        <a href="cliente_buscar.php" class="nav-item active blue">
            <span class="nav-icon">🔍</span><span>Buscar</span>
        </a>
        <a href="cliente_perfil.php" class="nav-item">
            <span class="nav-icon">👤</span><span>Perfil</span>
        </a>
    </nav>
</body>
</html>