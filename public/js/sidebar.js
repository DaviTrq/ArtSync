document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const topbar = document.querySelector('.topbar');
    const mainContent = document.querySelector('.main-content');
    const footer = document.querySelector('.footer');
    let isNavigating = false;
    
    // Verifica se é mobile
    const isMobile = window.innerWidth <= 768;
    
    if (!isMobile) {
        // Desktop: Define estado inicial (retraído)
        if (topbar) topbar.style.left = '80px';
        if (mainContent) mainContent.style.marginLeft = '80px';
        if (footer) footer.style.marginLeft = '80px';
        
        // Previne expansão ao clicar em links
        const menuLinks = sidebar.querySelectorAll('nav a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                isNavigating = true;
                // Força menu retraído
                if (sidebar) sidebar.style.width = '80px';
                if (topbar) topbar.style.left = '80px';
                if (mainContent) mainContent.style.marginLeft = '80px';
                if (footer) footer.style.marginLeft = '80px';
            });
        });
        
        if (sidebar && topbar) {
            sidebar.addEventListener('mouseenter', function() {
                if (!isNavigating) {
                    topbar.style.left = '260px';
                    if (mainContent) mainContent.style.marginLeft = '260px';
                    if (footer) footer.style.marginLeft = '260px';
                }
            });
            
            sidebar.addEventListener('mouseleave', function() {
                topbar.style.left = '80px';
                if (mainContent) mainContent.style.marginLeft = '80px';
                if (footer) footer.style.marginLeft = '80px';
                isNavigating = false;
            });
        }
    } else {
        // Mobile: Remove estilos inline
        if (topbar) topbar.style.left = '';
        if (mainContent) mainContent.style.marginLeft = '';
        if (footer) footer.style.marginLeft = '';
    }
    
    // Atualiza ao redimensionar
    window.addEventListener('resize', function() {
        const nowMobile = window.innerWidth <= 768;
        if (nowMobile) {
            if (topbar) topbar.style.left = '';
            if (mainContent) mainContent.style.marginLeft = '';
            if (footer) footer.style.marginLeft = '';
        } else {
            if (topbar) topbar.style.left = '80px';
            if (mainContent) mainContent.style.marginLeft = '80px';
            if (footer) footer.style.marginLeft = '80px';
        }
    });
});
