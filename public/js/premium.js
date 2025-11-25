// Sistema de Pop-ups Premium Estratégicos

const premiumPopups = {
    portfolio: {
        icon: '📊',
        title: 'Desbloqueie Análises Avançadas',
        description: 'Descubra como seu portfólio está performando e receba insights personalizados de IA.',
        features: [
            'Análise de engajamento em tempo real',
            'Sugestões de melhoria por IA',
            'Comparação com artistas similares',
            'Relatórios semanais detalhados'
        ],
        trigger: 'portfolio_view'
    },
    ai: {
        icon: '🤖',
        title: 'IA Premium: Estratégias Personalizadas',
        description: 'Nossa IA analisa sua carreira e identifica oportunidades que você está perdendo.',
        features: [
            'Análise profunda de marketing',
            'Identificação de erros estratégicos',
            'Plano de ação personalizado',
            'Consultoria 24/7 com IA avançada'
        ],
        trigger: 'ai_limit'
    },
    schedule: {
        icon: '📅',
        title: 'Otimize Sua Agenda com IA',
        description: 'Receba sugestões inteligentes de horários e locais para maximizar seu alcance.',
        features: [
            'Análise de melhores horários',
            'Sugestões de locais estratégicos',
            'Integração com redes sociais',
            'Lembretes inteligentes'
        ],
        trigger: 'schedule_create'
    },
    dashboard: {
        icon: '💎',
        title: 'Insights que Impulsionam Carreiras',
        description: 'Veja métricas avançadas e descubra o que está impedindo seu crescimento.',
        features: [
            'Dashboard completo de métricas',
            'Análise de concorrência',
            'Previsões de crescimento',
            'Alertas de oportunidades'
        ],
        trigger: 'dashboard_load'
    }
};

let popupShownThisSession = false;
let popupCount = parseInt(localStorage.getItem('premiumPopupCount') || '0');

function showPremiumPopup(type) {
    if (popupShownThisSession || popupCount >= 3) return;
    
    const popup = premiumPopups[type];
    if (!popup) return;
    
    const popupHTML = `
        <div class="premium-popup" id="premiumPopup">
            <div class="premium-popup-header">
                <div class="premium-popup-icon">${popup.icon}</div>
                <button class="premium-popup-close" onclick="closePremiumPopup()">&times;</button>
            </div>
            <h3 class="premium-popup-title">${popup.title}</h3>
            <p class="premium-popup-description">${popup.description}</p>
            <ul class="premium-features">
                ${popup.features.map(f => `<li><i class="fas fa-check-circle"></i> ${f}</li>`).join('')}
            </ul>
            <div class="premium-cta">
                <a href="/premium" class="btn-premium">Assinar Premium</a>
                <button class="btn-premium-outline" onclick="closePremiumPopup()">Agora não</button>
            </div>
        </div>
    `;
    
    const container = document.createElement('div');
    container.innerHTML = popupHTML;
    document.body.appendChild(container.firstElementChild);
    
    setTimeout(() => {
        document.getElementById('premiumPopup').classList.add('show');
    }, 100);
    
    popupShownThisSession = true;
    popupCount++;
    localStorage.setItem('premiumPopupCount', popupCount.toString());
}

function closePremiumPopup() {
    const popup = document.getElementById('premiumPopup');
    if (popup) {
        popup.classList.remove('show');
        setTimeout(() => popup.remove(), 300);
    }
}

// Triggers estratégicos
function initPremiumTriggers() {
    // Trigger no Dashboard após 10 segundos
    if (window.location.pathname === '/dashboard') {
        setTimeout(() => showPremiumPopup('dashboard'), 10000);
    }
    
    // Trigger no Portfólio ao visualizar projeto
    if (window.location.pathname === '/portfolio') {
        setTimeout(() => showPremiumPopup('portfolio'), 15000);
    }
    
    // Trigger na IA após 3 perguntas
    if (window.location.pathname === '/ai') {
        const aiUsage = parseInt(sessionStorage.getItem('aiUsageCount') || '0');
        if (aiUsage >= 3) {
            setTimeout(() => showPremiumPopup('ai'), 2000);
        }
    }
    
    // Trigger na Agenda ao criar evento
    if (window.location.pathname === '/schedule') {
        const scheduleCount = parseInt(localStorage.getItem('scheduleEventCount') || '0');
        if (scheduleCount >= 5) {
            setTimeout(() => showPremiumPopup('schedule'), 5000);
        }
    }
}

// Contador de uso da IA
if (window.location.pathname === '/ai') {
    const form = document.getElementById('ai-form');
    if (form) {
        form.addEventListener('submit', () => {
            const count = parseInt(sessionStorage.getItem('aiUsageCount') || '0') + 1;
            sessionStorage.setItem('aiUsageCount', count.toString());
        });
    }
}

// Inicializar triggers
document.addEventListener('DOMContentLoaded', initPremiumTriggers);

// Badge Premium no header
function addPremiumBadge() {
    const isPremium = document.body.dataset.premium === 'true';
    if (!isPremium) {
        const topbarContent = document.querySelector('.topbar-content');
        if (topbarContent) {
            const badge = document.createElement('a');
            badge.href = '/premium';
            badge.className = 'premium-badge';
            badge.innerHTML = '<i class="fas fa-gem"></i> Premium';
            badge.style.marginLeft = 'auto';
            badge.style.marginRight = '20px';
            badge.style.textDecoration = 'none';
            topbarContent.insertBefore(badge, topbarContent.lastElementChild);
        }
    }
}

document.addEventListener('DOMContentLoaded', addPremiumBadge);
