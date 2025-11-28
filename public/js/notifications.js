document.querySelectorAll('.notification-item').forEach(item => {
    item.addEventListener('click', function(e) {
        if (e.target.tagName === 'BUTTON' || e.target.closest('button')) return;
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
function markAllRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST'
    }).then(() => location.reload());
}
function acceptConnection(userId, btn) {
    fetch('/network/accept-request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'user_id=' + userId
    }).then(r => r.json()).then(data => {
        if (data.success) {
            btn.closest('.notification-item').remove();
            const count = document.querySelector('.notification-count');
            if (count) {
                const num = parseInt(count.textContent) - 1;
                if (num > 0) count.textContent = num;
                else count.style.display = 'none';
            }
        }
    });
}
function rejectConnection(userId, btn) {
    fetch('/network/reject-request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'user_id=' + userId
    }).then(r => r.json()).then(data => {
        if (data.success) {
            btn.closest('.notification-item').remove();
            const count = document.querySelector('.notification-count');
            if (count) {
                const num = parseInt(count.textContent) - 1;
                if (num > 0) count.textContent = num;
                else count.style.display = 'none';
            }
        }
    });
}
