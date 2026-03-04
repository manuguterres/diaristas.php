<?php 
require_once 'config/database.php'; 
if(isset($_SESSION['usuario_id'])) {
    header("Location: " . ($_SESSION['usuario_tipo'] === 'cliente' ? 'pages/cliente.php' : 'pages/diarista.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>CasaLimpa - Bem-vindo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card" style="max-height: 95vh; overflow-y: auto;">
            <div class="logo-area" style="margin-bottom: 20px;">
                <h1 style="color: var(--primary); font-size: 28px;">CasaLimpa</h1>
                <p class="text-muted">Conectando lares a profissionais de confiança</p>
            </div>

            <form id="form-login" action="actions/auth.php" method="POST">
                <input type="hidden" name="acao" value="login">
                <div class="input-group">
                    <label>E-mail</label>
                    <input type="email" name="email" class="input-control" placeholder="seu@email.com" required>
                </div>
                <div class="input-group">
                    <label>Senha</label>
                    <input type="password" name="senha" class="input-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 8px;">Entrar na Plataforma</button>
                <a class="toggle-link" onclick="toggleForms()">Não tem uma conta? Cadastre-se</a>
            </form>

            <form id="form-cadastro" action="actions/auth.php" method="POST" enctype="multipart/form-data" style="display: none;">
                <input type="hidden" name="acao" value="cadastrar">
                <input type="hidden" name="tipo" id="input_tipo" value="cliente">

                <div class="type-selector">
                    <button type="button" class="type-btn active" id="btn_cliente" onclick="setTipo('cliente')">Sou Cliente</button>
                    <button type="button" class="type-btn" id="btn_diarista" onclick="setTipo('diarista')">Sou Profissional</button>
                </div>

                <div class="input-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" class="input-control" placeholder="Ex: Ana Paula" required>
                </div>
                <div class="input-group">
                    <label>E-mail</label>
                    <input type="email" name="email" class="input-control" placeholder="seu@email.com" required>
                </div>
                <div class="input-group">
                    <label>Senha</label>
                    <input type="password" name="senha" class="input-control" placeholder="Crie uma senha forte" required>
                </div>

                <div id="campos_cliente" style="display: block;">
                    <div class="input-group">
                        <label>Endereço Completo</label>
                        <input type="text" name="endereco" class="input-control" placeholder="Rua, Número, Bairro">
                    </div>
                    <div class="input-group">
                        <label>Cidade</label>
                        <input type="text" name="cidade" class="input-control" placeholder="Ex: São Paulo, SP">
                    </div>
                </div>

                <div id="campos_diarista" style="display: none;">
                    <div class="input-group">
                        <label>Telefone / WhatsApp</label>
                        <input type="text" name="telefone" class="input-control" placeholder="(00) 00000-0000">
                    </div>
                    <div class="input-group">
                        <label>Especialidades</label>
                        <input type="text" name="especialidade" class="input-control" placeholder="Ex: Limpeza Pesada, Passadoria">
                    </div>

                    <div class="file-upload-wrapper">
                        <p style="font-weight: 600; font-size:14px; margin-bottom:10px;">Documentos Obrigatórios</p>
                        <label style="font-size: 12px; color: #64748b; display:block; margin-bottom:4px;">Sua Foto de Perfil *</label>
                        <input type="file" name="foto" id="input_foto" accept="image/*" style="width: 100%; margin-bottom: 10px;">
                        
                        <label style="font-size: 12px; color: #64748b; display:block; margin-bottom:4px;">Documento Oficial (RG/CNH) *</label>
                        <input type="file" name="documento" id="input_doc" accept="image/*,.pdf" style="width: 100%;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 8px;">Criar Minha Conta</button>
                <a class="toggle-link" onclick="toggleForms()">Já tem conta? Faça login</a>
            </form>
        </div>
    </div>

    <script>
        function toggleForms() {
            const formLogin = document.getElementById('form-login');
            const formCadastro = document.getElementById('form-cadastro');
            if (formLogin.style.display === 'none') {
                formLogin.style.display = 'block'; formCadastro.style.display = 'none';
            } else {
                formLogin.style.display = 'none'; formCadastro.style.display = 'block';
            }
        }

        function setTipo(tipo) {
            document.getElementById('input_tipo').value = tipo;
            const btnCliente = document.getElementById('btn_cliente');
            const btnDiarista = document.getElementById('btn_diarista');
            const camposCliente = document.getElementById('campos_cliente');
            const camposDiarista = document.getElementById('campos_diarista');
            const inputFoto = document.getElementById('input_foto');
            const inputDoc = document.getElementById('input_doc');

            if (tipo === 'cliente') {
                btnCliente.classList.add('active'); btnDiarista.classList.remove('active');
                camposCliente.style.display = 'block'; camposDiarista.style.display = 'none';
                inputFoto.removeAttribute('required'); inputDoc.removeAttribute('required');
            } else {
                btnDiarista.classList.add('active'); btnCliente.classList.remove('active');
                camposCliente.style.display = 'none'; camposDiarista.style.display = 'block';
                inputFoto.setAttribute('required', 'required'); inputDoc.setAttribute('required', 'required');
            }
        }
    </script>
</body>
</html>