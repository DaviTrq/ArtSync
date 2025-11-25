// Marcar notificação individual como lida
document.querySelectorAll('.notification-item').forEach(item => {
    item.addEventListener('click', function(e) {
        const id = this.getAttribute('data-id');
        const type = this.getAttribute('data-type');
        const endpoint = type === 'admin' ? '/notifications/mark-admin-read' : '/notifications/mark-read';
        
        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        }).then(() => {
            if (!this.tagName || this.tagName !== 'A') {
                this.style.opacity = '0.5';
                setTimeout(() => location.reload(), 500);
            }
        });
    });
});

// Marcar todas como lidas
function markAllRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST'
    }).then(() => location.reload());
}
