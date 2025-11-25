<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="max-width: 1200px; margin: 0 auto 20px; padding: 0 20px;">
    <p id="scheduleDescription" style="color: var(--secondary-text-color); font-size: 1rem; margin: 0;"><?= $t['organize_shows']; ?></p>
</div>

<?php if (isset($feedback)): ?>
    <div class="<?= $feedback['type'] === 'error' ? 'error-message' : 'success-message'; ?>">
        <?= htmlspecialchars($feedback['message']); ?>
    </div>
<?php endif; ?>

<div class="schedule-layout">
    <div class="calendar-card card">
        <div class="calendar-header">
            <button type="button" id="prevMonth" class="nav-btn"><i class="fas fa-chevron-left"></i></button>
            <h3 id="currentMonth"></h3>
            <button type="button" id="nextMonth" class="nav-btn"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="calendar-grid" id="calendarGrid"></div>
    </div>

    <div class="events-sidebar" id="eventsSidebar" style="display: none;">
        <div class="sidebar-header" onclick="toggleSidebar()" style="cursor: pointer;">
            <h3><?= $t['scheduled_events']; ?></h3>
            <button type="button" class="toggle-sidebar" id="toggleSidebarBtn">
                <span style="font-size: 1.2rem; font-weight: 300;">&gt;</span>
            </button>
        </div>
        <div class="sidebar-content" id="sidebarContent"></div>
    </div>
</div>

<!-- Modal Criar Evento -->
<div id="createModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeCreateModal()">&times;</span>
        <h2 id="modalTitle"></h2>
        
        <form action="/schedule/create" method="post" enctype="multipart/form-data" id="eventForm">
            <input type="hidden" name="event_date" id="eventDate">
            
            <div class="input-group">
                <label><?= $t['title']; ?> *</label>
                <input type="text" name="event_title" required>
            </div>

            <div class="input-group">
                <label><?= $t['time']; ?> *</label>
                <input type="time" name="event_time" value="09:00" required>
            </div>

            <div class="input-group">
                <label><?= $t['location']; ?></label>
                <input type="text" name="event_location" placeholder="Ex: Teatro Municipal">
            </div>

            <div class="input-group">
                <label><?= $t['description']; ?></label>
                <textarea name="notes" rows="3"></textarea>
            </div>

            <div class="input-group">
                <label><?= $t['event_color']; ?></label>
                <div class="color-picker">
                    <label><input type="radio" name="event_color" value="#4CAF50" checked><span style="background:#4CAF50"></span></label>
                    <label><input type="radio" name="event_color" value="#2196F3"><span style="background:#2196F3"></span></label>
                    <label><input type="radio" name="event_color" value="#FF9800"><span style="background:#FF9800"></span></label>
                    <label><input type="radio" name="event_color" value="#F44336"><span style="background:#F44336"></span></label>
                    <label><input type="radio" name="event_color" value="#9C27B0"><span style="background:#9C27B0"></span></label>
                    <label><input type="radio" name="event_color" value="#607D8B"><span style="background:#607D8B"></span></label>
                </div>
            </div>

            <div class="input-group">
                <label><?= $t['attach_media']; ?></label>
                <input type="file" name="event_media" accept="image/*,audio/*,video/*">
            </div>

            <div class="input-group checkbox-group">
                <label>
                    <input type="checkbox" name="enable_notification" value="1" checked>
                    <span><?= $t['notify_before']; ?></span>
                </label>
            </div>

            <button type="submit" class="btn"><?= $t['save_event']; ?></button>
        </form>
    </div>
</div>

