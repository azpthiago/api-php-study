## API PHP - CRUD de Itens

Aplicação de estudo construída com PHP puro, SQLite (via PDO) e o servidor embutido da linguagem. O objetivo é demonstrar como estruturar uma API REST simples sem frameworks, separando responsabilidades em camadas (`Database`, `Service`, `Controller`) e expondo os endpoints em arquivos dentro de `public/`. A interface HTML disponível em `public/index.php` consome a própria API.

---

### 📦 Tecnologias e decisões
- **PHP ≥ 8.1** (CLI) com tipagem estrita habilitada.
- **PDO + SQLite** para persistência leve e sem dependências externas.
- **Servidor embutido do PHP** (`php -S`) para desenvolvimento local rápido.
- Endpoints aceitam `application/json` e `application/x-www-form-urlencoded`, padronizando respostas JSON.
- Organização em camadas simples:
  - `src/Database/Database.php` – singleton da conexão PDO.
  - `src/Services/ItemService.php` – regras de negócio e CRUD.
  - `src/Controllers/ItemController.php` – validação de payloads e respostas JSON.

---

### 🗂️ Estrutura das pastas principais

```
api-php/
├─ bootstrap.php           # Factory que monta o controller com suas dependências
├─ db/
│  └─ database.sqlite      # Banco de dados SQLite (criado após rodar o setup)
├─ public/
│  ├─ create_item.php      # Endpoint POST/JSON para criar itens
│  ├─ delete_item.php      # Endpoint POST/DELETE para remover itens
│  ├─ docs.php             # Resumo dos endpoints em JSON
│  ├─ index.php            # Frontend simples que consome a API
│  ├─ read_item.php        # Endpoint GET para listar itens
│  ├─ update_item.php      # Endpoint POST/PUT para atualizar itens
│  └─ styles/              # Recursos estáticos usados pela interface
└─ src/
   ├─ Controllers/
   │  └─ ItemController.php  # Validação + respostas JSON
   ├─ Database/
   │  └─ Database.php        # Conexão PDO com SQLite
   └─ Services/
      └─ ItemService.php     # Operações CRUD
```

---

### 🛠️ Instalando o PHP (sem XAMPP)

> Se você já possui PHP ≥ 8.1 com a extensão PDO_SQLITE habilitada, pode pular para a seção de configuração do projeto.

#### Windows
1. Baixe o pacote **Non Thread Safe** em formato `.zip` em https://windows.php.net/download/ (use a versão mais recente da série 8.x).
2. Extraia em `C:\php` (ou outra pasta à sua escolha).
3. Renomeie `php.ini-development` para `php.ini`.
4. Abra o `php.ini` e garanta que as linhas abaixo estejam descomentadas:
   ```
   extension_dir = "ext"
   extension=pdo_sqlite
   extension=sqlite3
   ```
5. Adicione `C:\php` à variável de ambiente **PATH**:
   - Painel de Controle → Sistema → Configurações avançadas → Variáveis de Ambiente.
   - Edite `Path` e inclua `C:\php`.
6. No PowerShell/cmd, teste com `php -v`.

#### Linux (Debian/Ubuntu)
```bash
sudo apt update
sudo apt install -y php php-cli php-sqlite3
php -v
```

#### macOS (Homebrew)
```bash
brew install php
php -v
```

Certifique-se de que `php -m | grep sqlite` retorne `pdo_sqlite` e `sqlite3`.

---

### ⚙️ Configuração do projeto

1. Clone ou copie o repositório:
   ```bash
   git clone https://github.com/seu-usuario/api-php.git
   cd api-php
   ```
2. Crie a estrutura de banco de dados (tabela `items`):
   ```bash
   php setup/init.php
   ```
   Esse script garante a criação da pasta `db/`, do arquivo `database.sqlite` e da tabela.
3. Inicie o servidor embutido:
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```
4. Acesse:
   - `http://127.0.0.1:8000/index.php` para a interface web.
   - `http://127.0.0.1:8000/docs.php` para visualizar a documentação dos endpoints.

---

### 🔌 Endpoints disponíveis

| Método         | Caminho             | Descrição                                                                 |
|----------------|---------------------|----------------------------------------------------------------------------|
| `GET`          | `/read_item.php`    | Lista todos os itens cadastrados                                          |
| `POST`         | `/create_item.php`  | Cria um novo item (`nome`, `descricao`) via JSON ou formulário            |
| `POST` / `PUT` | `/update_item.php`  | Atualiza item existente (`id`, `nome`, `descricao`)                        |
| `POST` / `DELETE` | `/delete_item.php` | Remove item (`id`) pelo corpo, query string ou JSON                       |

Todas as respostas seguem o formato:

```
```

### 🧪 Exemplos de uso via `curl`

```bash
# Criar item (form URL-encoded)
curl -X POST http://127.0.0.1:8000/create_item.php \
  -d "nome=Livro" \
  -d "descricao=Ler capítulo 1"

# Criar item (JSON)
curl -X POST http://127.0.0.1:8000/create_item.php \
  -H "Content-Type: application/json" \
  -d '{"nome": "Curso", "descricao": "Assistir aula"}'

# Listar itens
curl http://127.0.0.1:8000/read_item.php

# Atualizar item
curl -X PUT http://127.0.0.1:8000/update_item.php \
  -H "Content-Type: application/json" \
  -d '{"id": 1, "nome": "Livro atualizado", "descricao": "Adicionar notas"}'

# Remover item
curl -X DELETE "http://127.0.0.1:8000/delete_item.php?id=1"
```