<?php require __DIR__ . '/../layouts/header.php'; ?>
<style>
.premium-container {
    max-width: 1100px;
    margin: 30px auto;
    padding: 0 20px;
}
.premium-header {
    text-align: center;
    margin-bottom: 50px;
}
.premium-header h1 {
    font-size: 2rem;
    color: var(--primary-text-color);
    margin-bottom: 10px;
}
.premium-header p {
    font-size: 1rem;
    color: var(--secondary-text-color);
}
.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}
.plan-card {
    background: var(--glass-bg);
    border: 1px solid var(--border-color);
    padding: 30px;
    transition: all 0.3s ease;
}
.plan-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
}
.plan-card.featured {
    border-width: 2px;
    position: relative;
}
.plan-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #E8E8E8, #A8A8A8, #E8E8E8);
    border: 1px solid rgba(200, 200, 200, 0.5);
    color: #000;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(200, 200, 200, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.5);
}
.plan-name {
    font-size: 1.4rem;
    color: var(--primary-text-color);
    margin-bottom: 15px;
}
.plan-price {
    font-size: 2.5rem;
    font-weight: 600;
    color: var(--primary-text-color);
    margin-bottom: 5px;
}
.plan-price small {
    font-size: 1rem;
    color: var(--secondary-text-color);
    font-weight: 400;
}
.plan-features {
    list-style: none;
    padding: 0;
    margin: 25px 0;
}
.plan-features li {
    padding: 10px 0;
    color: var(--secondary-text-color);
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 0.9rem;
}
.plan-features li i {
    color: var(--primary-text-color);
    margin-top: 2px;
}
.plan-features li.unavailable {
    opacity: 0.4;
}
.plan-btn {
    width: 100%;
    padding: 12px;
    background: transparent;
    border: 1px solid var(--border-color);
    color: var(--primary-text-color);
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
}
.plan-btn:hover {
    background: var(--hover-bg);
    border-color: var(--border-color);
}
.plan-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
<div class="premium-container">
    <div class="premium-header">
        <h1><?= $t['plans_pricing']; ?></h1>
        <p><?= $t['choose_plan']; ?></p>
    </div>
    <div class="pricing-grid">
        <div class="plan-card">
            <h2 class="plan-name"><?= $t['free_plan']; ?></h2>
            <div class="plan-price">R$ 0<small><?= $t['per_month']; ?></small></div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> <?= $t['basic_portfolio']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['event_schedule']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['basic_ai']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['simple_dashboard']; ?></li>
                <li class="unavailable"><i class="fas fa-times"></i> <?= $t['advanced_analytics']; ?></li>
                <li class="unavailable"><i class="fas fa-times"></i> <?= $t['ai_insights']; ?></li>
            </ul>
            <button class="plan-btn" disabled><?= $t['current_plan']; ?></button>
        </div>
        <div class="plan-card featured">
            <span class="plan-badge"><?= $t['recommended']; ?></span>
            <h2 class="plan-name"><?= $t['premium_plan']; ?></h2>
            <div class="plan-price">R$ 29,90<small><?= $t['per_month']; ?></small></div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> <?= $t['everything_free']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['unlimited_ai']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['marketing_analysis']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['error_identification']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['advanced_dashboard']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['priority_support']; ?></li>
            </ul>
            <button class="plan-btn" onclick="alert('<?= $t['coming_soon']; ?>');"><?= $t['subscribe']; ?></button>
        </div>
        <div class="plan-card">
            <h2 class="plan-name"><?= $t['premium_plus_plan']; ?></h2>
            <div class="plan-price">R$ 49,90<small><?= $t['per_month']; ?></small></div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> <?= $t['everything_premium']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['monthly_consulting']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['competitor_analysis']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['custom_plan']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['detailed_reports']; ?></li>
                <li><i class="fas fa-check"></i> <?= $t['vip_support']; ?></li>
            </ul>
            <button class="plan-btn" onclick="alert('<?= $t['coming_soon']; ?>');"><?= $t['subscribe']; ?></button>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