<!-- Modal Editar Evento -->
<div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2><i class="fas fa-edit"></i> <?= $t['edit_event']; ?></h2>
        
        <form action="/schedule/update" method="post" enctype="multipart/form-data" id="editForm">
            <input type="hidden" name="event_id" id="editEventId">
            <input type="hidden" name="event_date" id="editEventDate">
            
            <div class="input-group">
                <label>Título *</label>
                <input type="text" name="event_title" id="editEventTitle" required>
            </div>

            <div class="input-group">
                <label>Horário *</label>
                <input type="time" name="event_time" id="editEventTime" required>
            </div>

            <div class="input-group">
                <label>Local</label>
                <input type="text" name="event_location" id="editEventLocation">
            </div>

            <div class="input-group">
                <label>Descrição</label>
                <textarea name="notes" id="editEventNotes" rows="3"></textarea>
            </div>

            <div class="input-group">
                <label>Cor do Evento</label>
                <div class="color-picker" id="editColorPicker">
                    <label><input type="radio" name="event_color" value="#4CAF50"><span style="background:#4CAF50"></span></label>
                    <label><input type="radio" name="event_color" value="#2196F3"><span style="background:#2196F3"></span></label>
                    <label><input type="radio" name="event_color" value="#FF9800"><span style="background:#FF9800"></span></label>
                    <label><input type="radio" name="event_color" value="#F44336"><span style="background:#F44336"></span></label>
                    <label><input type="radio" name="event_color" value="#9C27B0"><span style="background:#9C27B0"></span></label>
                    <label><input type="radio" name="event_color" value="#607D8B"><span style="background:#607D8B"></span></label>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn" style="flex: 1;"><?= $t['save_changes_btn']; ?></button>
                <a href="#" id="deleteEventBtn" class="btn" style="flex: 1; background: rgba(220,53,69,0.2); border-color: rgba(220,53,69,0.4); text-align: center;" onclick="return confirm('<?= $t['delete_event']; ?>')">
                    <i class="fas fa-trash"></i> <?= $t['delete']; ?>
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.schedule-layout {
    display: flex;
    gap: 20px;
    max-width: 1200px;
    margin: 20px auto;
    position: relative;
    justify-content: center;
}

.calendar-card {
    width: 800px;
    padding: 30px;
    transition: all 0.3s ease;
}

.events-sidebar {
    width: 320px;
    background: rgba(20, 20, 25, 0.6);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-self: flex-start;
    max-height: 100%;
}

.events-sidebar.collapsed {
    width: 60px;
    min-height: 60px;
    height: fit-content;
    cursor: pointer;
}

.events-sidebar.collapsed .sidebar-header {
    padding: 14px;
    border-bottom: none;
    justify-content: center;
}

.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
    user-select: none;
}

.sidebar-header:hover {
    background: rgba(255, 255, 255, 0.03);
}

.sidebar-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: var(--primary-text-color);
    white-space: nowrap;
    transition: opacity 0.3s ease;
}

.events-sidebar.collapsed .sidebar-header h3 {
    opacity: 0;
    width: 0;
}

.toggle-sidebar {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    pointer-events: none;
}

.toggle-sidebar i {
    transition: transform 0.3s ease;
    pointer-events: none;
}

.toggle-sidebar i {
    transition: transform 0.3s ease;
}

.sidebar-content {
    padding: 15px;
    overflow-y: auto;
    transition: all 0.3s ease;
}

.events-sidebar.collapsed .sidebar-content {
    display: none;
}

.sidebar-content::-webkit-scrollbar {
    width: 8px;
}

.sidebar-content::-webkit-scrollbar-track {
    background: rgba(10, 10, 15, 0.5);
    border-radius: 10px;
}

.sidebar-content::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, rgba(255,255,255,0.3), rgba(255,255,255,0.15));
    border-radius: 10px;
    border: 2px solid rgba(10, 10, 15, 0.5);
}

.sidebar-content::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, rgba(255,255,255,0.5), rgba(255,255,255,0.3));
}

.sidebar-event {
    background: rgba(255, 255, 255, 0.05);
    border-left: 4px solid;
    padding: 12px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.sidebar-event:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.sidebar-event-date {
    font-size: 0.75rem;
    color: var(--secondary-text-color);
    margin-bottom: 5px;
}

.sidebar-event-title {
    font-size: 0.95rem;
    color: var(--primary-text-color);
    font-weight: 500;
    margin-bottom: 3px;
}

.sidebar-event-time {
    font-size: 0.8rem;
    color: var(--secondary-text-color);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.calendar-header h3 {
    margin: 0;
    font-size: 1.3rem;
    color: var(--primary-text-color);
    text-transform: capitalize;
}

.nav-btn {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.nav-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.4);
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
}

.calendar-day-header {
    text-align: center;
    padding: 8px;
    font-weight: 500;
    color: var(--secondary-text-color);
    font-size: 0.8rem;
    text-transform: uppercase;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    font-size: 0.9rem;
    color: var(--primary-text-color);
}

.calendar-day .day-number {
    transition: all 0.3s ease;
}

.calendar-day.expanded {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.4);
    z-index: 10;
}

