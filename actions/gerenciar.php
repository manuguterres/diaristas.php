<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
require_once '../config/database.php';
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

try {
    // FUNÇÕES DE PERFIL
    if ($acao === 'atualizar_foto') {
        $usuario_id = $_SESSION['usuario_id'];
        if (isset($_FILES['nova_foto']) && $_FILES['nova_foto']['error'] === 0) {
            $dir = '../uploads/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $foto = time() . '_foto_' . basename($_FILES['nova_foto']['name']);
            move_uploaded_file($_FILES['nova_foto']['tmp_name'], $dir . $foto);
            $stmt = $pdo->prepare("UPDATE usuarios SET foto = ? WHERE id = ?");
            $stmt->execute([$foto, $usuario_id]);
            echo "<script>alert('Foto atualizada com sucesso!'); window.location='../pages/diarista_perfil.php';</script>";
        }
        exit;

    } elseif ($acao === 'atualizar_senha') {
        $usuario_id = $_SESSION['usuario_id'];
        $senha_atual = $_POST['senha_atual']; $nova_senha = $_POST['nova_senha'];
        $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $user = $stmt->fetch();
        if (password_verify($senha_atual, $user['senha'])) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt_update = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $stmt_update->execute([$senha_hash, $usuario_id]);
            echo "<script>alert('Senha alterada com sucesso!'); window.location='../pages/diarista_perfil.php';</script>";
        } else {
            echo "<script>alert('A senha atual está incorreta!'); window.location='../pages/diarista_perfil.php';</script>";
        }
        exit;

    } elseif ($acao === 'atualizar_perfil_cliente') {
        $usuario_id = $_SESSION['usuario_id'];
        $endereco = $_POST['endereco']; $cidade = $_POST['cidade'];
        $stmt = $pdo->prepare("UPDATE usuarios SET endereco = ?, cidade = ? WHERE id = ?");
        $stmt->execute([$endereco, $cidade, $usuario_id]);
        echo "<script>alert('Endereço atualizado!'); window.location='../pages/cliente_perfil.php';</script>";
        exit;

    } elseif ($acao === 'atualizar_senha_cliente') {
        $usuario_id = $_SESSION['usuario_id'];
        $senha_atual = $_POST['senha_atual']; $nova_senha = $_POST['nova_senha'];
        $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $user = $stmt->fetch();
        if (password_verify($senha_atual, $user['senha'])) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt_update = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $stmt_update->execute([$senha_hash, $usuario_id]);
            echo "<script>alert('Senha alterada com sucesso!'); window.location='../pages/cliente_perfil.php';</script>";
        } else {
            echo "<script>alert('A senha atual está incorreta!'); window.location='../pages/cliente_perfil.php';</script>";
        }
        exit;

    // FUNÇÕES DE AGENDA
    } elseif ($acao === 'add_disponibilidade') {
        $diarista_id = $_SESSION['usuario_id'];
        $data = $_POST['data_disp']; $horario = $_POST['horario'];
        $stmt = $pdo->prepare("INSERT INTO disponibilidades (diarista_id, data_disp, horario) VALUES (?, ?, ?)");
        $stmt->execute([$diarista_id, $data, $horario]);
        header("Location: ../pages/diarista_agenda.php");
        exit; 

    } elseif ($acao === 'excluir_disponibilidade') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM disponibilidades WHERE id = ? AND diarista_id = ? AND status = 'Livre'");
        $stmt->execute([$id, $_SESSION['usuario_id']]);
        header("Location: ../pages/diarista_agenda.php");
        exit;

    } elseif ($acao === 'agendar') {
        $cliente_id = $_SESSION['usuario_id'];
        $disp_id = $_POST['disponibilidade_id'];
        $stmt = $pdo->prepare("SELECT * FROM disponibilidades WHERE id = ?");
        $stmt->execute([$disp_id]);
        $disp = $stmt->fetch();

        if ($disp && $disp['status'] === 'Livre') {
            $stmt = $pdo->prepare("INSERT INTO agendamentos (cliente_id, diarista_id, data_servico, horario) VALUES (?, ?, ?, ?)");
            $stmt->execute([$cliente_id, $disp['diarista_id'], $disp['data_disp'], $disp['horario']]);
            $stmt2 = $pdo->prepare("UPDATE disponibilidades SET status = 'Aguardando' WHERE id = ?");
            $stmt2->execute([$disp_id]);
            echo "<script>alert('Pedido enviado! Aguarde a confirmação do profissional.'); window.location='../pages/cliente.php';</script>";
            exit;
        }

    } elseif ($acao === 'responder_agendamento') {
        $agendamento_id = $_POST['agendamento_id'];
        $novo_status = $_POST['resposta']; 
        $stmt_ag = $pdo->prepare("SELECT * FROM agendamentos WHERE id = ?");
        $stmt_ag->execute([$agendamento_id]);
        $agendamento = $stmt_ag->fetch();

        if ($agendamento) {
            $stmt = $pdo->prepare("UPDATE agendamentos SET status = ? WHERE id = ?");
            $stmt->execute([$novo_status, $agendamento_id]);

            if ($novo_status === 'Recusado') {
                $stmt_disp = $pdo->prepare("UPDATE disponibilidades SET status = 'Livre' WHERE diarista_id = ? AND data_disp = ? AND horario = ?");
                $stmt_disp->execute([$agendamento['diarista_id'], $agendamento['data_servico'], $agendamento['horario']]);
            } elseif ($novo_status === 'Confirmado') {
                $stmt_disp = $pdo->prepare("UPDATE disponibilidades SET status = 'Ocupado' WHERE diarista_id = ? AND data_disp = ? AND horario = ?");
                $stmt_disp->execute([$agendamento['diarista_id'], $agendamento['data_servico'], $agendamento['horario']]);
            }
        }
        header("Location: ../pages/diarista.php");
        exit;
    }
} catch (PDOException $e) { die("Erro Banco: " . $e->getMessage()); } catch (Exception $e) { die("Erro: " . $e->getMessage()); }
?>