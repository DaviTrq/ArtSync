<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/background.php'; ?>
<script>
    window.dashboardTranslations = { week: '<?= $t['week']; ?>' };
</script>
<script src="/js/dashboard.js"></script>
<header class="main-header">
    <h2><?= $t['welcome']; ?>, <?php echo htmlspecialchars($_SESSION['artist_name'] ?? 'Artista'); ?>!</h2>
    <p><?= $t['career_summary']; ?></p>
</header>
<div class="card chart-container">
    <h3><?= $t['streams_overview']; ?></h3>
    <div style="position: relative; height: 400px; width: 100%;">
        <canvas id="streamsChart"></canvas> 
    </div>
</div>
<div class="widgets-grid">
    <a href="/schedule" class="widget-link">
        <div class="widget">
            <h3><?= $t['upcoming_events']; ?></h3>
            <p><?= $t['upcoming_events_desc']; ?></p>
        </div>
    </a>
    <a href="/connect-spotify" class="widget-link"> 
        <div class="widget">
            <h3><?= $t['spotify_metrics']; ?></h3>
            <p><?= $t['integration_pending']; ?></p>
        </div>
    </a>
    <a href="/ai" class="widget-link">
        <div class="widget">
            <h3><?= $t['ai_career_widget']; ?></h3>
            <p><?= $t['ai_career_desc']; ?></p>
        </div>
    </a>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
