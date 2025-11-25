<?php if (isset($_SESSION['user_id'])): ?>
        </main>
    </div>
<?php endif; ?>

<footer class="footer">
    <p>&copy; <?= date('Y'); ?> <strong>ArtSync</strong> — <?= $t['footer_text']; ?></p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Notificações (sino)
    const bell = document.getElementById('notification-bell');
    const drop = document.getElementById('notification-dropdown');
    if (bell && drop) {
        bell.addEventListener('click', (e) => {
            e.preventDefault();
            drop.classList.toggle('show');
        });
        document.addEventListener('click', (e) => {
            if (!bell.contains(e.target) && !drop.contains(e.target)) drop.classList.remove('show');
        });
    }

    // Menu do avatar (perfil)
    const avatar = document.getElementById('profileAvatar');
    const profileDrop = document.getElementById('profileDropdown');
    if (avatar && profileDrop) {
        avatar.addEventListener('click', () => profileDrop.classList.toggle('show'));
        document.addEventListener('click', (e) => {
            if (!avatar.contains(e.target) && !profileDrop.contains(e.target)) profileDrop.classList.remove('show');
        });
    }

    // Tema
    const body = document.body;
    const themeBtn = document.getElementById('toggleTheme');
    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            body.classList.toggle('light-theme');
            localStorage.setItem('theme', body.classList.contains('light-theme') ? 'light' : 'dark');
        });
        const saved = localStorage.getItem('theme');
        if (saved === 'light') body.classList.add('light-theme');
    }

    // Idioma (só visual)
    const langBtn = document.getElementById('toggleLang');
    if (langBtn) {
        langBtn.addEventListener('click', () => {
            const html = document.documentElement;
            if (html.lang === 'pt-BR') {
                html.lang = 'en';
                langBtn.innerHTML = '<i class="fas fa-globe"></i> Português';
            } else {
                html.lang = 'pt-BR';
                langBtn.innerHTML = '<i class="fas fa-globe"></i> English';
            }
        });
    }
});
</script>

<style>
/* Topbar + Perfil */
.topbar{
    position:fixed; top:0; left:260px; right:0; height:70px; z-index:200;
    background: var(--glass-bg); backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border-color);
}
.topbar .topbar-content{
    height:100%; display:flex; align-items:center; justify-content:space-between; padding:0 30px;
}
.page-title{ color: var(--primary-text-color); font-size:1.6em; font-weight:500; }

.profile-menu{ position:relative; }
.profile-avatar{
    width:45px; height:45px; border-radius:50%; overflow:hidden;
    border: 2px solid var(--border-color); cursor:pointer;
}
.profile-avatar img{ width:100%; height:100%; object-fit:cover; }

.profile-dropdown{
    position:absolute; top:60px; right:0; width:220px;
    background: rgba(10,10,15,.95); border: 1px solid var(--border-color);
    border-radius: 10px; display:none; box-shadow: 0 8px 30px rgba(0,0,0,.5); z-index:300;
}
.profile-dropdown.show{ display:block; }
.profile-dropdown .dropdown-item{
    width:100%; background:transparent; border:none; color: var(--secondary-text-color);
    display:flex; align-items:center; gap:10px; padding:10px 15px; text-decoration:none; cursor:pointer;
}
.profile-dropdown .dropdown-item:hover{ background: rgba(255,255,255,.06); color: var(--primary-text-color); }
.profile-dropdown hr{ border:none; border-top:1px solid var(--border-color); margin:8px 0; }

/* Ajusta conteúdo por causa da topbar fixa */
.main-content{ margin-left:260px; margin-top:70px; }

/* Modo claro */
body.light-theme{
    --primary-text-color:#000;
    --secondary-text-color:#444;
    --background-color:#f6f6f6;
    --glass-bg:rgba(255,255,255,.7);
    --border-color:rgba(0,0,0,.1);
}
</style>

</body>
</html>
