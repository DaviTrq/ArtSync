<?php require __DIR__ . '/../layouts/header.php'; ?>
<link rel="stylesheet" href="/css/schedule.css">
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
                <label><?= $t['priority_level']; ?></label>
                <select name="priority" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: var(--primary-text-color); font-family: 'Poppins', sans-serif;">
                    <option value="low"><?= $t['priority_low']; ?></option>
                    <option value="medium"><?= $t['priority_medium']; ?></option>
                    <option value="urgent"><?= $t['priority_urgent']; ?></option>
                </select>
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
                <label><?= $t['title']; ?> *</label>
                <input type="text" name="event_title" id="editEventTitle" required>
            </div>
            <div class="input-group">
                <label><?= $t['time']; ?> *</label>
                <input type="time" name="event_time" id="editEventTime" required>
            </div>
            <div class="input-group">
                <label><?= $t['location']; ?></label>
                <input type="text" name="event_location" id="editEventLocation">
            </div>
            <div class="input-group">
                <label><?= $t['description']; ?></label>
                <textarea name="notes" id="editEventNotes" rows="3"></textarea>
            </div>
            <div class="input-group">
                <label><?= $t['priority_level']; ?></label>
                <select name="priority" id="editPriority" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: var(--primary-text-color); font-family: 'Poppins', sans-serif;">
                    <option value="low"><?= $t['priority_low']; ?></option>
                    <option value="medium"><?= $t['priority_medium']; ?></option>
                    <option value="urgent"><?= $t['priority_urgent']; ?></option>
                </select>
            </div>
            <div class="input-group">
                <label><?= $t['event_color']; ?></label>
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
<script>
    window.scheduleEvents = <?= json_encode(array_map(function($e) {
        return [
            'id' => $e->id,
            'title' => $e->title,
            'date' => $e->eventDate,
            'location' => $e->location ?? '',
            'notes' => $e->notes ?? '',
            'color' => $e->color ?? '#4CAF50',
            'priority' => $e->priority ?? 'low'
        ];
    }, $events ?? [])); ?>;
    window.scheduleLang = '<?= $lang ?>';
    window.scheduleTranslations = { create: '<?= $t['create']; ?>' };
</script>
<script src="/js/schedule.js"></script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
