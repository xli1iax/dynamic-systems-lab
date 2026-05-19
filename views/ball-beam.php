<!DOCTYPE html>
<html lang="<?= htmlspecialchars(getLang()) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('ball_beam') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<nav class="language-switcher">
    <a href="<?= langUrl('sk') ?>">SK</a>
    <span>|</span>
    <a href="<?= langUrl('en') ?>">EN</a>
</nav>

<main class="simulation-page">
    <div class="simulation-shell">

        <div class="simulation-header">
            <a href="/" class="back-btn"><?= t('back_dashboard') ?></a>
            <h1><?= t('ball_beam') ?></h1>
            <p><?= t('ball_beam_page_description') ?></p>
        </div>

        <form id="ballBeamForm" class="simulation-controls">
            <div class="input-group">
                <label for="r"><?= t('target_position_r') ?></label>
                <input type="number" id="r" name="r" step="0.01" value="0.25">
            </div>

            <div class="input-group">
                <label for="duration"><?= t('simulation_duration') ?></label>
                <input type="number" id="duration" name="duration" step="0.1" value="5">
            </div>

            <div class="input-group">
                <label for="step"><?= t('simulation_step') ?></label>
                <input type="number" id="step" name="step" step="0.005" value="0.01">
            </div>

            <div class="input-group">
                <label for="initPosition"><?= t('initial_position') ?></label>
                <input type="number" id="initPosition" name="initPosition" step="0.01" value="0">
            </div>

            <div class="input-group">
                <label for="initVelocity"><?= t('initial_velocity') ?></label>
                <input type="number" id="initVelocity" name="initVelocity" step="0.01" value="0">
            </div>

            <div class="input-group">
                <label for="initAngle"><?= t('initial_angle') ?></label>
                <input type="number" id="initAngle" name="initAngle" step="0.01" value="0">
            </div>

            <div class="input-group">
                <label for="initAngularVelocity"><?= t('initial_angular_velocity') ?></label>
                <input type="number" id="initAngularVelocity" name="initAngularVelocity" step="0.01" value="0">
            </div>

            <button type="submit" id="runBtn"><?= t('run_simulation') ?></button>
        </form>

        <section class="simulation-layout">
            <div class="simulation-card">
                <h2><?= t('animation') ?></h2>
                <canvas id="ballBeamCanvas" width="1000" height="500"></canvas>
            </div>

            <div class="simulation-card">
                <h2><?= t('graph') ?></h2>
                <canvas id="ballBeamChart"></canvas>
            </div>
        </section>

    </div>
</main>

<script>
    window.API_KEY = <?= json_encode(getApiKey()) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
<script src="/js/ball-beam.js"></script>
</body>
</html>