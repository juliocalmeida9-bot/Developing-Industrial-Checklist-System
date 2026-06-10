# ⚙ CheckInd — Sistema de Checklist Industrial
**Indústria Metalúrgica TechForge**

Sistema integrado multiplataforma para controle de uso de máquinas e verificação de segurança no chão de fábrica.

---

## 🏗️ Arquitetura

```
┌─────────────────────┐    HTTP/JSON REST    ┌──────────────────────────┐
│  App Mobile (Flet)  │ ◄──────────────────► │   Backend PHP MVC        │
│  Python 3.x         │                      │   Apache/XAMPP + MySQL   │
└─────────────────────┘                      └────────────┬─────────────┘
                                                          │ PDO
┌─────────────────────┐   Sessão PHP         ┌────────────▼─────────────┐
│  Painel Web         │ ◄──────────────────► │   Banco: MySQL checkind  │
│  Bootstrap 5 + JS   │                      └──────────────────────────┘
└─────────────────────┘
```

### Padrão MVC
- **Models** (`backend/models/`) — Acesso ao banco via PDO preparado
- **Views** (`backend/views/`) — HTML com Bootstrap, zero SQL
- **Controllers** (`backend/controllers/`) — Lógica de negócio, sem HTML

---

## 🛠️ Pré-requisitos

| Componente | Versão mínima |
|---|---|
| XAMPP (Apache + MySQL + PHP) | 8.1+ |
| Python | 3.10+ |
| pip | 23+ |

---

## 🚀 Instalação e Configuração

### 1. Banco de Dados

1. Inicie o **XAMPP Control Panel** e ative Apache e MySQL
2. Acesse `http://localhost/phpmyadmin`
3. Importe o schema:
   - Clique em **"Novo"** → nomeie `checkind` → **Criar**
   - Aba **SQL** → cole o conteúdo de `database/schema.sql` → **Executar**

### 2. Backend PHP

1. Copie a pasta `backend/` para o diretório do servidor web do Laragon ou XAMPP.
   - Laragon padrão: `C:\laragon\www\checkind\`
   - XAMPP padrão: `C:\xampp\htdocs\checkind\`
   (O conteúdo de `backend/` deve ficar direto dentro de `checkind/`)

2. **Habilitar mod_rewrite no Apache:**
   - Laragon já ativa `mod_rewrite` por padrão.
   - Se estiver usando XAMPP, abra `C:\xampp\apache\conf\httpd.conf`
   - Localize e descomente: `LoadModule rewrite_module modules/mod_rewrite.so`
   - Localize o bloco `<Directory "C:/xampp/htdocs">` e altere `AllowOverride None` para `AllowOverride All`
   - Reinicie o Apache no painel do XAMPP ou Laragon

3. **Popular o banco com dados iniciais:**

   **Opção A — Via terminal:**
   ```bash
   cd C:\laragon\www\checkind
   php ..\..\..\..\Users\julio\Documents\GitHub\projeto-joão\database\seed.php
   ```

   **Opção B — Via navegador (temporário):**
   Copie também o `database/seed.php` para `C:\laragon\www\checkind\seed.php`
   Acesse `http://localhost/checkind/seed.php`
   ⚠️ Remova o arquivo após executar!

4. **Acesse o painel:**
   ```
   http://localhost/checkind/
   ```

> O `backend/config/app.php` agora detecta automaticamente o `BASE_URL` e `BASE_PATH`, então o sistema deve funcionar tanto em `http://localhost/checkind` quanto em um site Laragon customizado como `http://checkind.test`.

### 3. App Mobile (Python Flet)

```bash
cd "c:\Users\julio\Documents\GitHub\projeto-joão\mobile"

# Instalar dependências
pip install -r requirements.txt

# Instalar o zbar para Windows (necessário para OpenCV QR):
# O opencv-python já inclui QRCodeDetector internamente — sem dependência extra!

# Executar o app
python main.py
```

---

## 🔑 Credenciais Iniciais

### Painel Web (Gestor)
| Campo | Valor |
|---|---|
| E-mail | joaoitor@gmail.com |
| Senha | 123456 |

### App Mobile (Funcionários — criados pelo seed)
| Nome | Matrícula | E-mail | Senha |
|---|---|---|---|
| Carlos Mendes | OP001 | carlos@techforge.com | op1234 |
| Ana Rodrigues | OP002 | ana@techforge.com | op1234 |
| Bruno Lima | OP003 | bruno@techforge.com | op1234 |
| Fernanda Costa | OP004 | fer@techforge.com | op1234 |

---

## 📋 Backlog — Requisitos Funcionais

| RF | Módulo | Descrição | Status |
|---|---|---|---|
| RF01 | Web | Autenticação administrativa (login/logout) | ✅ |
| RF02 | Web | CRUD de funcionários com credenciais | ✅ |
| RF03 | Web | CRUD de máquinas + geração de QR Code | ✅ |
| RF04 | Web | Cadastro de itens de checklist (entrada/saída) | ✅ |
| RF05 | Web | Dashboard de logs e utilização em tempo real | ✅ |
| RF06 | API | Endpoint de autenticação do funcionário | ✅ |
| RF07 | API | Endpoint de consulta de máquina via QR Code | ✅ |
| RF08 | API | Endpoint de envio de checklist (logs) | ✅ |
| RF09 | Mobile | Tela de login do funcionário | ✅ |
| RF10 | Mobile | Leitura de QR Code via câmera | ✅ |
| RF11 | Mobile | Checklist de entrada (início de turno) | ✅ |
| RF12 | Mobile | Checklist de saída (fim de turno) | ✅ |

