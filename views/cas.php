<!DOCTYPE html>
<html lang="<?= htmlspecialchars(getLang()) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('cas_form') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/material-darker.min.css">
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
            <h1><?= t('cas_form') ?></h1>
            <p><?= t('cas_page_description') ?></p>
        </div>

        <section class="simulation-card">
            <form id="casForm" class="cas-form">
                <label for="command"><?= t('cas_command') ?></label>

                <textarea id="command" name="command">a=1+1</textarea>

                <button type="submit" id="sendCasBtn">
                    <?= t('send_request') ?>
                </button>
            </form>
        </section>

        <section class="simulation-card">
            <h2><?= t('output') ?></h2>
            <pre id="casOutput" class="cas-output">---</pre>
        </section>

    </div>
</main>

<script>
    window.API_KEY = <?= json_encode(getApiKey()) ?>;
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/octave/octave.min.js"></script>

<script>
    const textarea = document.getElementById('command');

    const editor = CodeMirror.fromTextArea(textarea, {
        lineNumbers: true,
        mode: 'octave',
        theme: 'material-darker',
        indentUnit: 4,
        lineWrapping: true
    });

    document.getElementById('casForm').addEventListener('submit', async (event) => {
        event.preventDefault();

        const command = editor.getValue();
        const output = document.getElementById('casOutput');

        output.textContent = 'Loading...';

        try {
            const response = await fetch('/api/cas/execute', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-API-KEY': window.API_KEY
                },
                body: JSON.stringify({
                    command: command,
                    source: 'form'
                })
            });

            const data = await response.json();

            if (!data.success) {
                output.textContent = data.error || 'Error';
                return;
            }

            output.textContent = JSON.stringify(data.result, null, 2);

        } catch (error) {
            output.textContent = error.message;
        }
    });
</script>

</body>
</html>