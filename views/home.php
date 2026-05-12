<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main class="container">
    <h1><?= htmlspecialchars($title) ?></h1>
    <p><?= htmlspecialchars($message) ?></p>

    <button id="testBtn">Test API</button>
    <pre id="output"></pre>
</main>

<script src="/js/app.js"></script>
</body>
</html>