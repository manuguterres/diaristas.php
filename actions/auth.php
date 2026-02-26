<?php
require_once '../config/database.php';

$acao = $_POST['acao'] ?? '';

if ($acao === 'login') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_tipo'] = $user['tipo'];
        
        $destino = ($user['tipo'] === 'cliente') ? '../pages/cliente.php' : '../pages/diarista.php';
        header("Location: $destino");
        exit;
    } else {
        echo "<script>alert('E-mail ou senha incorretos!'); window.location='../index.php';</script>";
        exit;
    }
} elseif ($acao === 'cadastrar') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'];
    
    $telefone = $_POST['telefone'] ?? null;
    $endereco = $_POST['endereco'] ?? null;
    $cidade = $_POST['cidade'] ?? null;
    $especialidade = $_POST['especialidade'] ?? null;
    
    $foto = null; $documento = null; $verificado = 0;

    if ($tipo === 'diarista') {
        if (empty($_FILES['foto']['name']) || empty($_FILES['documento']['name'])) {
            echo "<script>alert('Erro: A foto e o documento são obrigatórios!'); window.location='../index.php';</script>";
            exit;
        }
        $dir = '../uploads/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        if ($_FILES['foto']['error'] === 0) {
            $foto = time() . '_foto_' . basename($_FILES['foto']['name']);
            move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $foto);
        }
        if ($_FILES['documento']['error'] === 0) {
            $documento = time() . '_doc_' . basename($_FILES['documento']['name']);
            move_uploaded_file($_FILES['documento']['tmp_name'], $dir . $documento);
        }
        if ($foto && $documento) $verificado = 1;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, foto, documento, verificado, telefone, endereco, cidade, especialidade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senha, $tipo, $foto, $documento, $verificado, $telefone, $endereco, $cidade, $especialidade]);
        echo "<script>alert('Cadastro realizado com sucesso! Faça seu login.'); window.location='../index.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Erro: Este e-mail já está em uso.'); window.location='../index.php';</script>";
        exit;
    }
}
?>