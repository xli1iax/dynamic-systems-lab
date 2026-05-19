<!DOCTYPE html>
<html lang="<?= htmlspecialchars(getLang()) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('api_documentation') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css" />
</head>

<body class="api-docs-page">

<nav class="language-switcher">
    <a href="<?= langUrl('sk') ?>">SK</a>
    <span>|</span>
    <a href="<?= langUrl('en') ?>">EN</a>
</nav>

<div class="api-docs-shell">
    <div id="swagger-ui"></div>
</div>

<script src="https://unpkg.com/swagger-ui-dist/swagger-ui-bundle.js"></script>

<script>
    window.onload = function () {
        SwaggerUIBundle({
            url: "/openapi/openapi.json?v=3",
            dom_id: "#swagger-ui",
            deepLinking: true,
            docExpansion: "list",
            defaultModelsExpandDepth: 2,
            defaultModelExpandDepth: 2,
            displayRequestDuration: true,
            tryItOutEnabled: true,
            presets: [
                SwaggerUIBundle.presets.apis
            ]
        });
    };
</script>

</body>
</html>