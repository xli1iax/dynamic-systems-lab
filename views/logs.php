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

            <div class="logs-preview">
                <h2><?= t('logs_preview') ?></h2>

                <div class="logs-table-wrapper">
                    <table class="logs-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th><?= t('source') ?></th>
                            <th><?= t('command') ?></th>
                            <th><?= t('success') ?></th>
                            <th><?= t('error_message') ?></th>
                            <th><?= t('ip_address') ?></th>
                            <th><?= t('created_at') ?></th>
                        </tr>
                        </thead>
                        <tbody id="logsTableBody">
                        <tr>
                            <td colspan="7"><?= t('loading_logs') ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </div>
</main>

<script>
    window.API_KEY = <?= json_encode(getApiKey()) ?>;

    async function loadLogs() {
        const tbody = document.getElementById('logsTableBody');

        try {
            const response = await fetch('/api/logs', {
                headers: {
                    'X-API-KEY': window.API_KEY
                }
            });

            const json = await response.json();
            const logs = json.data ?? [];

            if (logs.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7">No logs found</td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = logs.map(log => `
                <tr>
                    <td>${log.id ?? '-'}</td>
                    <td>${log.source ?? '-'}</td>
                    <td class="log-command">${escapeHtml(log.command ?? '-')}</td>
                    <td>${Number(log.success) === 1 ? 'OK' : 'ERROR'}</td>
                    <td>${escapeHtml(log.error_message ?? '-')}</td>
                    <td>${log.ip_address ?? '-'}</td>
                    <td>${log.created_at ?? '-'}</td>
                </tr>
            `).join('');

        } catch (error) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7">${escapeHtml(error.message)}</td>
                </tr>
            `;
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    loadLogs();
</script>

</body>
</html>