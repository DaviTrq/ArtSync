# ArtSync - Guia de Implementação de Segurança

## Recursos de Segurança Implementados

### 1. Autenticação e Autorização
- **Proteção CSRF**: Todos os formulários incluem tokens CSRF
- **Segurança de Senha**: Hash Argon2ID, requisitos de senha forte
- **Segurança de Sessão**: Configuração segura de sessão, regeneração no login
- **Limitação de Taxa**: Limitação de tentativas de login (5 tentativas por 15 minutos)
- **Proteção Admin**: Controle de acesso baseado em funções

### 2. Validação e Sanitização de Entrada
- **Prevenção XSS**: Todas as saídas escapadas corretamente com htmlspecialchars
- **Sanitização de Entrada**: Todas as entradas do usuário sanitizadas antes do processamento
- **Segurança de Upload**: Validação de tipo MIME, limites de tamanho, prevenção de path traversal
- **Validação de Email**: Validação adequada de formato de email

### 3. Segurança do Banco de Dados
- **Prevenção de SQL Injection**: Todas as consultas usam prepared statements
- **Segurança de Conexão**: Configuração PDO segura com modo estrito
- **Variáveis de Ambiente**: Credenciais do banco armazenadas no arquivo .env

### 4. Segurança de Arquivos
- **Restrições de Upload**: Apenas tipos de arquivo permitidos (imagens, áudio)
- **Limites de Tamanho**: Máximo 5MB por arquivo
- **Validação de Caminho**: Previne ataques de directory traversal
- **Nomes de Arquivo Seguros**: Nomes de arquivo únicos gerados

### 5. Tratamento de Erros
- **Prevenção de Divulgação de Informações**: Mensagens de erro genéricas em produção
- **Registro de Erros**: Erros detalhados registrados para depuração
- **Degradação Graciosa**: Páginas de erro adequadas e fallbacks

### 6. Segurança HTTP
- **Cabeçalhos de Segurança**: X-Content-Type-Options, X-Frame-Options, X-XSS-Protection
- **Política de Segurança de Conteúdo**: Restringe carregamento de recursos
- **Pronto para HTTPS**: Configuração de cookie seguro para HTTPS

## Configuração Necessária

### 1. Configuração do Ambiente
Copie `.env.example` para `.env` e configure:
```
DB_HOST=localhost
DB_NAME=artsync_db
DB_USER=root
DB_PASS=sua_senha

APP_URL=https://seu-dominio.com
APP_DEBUG=false

GEMINI_API_KEY=sua_chave_api_aqui
```

### 2. Permissões de Arquivo
Defina permissões adequadas:
- `public/uploads/`: 755
- `.env`: 600
- `config/`: 644

### 3. Configuração do Servidor Web
Certifique-se de que os módulos Apache estão habilitados:
- mod_rewrite
- mod_headers
- mod_ssl (para HTTPS)

## Lista de Verificação de Segurança

- [ ] Configurar arquivo .env com credenciais seguras
- [ ] Definir permissões de arquivo adequadas
- [ ] Habilitar HTTPS em produção
- [ ] Configurar banco de dados com usuário restrito
- [ ] Configurar backups regulares
- [ ] Monitorar logs de erro
- [ ] Atualizar dependências regularmente
- [ ] Configurar regras de firewall

## Manutenção

### Tarefas Regulares
1. Atualizar dependências do Composer: `composer update`
2. Revisar logs de erro: Verificar logs de erro do servidor
3. Monitorar tentativas de login falhadas
4. Limpar arquivos antigos enviados
5. Fazer backup do banco de dados regularmente

### Monitoramento de Segurança
- Monitorar tabela `audit_logs` para atividade suspeita
- Verificar padrões de login falhado
- Revisar padrões de upload de arquivo
- Monitorar uso de disco no diretório de uploads