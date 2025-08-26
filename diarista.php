<?php
require_once "config.php";
if (!is_logged()) header("Location: index.php");
$user = current_user($db);
if ($user['role']!=='DIARISTA') { header("Location: client.php"); exit; }
$flash = flash_get();

// processa formulário de criação/atualização de perfil
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save_profile'])) {
    $bio = $_POST['bio'] ?? '';
    $preco = (float)($_POST['preco_hora'] ?? 0);
    $bairros = $_POST['bairros'] ?? '';
  // inserir ou atualizar
    $sel = $db->prepare("SELECT id FROM cleaner_profiles WHERE user_id=?");
    $sel->execute([$user['id']]); $row = $sel->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $upd = $db->prepare("UPDATE cleaner_profiles SET bio=?, preco_hora=?, bairros=? WHERE user_id=?");
        $upd->execute([$bio,$preco,$bairros,$user['id']]);
    } else {
        $ins = $db->prepare("INSERT INTO cleaner_profiles (user_id,bio,preco_hora,bairros) VALUES (?, ?, ?, ?)");
        $ins->execute([$user['id'],$bio,$preco,$bairros]);
    }
    flash_set("Perfil salvo");
    header("Location: diarista.php"); exit;
}

// busca perfil e solicitações recebidas
$profileStmt = $db->prepare("SELECT * FROM cleaner_profiles WHERE user_id=?"); $profileStmt->execute([$user['id']]);
$profile = $profileStmt->fetch(PDO::FETCH_ASSOC);

$reqStmt = $db->prepare("SELECT r.*, u.name as cliente_name, u.telefone as cliente_telefone FROM service_requests r JOIN users u ON u.id=r.cliente_id WHERE r.diarista_id=? ORDER BY r.created_at DESC");
$reqStmt->execute([$user['id']]); $reqs = $reqStmt->fetchAll(PDO::FETCH_ASSOC);

// processa ações de aceitar/recusar via GET para simplicidade
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action']; $id = (int)$_GET['id'];
    $sel = $db->prepare("SELECT * FROM service_requests WHERE id=?"); $sel->execute([$id]); $rq = $sel->fetch(PDO::FETCH_ASSOC);
    if ($rq && (int)$rq['diarista_id']==(int)$user['id']) {
        if ($action==='accept') { $u = $db->prepare("UPDATE service_requests SET status='ACEITO' WHERE id=?"); $u->execute([$id]); flash_set("Pedido aceito"); header("Location: diarista.php"); exit; }
        if ($action==='reject') { $u = $db->prepare("UPDATE service_requests SET status='RECUSADO' WHERE id=?"); $u->execute([$id]); flash_set("Pedido recusado"); header("Location: diarista.php"); exit; }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Painel Diarista</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet"></head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container">
    <a class="navbar-brand" href="diarista.php">Diarista: <?=htmlspecialchars($user['name'])?></a>
    <div class="ms-auto">
      <a class="btn btn-outline-light" href="logout.php">Sair</a>
    </div>
  </div>
</nav>
<div class="container py-4">
  <?php if ($flash) echo "<div class='alert alert-success'>".htmlspecialchars($flash)."</div>"; ?>
  <div class="row">
    <div class="col-md-6">
      <!-- Exemplo de exibição do telefone do cliente nas solicitações -->
      <?php if (!empty($reqs)): ?>
        <div class="mb-3">
          <h5>Solicitações Recebidas</h5>
          <?php foreach ($reqs as $r): ?>
            <div class="border rounded p-2 mb-2">
              <div><b>Cliente:</b> <?=htmlspecialchars($r['cliente_name'])?></div>
              <div><b>Telefone:</b> <?=htmlspecialchars($r['cliente_telefone'])?></div>
              <div><b>Status:</b> <?=htmlspecialchars($r['status'])?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <div class="card shadow mb-3"><div class="card-body">
        <h5>Meu Perfil</h5>
        <form method="post">
          <div class="mb-3"><label>Bio</label><textarea name="bio" class="form-control"><?=htmlspecialchars($profile['bio'] ?? '')?></textarea></div>
          <div class="mb-3"><label>Preço por hora (R$)</label><input name="preco_hora" class="form-control" value="<?=htmlspecialchars($profile['preco_hora'] ?? '')?>"></div>
          <div class="mb-3"><label>Bairros (CSV)</label><input name="bairros" class="form-control" value="<?=htmlspecialchars($profile['bairros'] ?? '')?>"></div>
          <button name="save_profile" class="btn btn-success">Salvar Perfil</button>
        </form>
      </div></div>
    </div>
    <div class="col-md-6">
      <div class="card shadow mb-3"><div class="card-body">
        <h5>Pedidos Recebidos</h5>
        <?php if (empty($reqs)) echo "<p>Nenhum pedido</p>"; ?>
        <?php foreach ($reqs as $r): ?>
          <div class="border p-2 mb-2">
            <strong>ID <?=$r['id']?></strong><br>
            Cliente: <?=htmlspecialchars($r['cliente_name'])?><br>
            Data: <?=$r['data']?> <?=$r['hora_inicio']?><br>
            Horas: <?=$r['horas']?> — Preço: R$ <?=number_format($r['preco_total'],2,',','.')?><br>
            Status: <?=$r['status']?><br>
            <?php if ($r['status']==='PENDENTE'): ?>
              <a class="btn btn-sm btn-success" href="diarista.php?action=accept&id=<?=$r['id']?>">Aceitar</a>
              <a class="btn btn-sm btn-danger" href="diarista.php?action=reject&id=<?=$r['id']?>">Recusar</a>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div></div>
    </div>
  </div>
</div>
</body></html>
