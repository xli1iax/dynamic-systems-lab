<!DOCTYPE html>
<html lang="<?= htmlspecialchars(getLang()) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('logs') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/css/style.css">
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

            <h1><?= t('logs') ?></h1>

            <p><?= t('logs_page_description') ?></p>
        </div>

        <section class="simulation-card">

            <div class="logs-actions">
                <a href="/api/logs/export" class="export-btn">
                    <?= t('export_csv') ?>
                </a>
            </div>

        </section>

    </div>
</main>

</body>
</html>