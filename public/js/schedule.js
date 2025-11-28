let currentDate = new Date();
const events = window.scheduleEvents || [];
function getPriorityIcon(priority) {
    const icons = {
        'low': '<i class="fas fa-check-circle" style="color: #4CAF50; margin-right: 4px;"></i>',
        'medium': '<i class="fas fa-exclamation-triangle" style="color: #FF9800; margin-right: 4px;"></i>',
        'urgent': '<i class="fas fa-exclamation-circle" style="color: #F44336; margin-right: 4px;"></i>'
    };
    return icons[priority] || icons['low'];
}
function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const lang = window.scheduleLang || 'pt-BR';
    const locale = lang === 'en-US' ? 'en-US' : 'pt-BR';
    document.getElementById('currentMonth').textContent = 
        new Date(year, month).toLocaleDateString(locale, { month: 'long', year: 'numeric' });
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';
    const dayNames = lang === 'en-US' 
        ? ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
        : ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
    dayNames.forEach(day => {
        const header = document.createElement('div');
        header.className = 'calendar-day-header';
        header.textContent = day;
        grid.appendChild(header);
    });
    for (let i = firstDay - 1; i >= 0; i--) {
        grid.appendChild(createDay(daysInPrevMonth - i, true, year, month - 1));
    }
    for (let i = 1; i <= daysInMonth; i++) {
        grid.appendChild(createDay(i, false, year, month));
    }
    const remaining = 42 - (firstDay + daysInMonth);
    for (let i = 1; i <= remaining; i++) {
        grid.appendChild(createDay(i, true, year, month + 1));
    }
}
let expandedDay = null;
function createDay(num, isOther, year, month) {
    const day = document.createElement('div');
    day.className = 'calendar-day' + (isOther ? ' other-month' : '');
    const dayNumber = document.createElement('span');
    dayNumber.className = 'day-number';
    dayNumber.textContent = num;
    day.appendChild(dayNumber);
    if (!isOther) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(num).padStart(2, '0')}`;
        const dayEvents = events.filter(e => e.date.startsWith(dateStr));
        const actions = document.createElement('div');
        actions.className = 'day-actions';
        if (dayEvents.length > 0) {
            day.classList.add('has-events');
            day.style.borderColor = dayEvents[0].color;
            day.style.background = `${dayEvents[0].color}15`;
            const dots = document.createElement('div');
            dots.className = 'event-dots';
            dayEvents.slice(0, 3).forEach(e => {
                const dot = document.createElement('div');
                dot.className = 'event-dot';
                const priorityColors = { 'low': '#4CAF50', 'medium': '#FF9800', 'urgent': '#F44336' };
                dot.style.background = priorityColors[e.priority] || '#4CAF50';
                dots.appendChild(dot);
            });
            day.appendChild(dots);
            dayEvents.forEach(e => {
                const editBtn = document.createElement('button');
                editBtn.className = 'day-action-btn';
                editBtn.innerHTML = `${getPriorityIcon(e.priority)} ${e.title.substring(0, 10)}`;
                editBtn.onclick = (ev) => {
                    ev.stopPropagation();
                    openEditModal(e);
                };
                actions.appendChild(editBtn);
            });
        } else {
            const createBtn = document.createElement('button');
            createBtn.className = 'day-action-btn create';
            createBtn.innerHTML = `<i class="fas fa-plus"></i> ${window.scheduleTranslations?.create || 'Criar'}`;
            createBtn.onclick = (ev) => {
                ev.stopPropagation();
                openCreateModal(dateStr);
            };
            actions.appendChild(createBtn);
        }
        day.appendChild(actions);
        const today = new Date();
        if (year === today.getFullYear() && month === today.getMonth() && num === today.getDate()) {
            day.classList.add('today');
        }
        day.onclick = () => toggleDay(day);
    }
    return day;
}
function toggleDay(dayElement) {
    if (expandedDay && expandedDay !== dayElement) {
        expandedDay.classList.remove('expanded');
    }
    dayElement.classList.toggle('expanded');
    expandedDay = dayElement.classList.contains('expanded') ? dayElement : null;
}
function openCreateModal(dateStr) {
    const date = new Date(dateStr + 'T12:00:00');
    const lang = window.scheduleLang || 'pt-BR';
    const locale = lang === 'en-US' ? 'en-US' : 'pt-BR';
    const newEventText = lang === 'en-US' ? 'New Event' : 'Novo Evento';
    document.getElementById('modalTitle').innerHTML = 
        `<i class="fas fa-calendar-plus"></i> ${newEventText} - ${date.toLocaleDateString(locale, { day: 'numeric', month: 'long' })}`;
    document.getElementById('eventForm').reset();
    document.getElementById('eventDate').value = dateStr;
    const modal = document.getElementById('createModal');
    modal.style.display = 'flex';
    document.body.classList.add('modal-open');
}
function closeCreateModal() {
    const modal = document.getElementById('createModal');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
    if (expandedDay) expandedDay.classList.remove('expanded');
}
function openEditModal(event) {
    const date = new Date(event.date);
    document.getElementById('editEventId').value = event.id;
    document.getElementById('editEventDate').value = event.date.split(' ')[0];
    document.getElementById('editEventTitle').value = event.title;
    document.getElementById('editEventTime').value = date.toTimeString().slice(0, 5);
    document.getElementById('editEventLocation').value = event.location || '';
    document.getElementById('editEventNotes').value = event.notes || '';
    const colorInputs = document.querySelectorAll('#editColorPicker input[type="radio"]');
    colorInputs.forEach(input => {
        input.checked = input.value === event.color;
    });
    document.getElementById('editPriority').value = event.priority || 'low';
    document.getElementById('deleteEventBtn').href = `/schedule/delete?id=${event.id}`;
    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
    document.body.classList.add('modal-open');
}
function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
    if (expandedDay) expandedDay.classList.remove('expanded');
}
document.getElementById('prevMonth').onclick = () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
};
document.getElementById('nextMonth').onclick = () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
};
window.onclick = (e) => {
    if (e.target.id === 'createModal') closeCreateModal();
    if (e.target.id === 'editModal') closeEditModal();
    if (!e.target.closest('.calendar-day') && expandedDay) {
        expandedDay.classList.remove('expanded');
        expandedDay = null;
    }
};
function toggleSidebar() {
    const sidebar = document.getElementById('eventsSidebar');
    const btn = document.getElementById('toggleSidebarBtn');
    const desc = document.getElementById('scheduleDescription');
    sidebar.classList.toggle('collapsed');
    if (sidebar.classList.contains('collapsed')) {
        btn.innerHTML = '<span style="font-size: 1.2rem; font-weight: 300;">&lt;</span>';
        desc.style.textAlign = 'center';
    } else {
        btn.innerHTML = '<span style="font-size: 1.2rem; font-weight: 300;">&gt;</span>';
        desc.style.textAlign = 'left';
    }
}
function renderEventsSidebar() {
    const sidebar = document.getElementById('eventsSidebar');
    const content = document.getElementById('sidebarContent');
    if (events.length === 0) {
        sidebar.style.display = 'none';
        return;
    }
    sidebar.style.display = 'block';
    const sortedEvents = [...events].sort((a, b) => new Date(a.date) - new Date(b.date));
    const lang = window.scheduleLang || 'pt-BR';
    const locale = lang === 'en-US' ? 'en-US' : 'pt-BR';
    content.innerHTML = sortedEvents.map((e, idx) => {
        const date = new Date(e.date);
        const priorityColors = { 'low': '#4CAF50', 'medium': '#FF9800', 'urgent': '#F44336' };
        const borderColor = priorityColors[e.priority] || '#4CAF50';
        return `
            <div class="sidebar-event" style="border-left-color: ${borderColor}" onclick="openEditModalFromSidebar(${idx})">
                <div class="sidebar-event-date">${date.toLocaleDateString(locale, { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                <div class="sidebar-event-title">${getPriorityIcon(e.priority)} ${e.title}</div>
                <div class="sidebar-event-time"><i class="fas fa-clock"></i> ${date.toLocaleTimeString(locale, {hour: '2-digit', minute: '2-digit'})}</div>
            </div>
        `;
    }).join('');
}
function openEditModalFromSidebar(idx) {
    const sortedEvents = [...events].sort((a, b) => new Date(a.date) - new Date(b.date));
    openEditModal(sortedEvents[idx]);
}
renderCalendar();
renderEventsSidebar();
