<!DOCTYPE html>
<html lang="<?= htmlspecialchars(getLang()) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('home_title') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<nav class="language-switcher">
    <a href="<?= langUrl('sk') ?>">SK</a>
    <span>|</span>
    <a href="<?= langUrl('en') ?>">EN</a>
</nav>

<main class="home-page">
    <section class="hero">
        <div class="hero-badge">WEBTE2 • LS 2025/2026</div>

        <h1><?= t('home_title') ?></h1>

        <p><?= t('home_description') ?></p>
    </section>

    <section class="dashboard-section">
        <h2><?= t('calculations') ?></h2>

        <div class="dashboard-grid single">
            <a href="/cas" class="dashboard-card">
                <div class="card-icon">⌘</div>
                <div>
                    <h3><?= t('cas_form') ?></h3>
                    <p><?= t('cas_description') ?></p>
                    <span><?= t('open_cas') ?></span>
                </div>
            </a>
        </div>
    </section>

    <section class="dashboard-section">
        <h2><?= t('simulations') ?></h2>

        <div class="dashboard-grid">
            <a href="/animations/pendulum" class="dashboard-card">
                <div class="card-icon">⟲</div>
                <div>
                    <h3><?= t('pendulum') ?></h3>
                    <p><?= t('pendulum_description') ?></p>
                    <span><?= t('open_simulation') ?></span>
                </div>
            </a>

            <a href="/animations/ball-beam" class="dashboard-card">
                <div class="card-icon">●</div>
                <div>
                    <h3><?= t('ball_beam') ?></h3>
                    <p><?= t('ball_beam_description') ?></p>
                    <span><?= t('open_simulation') ?></span>
                </div>
            </a>
        </div>
    </section>

    <section class="dashboard-section">
        <h2><?= t('data_admin') ?></h2>

        <div class="dashboard-grid">
            <a href="/statistics" class="dashboard-card">
                <div class="card-icon">◔</div>
                <div>
                    <h3><?= t('statistics') ?></h3>
                    <p><?= t('statistics_description') ?></p>
                    <span><?= t('view_statistics') ?></span>
                </div>
            </a>

            <a href="/logs" class="dashboard-card">
                <div class="card-icon">☰</div>
                <div>
                    <h3><?= t('logs') ?></h3>
                    <p><?= t('logs_description') ?></p>
                    <span><?= t('view_logs') ?></span>
                </div>
            </a>
        </div>
    </section>

    <section class="dashboard-section">
        <h2><?= t('documentation') ?></h2>

        <div class="dashboard-grid">
            <a href="/docs" class="dashboard-card">
                <div class="card-icon">{ }</div>
                <div>
                    <h3><?= t('openapi_docs') ?></h3>
                    <p><?= t('openapi_description') ?></p>
                    <span><?= t('open_docs') ?></span>
                </div>
            </a>

            <a href="/documentation" class="dashboard-card">
                <div class="card-icon">PDF</div>
                <div>
                    <h3><?= t('pdf_docs') ?></h3>
                    <p><?= t('pdf_description') ?></p>
                    <span><?= t('open_pdf') ?></span>
                </div>
            </a>
        </div>
    </section>
</main>

</body>
</html>