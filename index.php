<?php
require_once "config.php";
if (is_logged()) header("Location: dashboard.php");
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email && $password) {
        $stmt = $db->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($u && password_verify($password, $u['password_hash'])) {
            $_SESSION['user_id'] = $u['id'];
            header("Location: dashboard.php"); exit;
        } else {
            $error = "E-mail ou senha inválidos";
        }
    } else {
        $error = "Preencha os campos";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Diaristas — Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container"><a class="navbar-brand" href="index.php">Diaristas</a></div>
</nav>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-body">
          <h4 class="card-title">Entrar</h4>
          <?php if ($error): ?>
            <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
          <?php endif; ?>
          <?php if ($m=flash_get()): ?>
            <div class="alert alert-success"><?=htmlspecialchars($m)?></div>
          <?php endif; ?>
          <form method="post">
            <div class="mb-3"><label>Email</label><input name="email" type="email" class="form-control" required></div>
            <div class="mb-3"><label>Senha</label><input name="password" type="password" class="form-control" required></div>
            <button class="btn btn-primary w-100">Entrar</button>
          </form>
          <hr>
          <p class="text-center">Ainda não tem conta? <a href="register.php">Criar conta</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
