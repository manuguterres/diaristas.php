<?php
require_once '../config/database.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'diarista') header("Location: ../index.php");
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$perfil = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Perfil - CasaLimpa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="app-header purple">Meu Perfil</header>

    <main class="container">
        <div class="card mb-20" style="text-align: center; padding: 40px 20px;">
            <?php if($perfil['foto']): ?>
                <img src="../uploads/<?= $perfil['foto'] ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin: 0 auto 16px auto; border: 3px solid var(--purple);">
            <?php else: ?>
                <div class="avatar-placeholder" style="width: 100px; height: 100px; font-size: 36px; margin: 0 auto 16px auto;"><?= strtoupper(substr($perfil['nome'], 0, 1)) ?></div>
            <?php endif; ?>
            
            <h2 style="font-size: 22px; margin-bottom: 4px;"><?= htmlspecialchars($perfil['nome']) ?></h2>
            <p class="text-muted" style="font-size: 14px; margin-bottom: 20px;"><?= htmlspecialchars($perfil['especialidade']) ?></p>

            <?php if($perfil['verificado']): ?>
                <div style="background: var(--info-bg); color: var(--info-text); padding: 12px; border-radius: 12px;">
                    <span style="font-weight: 700; font-size: 14px;">✓ Conta Verificada</span>
                </div>
            <?php else: ?>
                <div style="background: var(--warning-bg); color: var(--warning-text); padding: 12px; border-radius: 12px;">
                    <span style="font-weight: 700; font-size: 14px;">⚠ Verificação Pendente</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="card mb-20">
            <h4 style="margin-bottom: 12px; font-size: 16px;">Atualizar Foto</h4>
            <form action="../actions/gerenciar.php" method="POST" enctype="multipart/form-data" class="flex-col">
                <input type="hidden" name="acao" value="atualizar_foto">
                <input type="file" name="nova_foto" accept="image/*" class="input-control" required>
                <button type="submit" class="btn btn-purple">Salvar Foto</button>
            </form>
        </div>

        <div class="card mb-20">
            <h4 style="margin-bottom: 12px; font-size: 16px;">Segurança</h4>
            <form action="../actions/gerenciar.php" method="POST" class="flex-col">
                <input type="hidden" name="acao" value="atualizar_senha">
                <input type="password" name="senha_atual" class="input-control" placeholder="Senha Atual" required>
                <input type="password" name="nova_senha" class="input-control" placeholder="Nova Senha" required>
                <button type="submit" class="btn btn-primary" style="background: var(--text-muted);">Alterar Senha</button>
            </form>
        </div>
        
        <a href="../logout.php" class="btn btn-danger" style="margin-top: 10px;">Sair da Conta</a>
    </main>

    <nav class="bottom-nav">
        <a href="diarista.php" class="nav-item">
            <span class="nav-icon">🔔</span><span>Pedidos</span>
        </a>
        <a href="diarista_agenda.php" class="nav-item">
            <span class="nav-icon">📅</span><span>Agenda</span>
        </a>
        <a href="diarista_perfil.php" class="nav-item active purple">
            <span class="nav-icon">👤</span><span>Perfil</span>
        </a>
    </nav>
</body>
</html>