.calendar-day.expanded .day-number {
    font-size: 0.7rem;
    margin-bottom: 4px;
}

.day-actions {
    display: none;
    flex-direction: column;
    gap: 4px;
    width: 100%;
    padding: 0 4px;
}

.calendar-day.expanded .day-actions {
    display: flex;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
}

.day-action-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    padding: 4px;
    border-radius: 4px;
    font-size: 0.7rem;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.day-action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
}

.nav-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.4);
}

.day-action-btn.create {
    background: rgba(76, 175, 80, 0.2);
    border-color: rgba(76, 175, 80, 0.4);
}

.day-action-btn.delete {
    background: rgba(220, 53, 69, 0.2);
    border-color: rgba(220, 53, 69, 0.4);
}

.calendar-day:hover:not(.other-month) {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

.calendar-day.other-month {
    opacity: 0.2;
    cursor: default;
}

.calendar-day.today {
    border: 2px solid #4CAF50;
    font-weight: 600;
}

.calendar-day.has-events {
    background: rgba(76, 175, 80, 0.15);
    border-color: rgba(76, 175, 80, 0.4);
}

.event-dots {
    position: absolute;
    bottom: 3px;
    display: flex;
    gap: 2px;
}

.event-dot {
    width: 4px;
    height: 4px;
    border-radius: 50%;
}



.color-picker {
    display: flex;
    gap: 10px;
}

.color-picker label {
    cursor: pointer;
    position: relative;
}

.color-picker input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.color-picker span {
    display: block;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.color-picker input[type="radio"]:checked + span {
    border-color: #fff;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
}

.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
    display: flex !important;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    overflow-y: auto;
}

.modal::-webkit-scrollbar {
    width: 0;
    height: 0;
    display: none;
}

.modal[style*="display: none"] {
    display: none !important;
}

.modal-content {
    background: rgba(20, 20, 25, 0.95);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    padding: 30px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    width: 90%;
    max-width: 600px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
    position: relative;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.5);
}

.modal-content h2 {
    color: var(--primary-text-color);
    margin-bottom: 20px;
}

.modal-content .close {
    position: absolute;
    top: 15px;
    right: 20px;
    color: var(--secondary-text-color);
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.modal-content .close:hover {
    color: var(--primary-text-color);
}

.modal-content::-webkit-scrollbar {
    width: 3px;
}

.modal-content::-webkit-scrollbar-track {
    background: transparent !important;
}

.modal-content::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>

<script>
let currentDate = new Date();
const events = <?= json_encode(array_map(function($e) {
    return [
        'id' => $e->id,
        'title' => $e->title,
        'date' => $e->eventDate,
        'location' => $e->location ?? '',
        'notes' => $e->notes ?? '',
        'color' => $e->color ?? '#4CAF50'
    ];
}, $events ?? [])); ?>;

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    const lang = '<?= $lang ?>';
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
                dot.style.background = e.color;
                dots.appendChild(dot);
            });
            day.appendChild(dots);
            
            dayEvents.forEach(e => {
                const editBtn = document.createElement('button');
                editBtn.className = 'day-action-btn';
                editBtn.innerHTML = `<i class="fas fa-edit"></i> ${e.title.substring(0, 10)}`;
                editBtn.onclick = (ev) => {
                    ev.stopPropagation();
                    openEditModal(e);
                };
                actions.appendChild(editBtn);
            });
        } else {
            const createBtn = document.createElement('button');
            createBtn.className = 'day-action-btn create';
            createBtn.innerHTML = '<i class="fas fa-plus"></i> <?= $t['create']; ?>';
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
    const lang = '<?= $lang ?>';
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
    
    content.innerHTML = sortedEvents.map((e, idx) => {
        const date = new Date(e.date);
        return `
            <div class="sidebar-event" style="border-left-color: ${e.color}" onclick="openEditModalFromSidebar(${idx})">
                <div class="sidebar-event-date">${date.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                <div class="sidebar-event-title">${e.title}</div>
                <div class="sidebar-event-time"><i class="fas fa-clock"></i> ${date.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}</div>
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


</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
