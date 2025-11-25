<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="search-container">
    <div class="search-header">
        <h2><?= $t['search']; ?></h2>
        <form method="GET" action="/search" class="search-form">
            <input type="text" name="q" placeholder="<?= $t['search_placeholder']; ?>" value="<?= htmlspecialchars($query); ?>" autofocus>
            <button type="submit" class="btn"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <?php if ($query): ?>
        <!-- Funcionalidades -->
        <?php if (!empty($results['features'])): ?>
            <div class="results-section">
                <h3><i class="fas fa-star"></i> <?= $t['features']; ?></h3>
                <div class="results-list">
                    <?php foreach ($results['features'] as $feature): ?>
                        <a href="<?= $feature['url']; ?>" class="result-item">
                            <i class="fas <?= $feature['icon']; ?>"></i>
                            <span><?= htmlspecialchars($feature['name']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Pessoas -->
        <?php if (!empty($results['users'])): ?>
            <div class="results-section">
                <h3><i class="fas fa-users"></i> <?= $t['people']; ?></h3>
                <div class="results-list">
                    <?php foreach ($results['users'] as $user): ?>
                        <a href="/network?search=<?= urlencode($user['artist_name']); ?>" class="result-item">
                            <div class="user-avatar-small">
                                <?= strtoupper(substr($user['artist_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <strong><?= htmlspecialchars($user['artist_name']); ?></strong>
                                <small><?= htmlspecialchars($user['email']); ?></small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tópicos -->
        <?php if (!empty($results['topics'])): ?>
            <div class="results-section">
                <h3><i class="fas fa-comments"></i> <?= $t['forum_topics_search']; ?></h3>
                <div class="results-list">
                    <?php foreach ($results['topics'] as $topic): ?>
                        <a href="/forum/view?id=<?= $topic['id']; ?>" class="result-item">
                            <i class="fas fa-comment"></i>
                            <div>
                                <strong><?= htmlspecialchars($topic['title']); ?></strong>
                                <small><?= $t['by']; ?> <?= htmlspecialchars($topic['artist_name']); ?></small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($results['features']) && empty($results['users']) && empty($results['topics'])): ?>
            <p style="color: var(--secondary-text-color); text-align: center; margin-top: 50px;">
                <?= $t['no_results']; ?> "<?= htmlspecialchars($query); ?>"
            </p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.search-container { max-width: 900px; margin: 0 auto; }
.search-header { margin-bottom: 30px; }
.search-header h2 { color: var(--primary-text-color); margin-bottom: 15px; }
.search-form { display: flex; gap: 10px; }
.search-form input { flex: 1; padding: 12px 18px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: var(--primary-text-color); font-family: 'Poppins', sans-serif; font-size: 1rem; }
.results-section { margin-bottom: 30px; }
.results-section h3 { color: var(--primary-text-color); margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
.results-list { display: flex; flex-direction: column; gap: 10px; }
.result-item { display: flex; align-items: center; gap: 15px; padding: 15px; background: rgba(20, 20, 25, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); text-decoration: none; color: var(--primary-text-color); transition: 0.2s; }
.result-item:hover { background: rgba(30, 30, 35, 0.7); border-color: rgba(255, 255, 255, 0.2); }
.result-item i { font-size: 1.5rem; color: var(--secondary-text-color); min-width: 30px; text-align: center; }
.result-item strong { display: block; color: var(--primary-text-color); margin-bottom: 4px; }
.result-item small { color: var(--secondary-text-color); font-size: 0.85rem; }
.user-avatar-small { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #c0c0c0, #808080); display: flex; align-items: center; justify-content: center; font-weight: 600; color: #000; flex-shrink: 0; }
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