---

## 🔌 API REST — Endpoints

Base URL: `http://localhost/checkind/api`

### POST /auth/login
Autenticação do funcionário.

**Request:**
```json
{
  "email": "carlos@techforge.com",
  "password": "op1234"
}
```
ou com matrícula:
```json
{
  "matricula": "OP001",
  "password": "op1234"
}
```

**Response 200:**
```json
{
  "success": true,
  "token": "abc123def456...",
  "employee": {
    "id": 1,
    "name": "Carlos Mendes",
    "matricula": "OP001",
    "sector": "Usinagem"
  }
}
```

---

### GET /machine/{token}
Retorna dados da máquina e a lista de checklist ativa.

**Header:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "machine": {
    "id": 1,
    "name": "Torno CNC Alpha",
    "model": "CNC-A200",
    "sector": "Usinagem"
  },
  "checklist_type": "entry",
  "checklist_items": [
    {"id": 1, "question": "EPIs estão corretos?"},
    {"id": 2, "question": "Máquina sem vazamentos?"}
  ]
}
```

> O sistema detecta automaticamente se o tipo é `entry` ou `exit` com base no histórico do dia.

---

### POST /log
Envia as respostas do checklist.

**Header:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "machine_id": 1,
  "action_type": "entry",
  "responses": [
    {"item_id": 1, "answer": true},
    {"item_id": 2, "answer": true}
  ]
}
```

**Response 200:**
```json
{
  "success": true,
  "log_id": 42,
  "status": "approved",
  "message": "Checklist de Entrada aprovado! ✓"
}
```

---

### POST /auth/logout
Revoga o token de acesso.

**Header:** `Authorization: Bearer {token}`

---

## 📁 Estrutura do Projeto

```
projeto-joão/
├── database/
│   ├── schema.sql          ← Cria as tabelas
│   └── seed.php            ← Insere dados iniciais
│
├── backend/                ← Copiar para C:\xampp\htdocs\checkind\
│   ├── .htaccess           ← URL rewriting (mod_rewrite)
│   ├── index.php           ← Front Controller (ponto único de entrada)
│   ├── config/
│   │   ├── app.php         ← BASE_URL, BASE_PATH, timezone
│   │   └── database.php    ← Conexão PDO singleton
│   ├── core/
│   │   ├── Router.php      ← Roteador MVC
│   │   ├── Controller.php  ← Controller base (view, redirect, json, auth)
│   │   └── Model.php       ← Model base (CRUD genérico)
│   ├── models/
│   │   ├── User.php        ← Gestor web
│   │   ├── Employee.php    ← Funcionário + tokens API
│   │   ├── Machine.php     ← Máquinas + QR tokens
│   │   ├── ChecklistItem.php ← Perguntas do checklist
│   │   └── Log.php         ← Logs imutáveis + respostas
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── EmployeeController.php
│   │   ├── MachineController.php
│   │   ├── ChecklistController.php
│   │   ├── LogController.php
│   │   └── api/
│   │       ├── AuthApiController.php
│   │       ├── MachineApiController.php
│   │       └── LogApiController.php
│   ├── views/
│   │   ├── layouts/main.php
│   │   ├── auth/login.php
│   │   ├── dashboard/index.php
│   │   ├── employees/{index,form}.php
│   │   ├── machines/{index,form}.php
│   │   ├── checklist/index.php
│   │   └── logs/index.php
│   └── public/
│       ├── css/style.css
│       └── js/main.js
│
└── mobile/                 ← App Python Flet
    ├── main.py             ← Ponto de entrada: ft.app(target=main)
    ├── config.py           ← API_BASE_URL e paleta de cores
    ├── requirements.txt
    ├── api/
    │   └── client.py       ← ApiClient (requests HTTP)
    └── views/
        ├── login_view.py   ← RF09
        ├── scanner_view.py ← RF10
        ├── checklist_view.py ← RF11, RF12
        └── success_view.py ← Resultado do checklist
```

---

## 🔒 Segurança Implementada

| Mecanismo | Implementação |
|---|---|
| Senhas | `password_hash()` / `password_verify()` (bcrypt) |
| SQL Injection | PDO preparado em todas as queries |
| XSS | `htmlspecialchars()` em todas as saídas |
| CSRF | Token de sessão validado em todos os formulários POST |
| API Auth | Token Bearer armazenado em tabela `api_tokens` |
| Logs | Tabela `usage_logs` sem UPDATE/DELETE (imutável por design) |

---

## 🧪 Testando o Fluxo Completo

1. **Painel Web:** Acesse `http://localhost/checkind` → Login → Dashboard
2. **Cadastre** uma máquina e copie o **token QR** exibido
3. **App Mobile:** Execute `python main.py` → Login com OP001/op1234
4. **Escaneie** com a câmera ou **cole o token manualmente**
5. **Preencha** o checklist de entrada → "Iniciar Trabalho"
6. **Repita** o scan → aparecerá o checklist de saída → "Finalizar Turno"
7. **Painel Web** → Histórico → visualize o log registrado

---

## 📚 Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Backend | PHP 8.1+ (MVC puro) |
| Banco | MySQL 8 / MariaDB |
| Frontend Web | Bootstrap 5.3 + Vanilla JS ES6 |
| Ícones | Font Awesome 6 |
| QR Code Web | qrcodejs (CDN) |
| App Mobile | Python 3.10+ + Flet |
| HTTP Client | Python requests |
| Câmera QR | OpenCV (cv2.QRCodeDetector) |

---

*Projeto desenvolvido para a disciplina de Transformação Digital e Indústria 4.0.*
