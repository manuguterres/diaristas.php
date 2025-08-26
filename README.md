
# Diaristas Web — PHP + SQLite

## Integrantes do Projeto

- CHARLES CARVALHO
- ALISON DE SOUZA LEITE
- JOAO MARCOS PATRIARCA
- JOAO MARCOS SIMOES
- MANUELA NUNES GUTERRES
- MERIANGENI THAIZ MONASTERIOS MATERANO
- JOSE VINICIUS MARCELINO SIMOES


Sistema web completo em PHP + SQLite com telas (Bootstrap) — login por sessão, cadastro, perfil de diarista, busca, agendamento, aceitar/recusar, pagamento simulado e avaliação.


## Como executar o projeto

### 1. Executando localmente (sem Docker)

1. Certifique-se de ter o PHP instalado (versão 8 ou superior).
2. No terminal, navegue até a pasta do projeto.
3. Execute o servidor embutido do PHP:
	```
	php -S localhost:8000
	```
4. Acesse no navegador: [http://localhost:8000](http://localhost:8000)
5. O banco de dados será criado automaticamente como `database.db` na primeira execução.
6. Para resetar o banco, apague o arquivo `database.db`.

### 2. Executando com Docker

1. Certifique-se de ter o Docker e o Docker Compose instalados.
2. No terminal, navegue até a pasta do projeto.
3. Execute o comando:
	```
	docker-compose up --build
	```
4. Acesse no navegador: [http://localhost:8000](http://localhost:8000)
5. O banco de dados será criado automaticamente no container.
6. Para resetar o banco, apague o arquivo `database.db` na pasta do projeto e reinicie o container.


## Páginas do sistema
- `index.php` — Login
- `register.php` — Cadastro
- `dashboard.php` — Redireciona para painel cliente/diarista
- `client.php` — Painel do cliente: listar diaristas, agendar, ver solicitações
- `diarista.php` — Painel do diarista: gerenciar perfil, pedidos recebidos
- `agendar.php` — Formulário de agendamento (pode ser acessado a partir do cliente)
- `avaliar.php` — Avaliar pedido concluído
- `logout.php` — Logout
- `config.php` — Banco de dados e funções auxiliares
- `assets/style.css` — Estilo adicional
