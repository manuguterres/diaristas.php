<?php
require_once "config.php";
if (is_logged()) header("Location: dashboard.php");
$error = null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $telefone = trim($_POST['telefone'] ?? '');
  $password = $_POST['password'] ?? '';
  $role = ($_POST['role'] ?? 'CLIENTE') === 'DIARISTA' ? 'DIARISTA' : 'CLIENTE';
  if (!$name || !$email || !$telefone || !$password) $error = "Preencha todos os campos";
  if (!$error) {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    try {
      $stmt = $db->prepare("INSERT INTO users (name,email,telefone,password_hash,role) VALUES (?, ?, ?, ?, ?)");
      $stmt->execute([$name, $email, $telefone, $hash, $role]);
      flash_set("Conta criada! Faça login.");
      header("Location: index.php"); exit;
    } catch (Exception $e) {
      $error = "E-mail já cadastrado";
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Criar Conta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container"><a class="navbar-brand" href="index.php">Diaristas</a></div>
</nav>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-body">
          <h4>Criar Conta</h4>
          <?php if ($error) echo "<div class='alert alert-danger'>".htmlspecialchars($error)."</div>"; ?>
          <form method="post">
            <div class="mb-3"><label>Nome</label><input name="name" class="form-control" required></div>
            <div class="mb-3"><label>Email</label><input name="email" type="email" class="form-control" required></div>
            <div class="mb-3"><label>Telefone</label><input name="telefone" type="text" class="form-control" required></div>
            <div class="mb-3"><label>Senha</label><input name="password" type="password" class="form-control" required></div>
            <div class="mb-3">
              <label>Sou</label>
              <select name="role" class="form-select">
                <option value="CLIENTE">Cliente</option>
                <option value="DIARISTA">Diarista</option>
              </select>
            </div>
            <button class="btn btn-success w-100">Criar</button>
          </form>
          <hr>
          <a href="index.php">Voltar ao login</a>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
