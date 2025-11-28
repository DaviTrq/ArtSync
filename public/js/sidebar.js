document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const topbar = document.querySelector('.topbar');
    const mainContent = document.querySelector('.main-content');
    const footer = document.querySelector('.footer');
    const notificationBell = document.getElementById('notification-bell');
    const notificationDropdown = document.getElementById('notification-dropdown');
    let isNavigating = false;
    const isMobile = window.innerWidth <= 768;
    if (!isMobile) {
        if (topbar) topbar.style.left = '80px';
        if (mainContent) mainContent.style.marginLeft = '80px';
        if (footer) footer.style.marginLeft = '80px';
        const menuLinks = sidebar.querySelectorAll('nav a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                isNavigating = true;
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
        if (topbar) topbar.style.left = '';
        if (mainContent) mainContent.style.marginLeft = '';
        if (footer) footer.style.marginLeft = '';
    }
    if (notificationBell && notificationDropdown) {
        let isOpen = false;
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            isOpen = !isOpen;
            if (isOpen) {
                notificationDropdown.classList.add('show');
            } else {
                notificationDropdown.classList.remove('show');
            }
        });
        notificationDropdown.addEventListener('mouseenter', function() {
            isOpen = true;
        });
        notificationDropdown.addEventListener('mouseleave', function() {
            isOpen = false;
            notificationDropdown.classList.remove('show');
        });
        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                isOpen = false;
                notificationDropdown.classList.remove('show');
            }
        });
    }
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
