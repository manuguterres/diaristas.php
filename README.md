# 🧹 CasaLimpa - Plataforma de Contratação de Serviços Domésticos

Este projeto é um Produto Mínimo Viável (MVP) desenvolvido como Projeto Integrador para o curso de Tecnologia em Análise e Desenvolvimento de Sistemas. A plataforma foi criada para centralizar e facilitar o processo de contratação de serviços domésticos, ligando clientes a profissionais (diaristas) de forma rápida e segura.

# Acesso para Aplicação online

Hospedamos a aplicação na web, para melhor avaliação e uso. https://projeto.astech.site/

Informações de usuário para login e teste: 
Login: teste@teste.com
Senha: teste

## 📱 Funcionalidades (Mobile-First)

A interface foi inteiramente projetada com foco em dispositivos móveis (smartphones), proporcionando uma experiência de navegação fluida e semelhante a uma aplicação nativa.

### Para Clientes
* **Registo e Perfil:** Criação de conta com recolha de endereço e cidade para facilitar a localização dos serviços.
* **Busca Inteligente:** Motor de busca de profissionais filtrado por especialidade (ex: Limpeza Pesada, Passadoria).
* **Agendamento Dinâmico:** Seleção de datas e horários baseada estritamente na disponibilidade real inserida pelo profissional no sistema.
* **Gestão de Pedidos:** Acompanhamento do estado do agendamento (Pendente, Confirmado ou Recusado).

### Para Profissionais (Diaristas)
* **Registo com Verificação:** Upload obrigatório de fotografia de perfil e documento de identificação (RG/CNH) para validação de segurança.
* **Gestão de Agenda:** Inserção e remoção de dias e horários livres. Quando um pedido é recusado, o horário volta automaticamente a ficar disponível.
* **Gestão de Pedidos:** Aprovação ou recusa de solicitações de clientes, com visualização prévia do local do serviço.
* **Dashboard:** Visão geral da agenda confirmada e das novas solicitações recebidas.

## 🛠️ Tecnologias Utilizadas

O projeto foi construído de forma independente e modular, garantindo facilidade de alojamento em qualquer servidor web (como cPanel, CyberPanel ou VPS) sem dependências externas complexas.

* **Front-end:** HTML5, CSS3 (Custom Properties, Flexbox, Grid) e JavaScript Vanilla.
* **Back-end:** PHP 8+ (Lógica de autenticação, upload de ficheiros e gestão de rotas).
* **Base de Dados:** SQLite3 (Ficheiro local leve e autónomo gerado automaticamente via PDO).

## 📁 Estrutura de Diretórios e Ficheiros

```text
casalimpa/
├── actions/
│   ├── auth.php            # Processa o login e registo de utilizadores
│   └── gerenciar.php       # Processa agendamentos, respostas e gestão de perfil/agenda
├── assets/
│   └── css/
│       └── style.css       # Estilos globais (Mobile-First, UI/UX moderna)
├── config/
│   └── database.php        # Conexão PDO e criação automática das tabelas SQLite
├── pages/
│   ├── cliente.php         # Ecrã de pedidos do cliente
│   ├── cliente_buscar.php  # Ecrã de busca de profissionais
│   ├── cliente_perfil.php  # Gestão de dados e segurança do cliente
│   ├── diarista.php        # Dashboard de pedidos da profissional
│   ├── diarista_agenda.php # Gestão de horários livres da profissional
│   └── diarista_perfil.php # Visualização de validações e atualização de foto/senha
├── uploads/                # Diretório gerado automaticamente para fotos e documentos
├── index.php               # Ponto de entrada (Ecrã de Autenticação/Registo)
├── logout.php              # Encerramento seguro de sessão
└── README.md               # Documentação do projeto
