<?php
require_once "config.php";
if (!is_logged()) header("Location: index.php");
$user = current_user($db);
if ($user['role']!=='CLIENTE') { header("Location: diarista.php"); exit; }

$diarista_id = (int)($_GET['diarista_id'] ?? 0);
if (!$diarista_id) { header("Location: client.php"); exit; }

// busca informações do diarista
$sel = $db->prepare("SELECT u.id,u.name,p.preco_hora FROM users u JOIN cleaner_profiles p ON p.user_id=u.id WHERE u.id=?");
$sel->execute([$diarista_id]); $d = $sel->fetch(PDO::FETCH_ASSOC);
if (!$d) { flash_set("Diarista não encontrado"); header("Location: client.php"); exit; }

$error = null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $data = $_POST['data'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $horas = (float)($_POST['horas'] ?? 2.0);
    if (!$data || !$hora) $error = "Preencha data e hora";
    else {
        $preco_total = $horas * (float)$d['preco_hora'];
        $ins = $db->prepare("INSERT INTO service_requests (cliente_id,diarista_id,data,hora_inicio,horas,preco_total,status) VALUES (?, ?, ?, ?, ?, ?, 'PENDENTE')");
        $ins->execute([$user['id'],$d['id'],$data,$hora,$horas,$preco_total]);
        flash_set("Solicitação criada com sucesso");
        header("Location: client.php"); exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Agendar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet"></head><body class="bg-light">
<nav class="navbar navbar-dark bg-primary fixed-top"><div class="container"><a class="navbar-brand" href="client.php">Agendar</a></div></nav>
<div class="container py-4">
  <div class="card shadow"><div class="card-body">
    <h5>Agendar com <?=htmlspecialchars($d['name'])?> — R$ <?=number_format($d['preco_hora'],2,',','.')?>/h</h5>
    <?php if ($error) echo "<div class='alert alert-danger'>".htmlspecialchars($error)."</div>"; ?>
    <form method="post">
      <div class="mb-3"><label>Data</label><input name="data" type="date" class="form-control" required></div>
      <div class="mb-3"><label>Hora</label><input name="hora" type="time" class="form-control" required></div>
      <div class="mb-3"><label>Horas</label><input name="horas" type="number" step="0.5" value="2" class="form-control"></div>
      <button class="btn btn-success">Solicitar</button>
      <a class="btn btn-link" href="client.php">Voltar</a>
    </form>
  </div></div>
</div>
</body></html>
