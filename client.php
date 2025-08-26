<?php
require_once "config.php";
if (!is_logged()) header("Location: index.php");
$user = current_user($db);
$flash = flash_get();

// busca simples por bairro
$q_bairro = trim($_GET['bairro'] ?? '');
$params = [];
$sql = "SELECT p.*, u.name as nome, u.email, u.telefone FROM cleaner_profiles p JOIN users u ON u.id = p.user_id WHERE 1=1";
if ($q_bairro) { $sql .= " AND LOWER(p.bairros) LIKE ?"; $params[] = "%".strtolower($q_bairro)."%"; }
$sql .= " ORDER BY p.preco_hora ASC";
$stmt = $db->prepare($sql); $stmt->execute($params);
$cleaners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// obtém meus pedidos
$stmt2 = $db->prepare("SELECT * FROM service_requests WHERE cliente_id=? ORDER BY created_at DESC");
$stmt2->execute([$user['id']]); $myreqs = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Painel Cliente</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container">
    <a class="navbar-brand" href="client.php">Cliente: <?=htmlspecialchars($user['name'])?></a>
    <div class="ms-auto">
      <a class="btn btn-outline-light" href="logout.php">Sair</a>
    </div>
  </div>
</nav>
<div class="container py-4">
  <?php if ($flash) echo "<div class='alert alert-success'>".htmlspecialchars($flash)."</div>"; ?>
  <div class="row">
    <div class="col-md-7">
      <div class="card shadow mb-3">
        <div class="card-body">
          <h5>Buscar Diaristas</h5>
          <form class="row g-2" method="get">
            <div class="col"><input name="bairro" value="<?=htmlspecialchars($q_bairro)?>" class="form-control" placeholder="Bairro"></div>
            <div class="col-auto"><button class="btn btn-primary">Buscar</button></div>
          </form>
        </div>
      </div>
      <?php foreach ($cleaners as $c): ?>
        <div class="card shadow">
          <div class="card-body">
            <h5><?=htmlspecialchars($c['nome'])?> <span class="small-muted">R$ <?=number_format($c['preco_hora'],2,',','.')?>/h</span></h5>
            <p><?=nl2br(htmlspecialchars($c['bio']))?></p>
            <p class="small-muted">Bairros: <?=htmlspecialchars($c['bairros'])?></p>
            <p class="small-muted">Telefone: <?=htmlspecialchars($c['telefone'])?></p>
            <a class="btn btn-sm btn-success" href="agendar.php?diarista_id=<?=$c['user_id']?>">Agendar</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="col-md-5">
      <div class="card shadow mb-3"><div class="card-body"><h5>Meus Pedidos</h5>
        <?php if (empty($myreqs)) echo "<p>Nenhum pedido ainda</p>"; ?>
        <?php foreach ($myreqs as $r): ?>
          <div class="border p-2 mb-2">
            <strong>ID <?=$r['id']?></strong><br>
            Data: <?=$r['data']?> <?=$r['hora_inicio']?><br>
            Horas: <?=$r['horas']?> — Preço: R$ <?=number_format($r['preco_total'],2,',','.')?><br>
            Status: <?=$r['status']?><br>
            <?php if ($r['status']==='ACEITO'): ?>
              <form method="post" action="client.php" class="mt-2">
                <input type="hidden" name="pay_id" value="<?=$r['id']?>">
                <button class="btn btn-sm btn-primary">Pagar (simulado)</button>
              </form>
            <?php endif; ?>
            <?php if ($r['status']==='CONCLUIDO'): ?>
              <a class="btn btn-sm btn-outline-primary mt-2" href="avaliar.php?request_id=<?=$r['id']?>">Avaliar</a>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div></div>
    </div>
  </div>
</div>
</body>
</html>
<?php
// processa o formulário de pagamento (post simples)
if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['pay_id'])) {
    $id = (int)$_POST['pay_id'];
  // verifica propriedade e status
    $s = $db->prepare("SELECT * FROM service_requests WHERE id=?");
    $s->execute([$id]); $req = $s->fetch(PDO::FETCH_ASSOC);
    if ($req && (int)$req['cliente_id']==(int)$user['id'] && $req['status']==='ACEITO') {
        $ins = $db->prepare("INSERT INTO payments (request_id,status,valor) VALUES (?, 'SIMULADO_OK', ?)");
        $ins->execute([$id, $req['preco_total']]);
        $upd = $db->prepare("UPDATE service_requests SET status='CONCLUIDO' WHERE id=?"); $upd->execute([$id]);
        flash_set("Pagamento simulado realizado e pedido concluído.");
        header("Location: client.php"); exit;
    } else {
        flash_set("Não foi possível processar o pagamento.");
        header("Location: client.php"); exit;
    }
}
