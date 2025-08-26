<?php
require_once "config.php";
if (!is_logged()) header("Location: index.php");
$user = current_user($db);
$request_id = (int)($_GET['request_id'] ?? ($_POST['request_id'] ?? 0));
if (!$request_id) { header("Location: client.php"); exit; }

// verifica se a solicitação existe, está concluída e pertence ao usuário
$sel = $db->prepare("SELECT * FROM service_requests WHERE id=?"); $sel->execute([$request_id]); $req = $sel->fetch(PDO::FETCH_ASSOC);
if (!$req || $req['status']!=='CONCLUIDO' || (int)$req['cliente_id'] !== (int)$user['id']) { flash_set("Pedido não disponível para avaliação"); header("Location: client.php"); exit; }

$error = null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $rating = max(1,min(5,(int)($_POST['rating'] ?? 5)));
    $comentario = $_POST['comentario'] ?? '';
    $ins = $db->prepare("INSERT INTO reviews (request_id,rating,comentario,autor_id) VALUES (?, ?, ?, ?)");
    $ins->execute([$request_id,$rating,$comentario,$user['id']]);
    flash_set("Avaliação registrada. Obrigado!");
    header("Location: client.php"); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Avaliar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet"></head><body class="bg-light">
<nav class="navbar navbar-dark bg-primary fixed-top"><div class="container"><a class="navbar-brand" href="client.php">Avaliar</a></div></nav>
<div class="container py-4">
  <div class="card shadow"><div class="card-body">
    <h5>Avaliar Serviço — Pedido #<?=$request_id?></h5>
    <form method="post">
      <input type="hidden" name="request_id" value="<?=$request_id?>">
      <div class="mb-3"><label>Nota (1-5)</label><input name="rating" type="number" min="1" max="5" value="5" class="form-control"></div>
      <div class="mb-3"><label>Comentário</label><textarea name="comentario" class="form-control"></textarea></div>
      <button class="btn btn-primary">Enviar Avaliação</button>
      <a class="btn btn-link" href="client.php">Voltar</a>
    </form>
  </div></div>
</div>
</body></html>
