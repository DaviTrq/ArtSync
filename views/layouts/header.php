<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$lang = $_SESSION['lang'] ?? 'pt-BR';
$translations = require __DIR__ . '/../../config/lang.php';
$t = $translations[$lang] ?? $translations['pt-BR'];

$notification_count = 0;
$notifications = [];
if (!empty($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../app/Services/NotificationService.php';
    $notifService = new \App\Services\NotificationService();
    $notifService->generateNotifications($_SESSION['user_id']);
    $notifications = $notifService->getUnreadNotifications($_SESSION['user_id']);
    $connectionNotifications = $notifService->getConnectionNotifications($_SESSION['user_id']);
    $notifications = array_merge($notifications, $connectionNotifications);
    if (!empty($_SESSION['is_admin'])) {
        $adminNotifications = $notifService->getAdminNotifications($_SESSION['user_id']);
        $notifications = array_merge($notifications, $adminNotifications);
    }
    $notification_count = count($notifications);
}

$avatarSrc = '/uploads/profile/default.png';
if (!empty($_SESSION['user_id'])) {
    $uid = (int) $_SESSION['user_id'];
    $base = __DIR__ . '/../../public/uploads/profile';
    $web = '/uploads/profile';
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        $p = "{$base}/user_{$uid}.{$ext}";
        if (file_exists($p)) {
            $avatarSrc = "{$web}/user_{$uid}.{$ext}?t=" . time();
            break;
        }
    }
    if (!empty($_SESSION['user_profile'])) {
        $avatarSrc = $_SESSION['user_profile'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'ArtSync'); ?></title>

    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="/css/portfolio.css">
    <link rel="stylesheet" href="/css/premium.css">
    <link rel="stylesheet" href="/css/light-theme-fix.css">


    <?php if (isset($currentPage) && $currentPage === 'portfolio'): ?>
        <link rel="stylesheet" href="/css/portfolio.css">
    <?php endif; ?>

    <?php if (isset($currentPage) && $currentPage === 'ai'): ?>
        <link rel="stylesheet" href="/css/portfolio.css">
    <?php endif; ?>

    <?php if (isset($currentPage) && ($currentPage === 'login' || $currentPage === 'register')): ?>
        <link rel="stylesheet" href="/css/login_register.css">
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="/js/sidebar.js" defer></script>
    <script src="/js/premium.js" defer></script>
    <script src="/js/notifications.js" defer></script>
    <script>
        (function() {
            const currentPage = '<?= $currentPage ?? ''; ?>';
            if (currentPage !== 'login' && currentPage !== 'register' && currentPage !== 'landing') {
                const savedTheme = localStorage.getItem('theme') || 'dark';
                if (savedTheme === 'light') {
                    document.documentElement.classList.add('light-theme');
                }
            }
        })();
    </script>
</head>

<body class="<?= ($currentPage === 'login' || $currentPage === 'register') ? 'centered-body' : ''; ?>">

    <div class="background-waves"></div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="dashboard-container">

            <aside class="sidebar">
                <a href="/dashboard" class="logo">
                    <img src="/images/artsync.png" alt="Art Sync Logo" style="height: 50px; margin-bottom: 30px;">
                </a>
                <nav>
                    <ul>
                        <li class="<?= ($currentPage === 'dashboard') ? 'active' : ''; ?>"><a href="/dashboard"><i class="fas fa-home"></i><span class="menu-text"><?= $t['dashboard']; ?></span></a></li>
                        <li class="<?= ($currentPage === 'portfolio') ? 'active' : ''; ?>"><a href="/portfolio"><i class="fas fa-user-circle"></i><span class="menu-text"><?= $t['portfolio']; ?></span></a></li>
                        <li class="<?= ($currentPage === 'schedule') ? 'active' : ''; ?>"><a href="/schedule"><i class="fas fa-calendar-alt"></i><span class="menu-text"><?= $t['schedule']; ?></span></a></li>
                        <li class="<?= ($currentPage === 'forum') ? 'active' : ''; ?>"><a href="/forum"><i class="fas fa-comments"></i><span class="menu-text">Fórum</span></a></li>
                        <li class="<?= ($currentPage === 'ia') ? 'active' : ''; ?>"><a href="/ai"><i class="fas fa-robot"></i><span class="menu-text"><?= $t['ai_career']; ?></span></a></li>
                        <?php if (!empty($_SESSION['is_admin'])): ?>
                            <li class="<?= ($currentPage === 'admin') ? 'active' : ''; ?>"><a href="/admin"><i class="fas fa-user-shield"></i><span class="menu-text">Admin</span></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div class="sidebar-footer">
                    <div class="notifications">
                        <a href="#" id="notification-bell">
                            <i class="fas fa-bell"></i>
                            <?php if ($notification_count > 0): ?>
                                <span class="notification-count"><?= $notification_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="notification-dropdown" id="notification-dropdown">
                            <?php if (empty($notifications)): ?>
                                <p style="padding: 15px; text-align: center; color: var(--secondary-text-color);"><?= $t['no_new_notifications']; ?></p>
                            <?php else: ?>
                                <?php foreach ($notifications as $notif): ?>
                                    <?php if (isset($notif['from_user_id'])): ?>
                                        <div class="notification-item" data-id="<?= $notif['id']; ?>" data-type="connection">
                                            <i class="fas fa-user-plus"></i>
                                            <div class="notification-text">
                                                <p><?= htmlspecialchars($notif['message']); ?></p>
                                                <small><?= date('d/m/Y H:i', strtotime($notif['created_at'])); ?></small>
                                                <div style="margin-top: 8px; display: flex; gap: 8px;">
                                                    <a href="/profile/view?id=<?= $notif['from_user_id']; ?>" class="btn-small" style="font-size: 11px; padding: 4px 8px;"><?= $t['view_profile']; ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php elseif (isset($notif['topic_id'])): ?>
                                        <div class="notification-item admin-notif" data-id="<?= $notif['id']; ?>" data-type="admin">
                                            <i class="fas fa-comment-dots"></i>
                                            <div class="notification-text">
                                                <p><?= htmlspecialchars($notif['message']); ?></p>
                                                <small><?= date('d/m/Y H:i', strtotime($notif['created_at'])); ?></small>
                                                <div style="margin-top: 8px; display: flex; gap: 8px;">
                                                    <a href="/forum/approve?id=<?= $notif['topic_id']; ?>" class="btn-small" style="font-size: 11px; padding: 4px 8px;">✓ <?= $t['approve_btn']; ?></a>
                                                    <a href="/forum/view?id=<?= $notif['topic_id']; ?>" class="btn-small" style="font-size: 11px; padding: 4px 8px; background: rgba(255,255,255,0.05);"><?= $t['view_btn']; ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="notification-item" data-id="<?= $notif['id']; ?>">
                                            <i class="fas fa-calendar-check"></i>
                                            <div class="notification-text">
                                                <p><?= htmlspecialchars($notif['message']); ?></p>
                                                <small><?= date('d/m/Y', strtotime($notif['created_at'])); ?></small>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <button class="btn-small" onclick="markAllRead()" style="width: 100%; margin-top: 10px;"><?= $t['mark_all_read']; ?></button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="/settings" class="settings-icon <?= ($currentPage === 'settings') ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i>
                    </a>
                </div>
            </aside>

            <header class="topbar">
                <div class="topbar-content">
                    <h2 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard'); ?></h2>

                    <div class="profile-menu">
                        <a href="/search" class="search-icon" title="Buscar">
                            <i class="fas fa-search"></i>
                        </a>
                        <div class="profile-avatar" id="profileAvatar">
                            <?php if (file_exists(__DIR__ . '/../../public' . $avatarSrc) && $avatarSrc !== '/uploads/profile/default.png'): ?>
                                <img src="<?= htmlspecialchars($avatarSrc); ?>" alt="Avatar">
                            <?php else: 
                                $userName = $_SESSION['artist_name'] ?? $_SESSION['user_name'] ?? 'U';
                                $initial = strtoupper(substr(trim($userName), 0, 1));
                            ?>
                                <span class="avatar-initial"><?= htmlspecialchars($initial); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="profile-dropdown" id="profileDropdown">
                            <a href="/settings" class="dropdown-item">
                                <i class="fas fa-user-edit"></i> <?= $t['edit_profile']; ?>
                            </a>
                            <a href="/settings" class="dropdown-item">
                                <i class="fas fa-cog"></i> <?= $t['settings']; ?>
                            </a>
                            <hr>
                            <a href="/logout" class="dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i> <?= $t['logout']; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="main-content">
            <?php else: ?>
            <?php endif; ?>