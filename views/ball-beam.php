<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Gulička na tyči</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<main class="simulation-page">
    <div class="simulation-shell">

        <div class="simulation-header">
            <a href="/" class="back-btn">← Dashboard</a>
            <h1>Gulička na tyči</h1>
            <p>Animácia dynamického systému s grafom polohy guličky a uhla naklonenia tyče.</p>
        </div>

        <form id="ballBeamForm" class="simulation-controls">
            <div class="input-group">
                <label for="r">Cieľová pozícia r</label>
                <input type="number" id="r" name="r" step="0.01" value="0.25">
            </div>

            <div class="input-group">
                <label for="duration">Trvanie simulácie</label>
                <input type="number" id="duration" name="duration" step="0.1" value="5">
            </div>

            <div class="input-group">
                <label for="step">Krok simulácie</label>
                <input type="number" id="step" name="step" step="0.005" value="0.01">
            </div>

            <div class="input-group">
                <label for="initPosition">Počiatočná pozícia</label>
                <input type="number" id="initPosition" name="initPosition" step="0.01" value="0">
            </div>

            <div class="input-group">
                <label for="initVelocity">Počiatočná rýchlosť</label>
                <input type="number" id="initVelocity" name="initVelocity" step="0.01" value="0">
            </div>

            <div class="input-group">
                <label for="initAngle">Počiatočný uhol</label>
                <input type="number" id="initAngle" name="initAngle" step="0.01" value="0">
            </div>

            <div class="input-group">
                <label for="initAngularVelocity">Počiatočná uhlová rýchlosť</label>
                <input type="number" id="initAngularVelocity" name="initAngularVelocity" step="0.01" value="0">
            </div>

            <button type="submit" id="runBtn">Spustiť simuláciu</button>
        </form>

        <section class="simulation-layout">
            <div class="simulation-card">
                <h2>Animácia</h2>
                <canvas id="ballBeamCanvas" width="1000" height="500"></canvas>
            </div>

            <div class="simulation-card">
                <h2>Graf</h2>
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