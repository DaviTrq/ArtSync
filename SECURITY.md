# Guia de Segurança - ArtSync

## Implementações de Segurança

### 1. Proteção CSRF (Cross-Site Request Forgery)
- Tokens CSRF em todos os formulários
- Validação obrigatória em requisições POST
- Classe: `App\Security\CSRF`

### 2. Rate Limiting
- Login: 5 tentativas em 15 minutos
- Registro: 3 tentativas por hora
- Classe: `App\Security\RateLimiter`

### 3. Headers de Segurança
- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Content-Security-Policy
- Strict-Transport-Security (HTTPS)
- Classe: `App\Security\SecurityHeaders`

### 4. Variáveis de Ambiente
- Credenciais do banco em `.env`
- Nunca commitar `.env` no Git
- Usar `.env.example` como template

### 5. Sessões Seguras
- HttpOnly cookies
- SameSite: Strict
- Secure flag (HTTPS)
- Session regeneration no login
- Tempo de vida configurável

### 6. Validação de Entrada
- Filter_var para emails
- htmlspecialchars para XSS
- PDO prepared statements para SQL injection
- Validação de tipos de arquivo

### 7. Senhas
- Hash com password_hash (bcrypt)
- Mínimo 6 caracteres
- Verificação com password_verify

## Configuração Inicial

1. Copie `.env.example` para `.env`:
```bash
cp .env.example .env
```

2. Configure as variáveis no `.env`:
```
DB_HOST=localhost
DB_NAME=artsync_db
DB_USER=root
DB_PASS=sua_senha_aqui
```

3. Configure permissões:
```bash
chmod 600 .env
chmod 755 public/uploads/
```

4. Em produção, force HTTPS no `.htaccess`:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Checklist de Segurança

- [x] CSRF tokens em formulários
- [x] Rate limiting em login/registro
- [x] Headers de segurança
- [x] Variáveis de ambiente
- [x] Sessões seguras
- [x] Validação de entrada
- [x] Hash de senhas
- [x] SQL injection protection (PDO)
- [x] XSS protection (htmlspecialchars)
- [x] Validação de upload de arquivos

## Recomendações Adicionais

1. **Backup Regular**: Configure backups automáticos do banco
2. **Logs**: Implemente logging de tentativas de login
3. **2FA**: Considere autenticação de dois fatores
4. **Auditoria**: Revise logs regularmente
5. **Updates**: Mantenha PHP e dependências atualizadas
6. **Firewall**: Configure firewall no servidor
7. **SSL/TLS**: Use certificado válido em produção
