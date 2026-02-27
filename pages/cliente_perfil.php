<?php
require_once '../config/database.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente') header("Location: ../index.php");
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$perfil = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Meu Perfil - CasaLimpa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="app-header blue">Meu Perfil</header>

    <main class="container">
        <div class="card mb-20" style="text-align: center; padding: 30px 20px;">
            <div class="avatar-placeholder" style="width: 80px; height: 80px; font-size: 30px; margin: 0 auto 16px auto; background: var(--info-bg); color: var(--primary);">
                <?= strtoupper(substr($perfil['nome'], 0, 1)) ?>
            </div>
            <h2 style="font-size: 20px; margin-bottom: 4px;"><?= htmlspecialchars($perfil['nome']) ?></h2>
            <p class="text-muted" style="font-size: 14px;"><?= htmlspecialchars($perfil['email']) ?></p>
        </div>

        <div class="card mb-20">
            <h4 style="margin-bottom: 12px; font-size: 16px;">Meus Dados de Localização</h4>
            <form action="../actions/gerenciar.php" method="POST" class="flex-col">
                <input type="hidden" name="acao" value="atualizar_perfil_cliente">
                <div class="input-group" style="margin-bottom: 10px;">
                    <label>Endereço Completo</label>
                    <input type="text" name="endereco" class="input-control" value="<?= htmlspecialchars($perfil['endereco'] ?? '') ?>" placeholder="Sua rua, número, bairro..." required>
                </div>
                <div class="input-group" style="margin-bottom: 10px;">
                    <label>Cidade</label>
                    <input type="text" name="cidade" class="input-control" value="<?= htmlspecialchars($perfil['cidade'] ?? '') ?>" placeholder="Ex: São Paulo, SP" required>
                </div>
                <button type="submit" class="btn btn-primary mt-10">Salvar Dados</button>
            </form>
        </div>

        <div class="card mb-20">
            <h4 style="margin-bottom: 12px; font-size: 16px;">Segurança</h4>
            <form action="../actions/gerenciar.php" method="POST" class="flex-col">
                <input type="hidden" name="acao" value="atualizar_senha_cliente">
                <input type="password" name="senha_atual" class="input-control" placeholder="Senha Atual" required>
                <input type="password" name="nova_senha" class="input-control" placeholder="Nova Senha" required>
                <button type="submit" class="btn" style="background: var(--text-muted); color: white;">Alterar Senha</button>
            </form>
        </div>
        
        <a href="../logout.php" class="btn btn-danger" style="margin-top: 10px;">Sair da Conta</a>
    </main>

    <nav class="bottom-nav">
        <a href="cliente.php" class="nav-item">
            <span class="nav-icon">📋</span><span>Pedidos</span>
        </a>
        <a href="cliente_buscar.php" class="nav-item">
            <span class="nav-icon">🔍</span><span>Buscar</span>
        </a>
        <a href="cliente_perfil.php" class="nav-item active blue">
            <span class="nav-icon">👤</span><span>Perfil</span>
        </a>
    </nav>
</body>
</html>