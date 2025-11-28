# ArtSync

Plataforma web pra ajudar artistas independentes a gerenciar carreira. Foi feito como projeto de fim de ano do Colegio Cotemig.

## O que faz

- **Portfólio/Press Kit**: Galeria com imagens, áudios e vídeos + link compartilhável
- **Agenda**: Eventos com notificações automáticas por data
- **Dashboard**: Gráficos e métricas de exemplo
- **IA de Carreira**: Dicas personalizadas usando Google Gemini
- **Fórum**: Sistema de tópicos e comentários com moderação admin
- **Mensagens Diretas**: Chat em tempo real com anexos (imagens, vídeos, áudios, arquivos DAW)
- **Rede Social**: Sistema de conexões entre artistas
- **Busca Global**: Encontre usuários, tópicos e funcionalidades
- **Sistema Bilíngue**: PT-BR e EN-US
- **Tema Claro/Escuro**: Alternância de temas

## Quem fez

- Davi Torquato - 22300333
- Cauã Lacerda - 22301429
- Gabriel Alves - 22301577
- Igor Ceolin - 22300139
- Ravi Braga - 22300198

## Estrutura

```
artsync-mvc/
├── app/               # Controllers, Models, Repos, Services
├── bdd/               # SQL do banco
├── config/            # Configs (Database, env, lang)
├── public/            # Raiz web (index.php, css, js, uploads)
├── vendor/            # Composer (PHPMailer)
└── views/             # Templates PHP
```

## Como rodar

### Requisitos

- PHP 8.1+
- MySQL 5.7+
- Apache com mod_rewrite
- Composer

### Instalação

**1. Baixar**

```bash
git clone https://github.com/usuario/artsync-mvc.git
cd artsync-mvc
```

Ou joga os arquivos em `C:/wamp64/www/artsync-mvc`

**2. Instalar dependências**

```bash
composer install
```

**3. Criar banco**

Abre o phpMyAdmin (http://localhost/phpmyadmin):
- Clica em "Novo"
- Nome: `artsync_db`
- Collation: `utf8mb4_unicode_ci`
- Importa o arquivo `bdd/install.sql`

**4. Configurar .env**

Copia o `.env.example` pra `.env` e edita:

```env
DB_HOST=localhost
DB_NAME=artsync_db
DB_USER=root
DB_PASS=

SESSION_LIFETIME=3600
MAX_LOGIN_ATTEMPTS=5
LOGIN_TIMEOUT=900

GEMINI_API_KEY=sua_chave_aqui  # opcional, só se quiser usar a IA

APP_ENV=development
APP_URL=http://localhost
```

**5. Configurar Apache**

Edita `httpd-vhosts.conf` (WAMP: `C:/wamp64/bin/apache/apacheX.X.X/conf/extra/httpd-vhosts.conf`):

```apache
<VirtualHost *:80>
    ServerName artsync.local
    DocumentRoot "C:/wamp64/www/artsync-mvc/public"
    <Directory "C:/wamp64/www/artsync-mvc/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Edita o arquivo hosts (`C:/Windows/System32/drivers/etc/hosts`):

```
127.0.0.1 artsync.local
```

Reinicia o Apache e acessa: http://artsync.local

Ou sem virtual host: http://localhost/artsync-mvc/public

**6. Criar admin**

Depois de criar uma conta normal, vai no phpMyAdmin:
- Abre a tabela `users`
- Edita teu usuário
- Muda `is_admin` pra `1`

**7. API da IA (opcional)**

Se quiser usar a IA:
1. Pega uma chave em https://makersuite.google.com/app/apikey
2. Coloca no `.env`: `GEMINI_API_KEY=sua_chave`

### Problemas comuns

**Erro "Class not found"**
```bash
composer dump-autoload
```

**Erro "Connection refused"**
- Verifica se o MySQL tá rodando
- Confere as credenciais no `.env`

**404 em tudo**
- Verifica se o `mod_rewrite` tá ativo
- Confirma que o DocumentRoot aponta pra pasta `/public`

**Upload não funciona**
```bash
# Linux/Mac
chmod -R 755 public/uploads/

# Windows: dá permissão de escrita na pasta
```

## Tecnologias

- PHP 8.1+ (MVC, OOP, Repository Pattern)
- MySQL/MariaDB
- HTML5, CSS3, JavaScript
- Composer (PHPMailer)
- Google Gemini AI (opcional)

## Arquitetura

### Padrão MVC
- **Models**: Entidades de domínio (User, PortfolioProject, etc)
- **Views**: Templates PHP separados por módulo
- **Controllers**: Lógica de aplicação e roteamento
- **Repositories**: Camada de acesso a dados (PDO)
- **Services**: Lógica de negócio (NotificationService)

### Segurança Implementada
- **Autenticação**: Sistema de login com rate limiting
- **Autorização**: Verificação de permissões em cada rota
- **CSRF**: Tokens de proteção em formulários
- **XSS**: Sanitização com htmlspecialchars()
- **SQL Injection**: PDO prepared statements
- **Upload**: Validação de tipo MIME e tamanho
- **Sessions**: HttpOnly, Secure, SameSite cookies
- **Headers**: CSP, X-Frame-Options, X-Content-Type-Options

## Features Detalhadas

### Portfólio/Press Kit
- Upload de múltiplas mídias (imagens, áudios, vídeos)
- Geração automática de link compartilhável
- Visualização pública de projetos
- Estatísticas de mídia por projeto

### Sistema de Mensagens
- Chat em tempo real
- Anexar arquivos: imagens, vídeos, áudios
- Suporte para arquivos DAW (.flp, .als, .ptx, .logic, .rpp)
- Gravação de áudio direto pelo navegador
- Indicador de mensagens não lidas
- Fotos de perfil nos chats

### Rede Social
- Sistema de conexões entre usuários
- Visualizar perfis de outros artistas
- Ver portfólio de outros usuários
- Estatísticas de seguidores/seguindo

### Fórum
- Criação de tópicos com anexos
- Sistema de comentários
- Moderação admin (aprovar/rejeitar)
- Notificações para admins

### Segurança
- Autenticação com sessões seguras
- Rate limiting em login
- CSRF protection
- Password hashing (bcrypt)
- PDO prepared statements
- Security headers (CSP, X-Frame-Options, etc)
- Validação de uploads
- XSS protection

## Licença

Projeto acadêmico - uso livre
