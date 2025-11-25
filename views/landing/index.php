<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Art Sync'); ?></title>
    <link rel="stylesheet" href="/css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="background-waves"></div>

    <header>
        <div class="container header-content">
            <a href="" class="logo">
                <img src="/images/artsync.png" alt="Art Sync Logo"> </a>
            <nav>
                <div class="divider"></div>
                <ul>
                    <li><a href="#hero">Início</a></li>
                    <li><a href="#features">Funcionalidades</a></li>
                </ul>
            </nav>
            <div class="header-buttons">
                <div class="divider"></div>
                <button class="btn-profile" onclick="openAuthModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <main>
        <section id="hero">
            <div class="container hero-content">
                <h1>Sua música, sua visão, seus dados.</h1>
                <p>Transforme sua paixão em uma carreira profissional com a primeira plataforma de gestão gratuita que
                    une dados, IA e estratégia para artistas independentes.</p>
                <a href="/register" class="cta-button" id="cta-button">Comece Agora, de graça</a>
            </div>
        </section>

        <section id="features">
            <div class="container">
                <h2>Ferramentas para Artistas Independentes</h2>
                <p class="section-subtitle">Tudo que você precisa para gerenciar sua carreira musical em um só lugar</p>

                <div class="features-grid">
                    <div class="feature-card">
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                        <h3>Dashboard</h3>
                        <p>Visualize métricas de desempenho e acompanhe o crescimento da sua carreira</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon"><i class="fas fa-images"></i></div>
                        <h3>Portfólio</h3>
                        <p>Organize suas fotos, vídeos e projetos em galerias profissionais</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <h3>Agenda</h3>
                        <p>Gerencie shows, ensaios e compromissos com calendário interativo</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon"><i class="fas fa-robot"></i></div>
                        <h3>Assistente IA</h3>
                        <p>Receba orientações personalizadas para impulsionar sua carreira</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon"><i class="fas fa-comments"></i></div>
                        <h3>Fórum</h3>
                        <p>Conecte-se com outros artistas e compartilhe experiências</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon"><i class="fas fa-crown"></i></div>
                        <h3>Premium</h3>
                        <p>Desbloqueie recursos avançados e leve sua carreira ao próximo nível</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Art Sync. Todos os direitos reservados.</p>
        </div>
    </footer>

    <div id="authModal" class="auth-modal" style="display: none;">
        <div class="auth-modal-content">
            <span class="close" onclick="closeAuthModal()">&times;</span>
            <h2>Acesse sua conta</h2>
            <div class="auth-buttons">
                <a href="/login" class="auth-btn">Fazer Login</a>
                <a href="/register" class="auth-btn">Criar Conta</a>
            </div>
        </div>
    </div>

    <script src="/js/landing.js"></script>
    <script>
        function openAuthModal() {
            document.getElementById('authModal').style.display = 'flex';
            document.body.classList.add('modal-open');
        }
        function closeAuthModal() {
            document.getElementById('authModal').style.display = 'none';
            document.body.classList.remove('modal-open');
        }
        window.onclick = (e) => {
            if (e.target.id === 'authModal') closeAuthModal();
        };
    </script>

</body>

</html>