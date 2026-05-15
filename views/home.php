<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Ball Beam Animation</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<main class="page">
    <section class="card">
        <h1>Gulička na tyči</h1>
        <p>Animácia dynamického systému s grafom polohy a uhla naklonenia tyče.</p>

        <form id="ballBeamForm" class="form">
            <label>
                Cieľová pozícia r
                <input type="number" step="0.01" name="r" value="0.25">
            </label>

            <label>
                Trvanie simulácie
                <input type="number" step="0.1" name="duration" value="5">
            </label>

            <label>
                Krok simulácie
                <input type="number" step="0.005" name="step" value="0.01">
            </label>

            <label>
                Počiatočná pozícia
                <input type="number" step="0.01" name="initPosition" value="0">
            </label>

            <label>
                Počiatočná rýchlosť
                <input type="number" step="0.01" name="initVelocity" value="0">
            </label>

            <label>
                Počiatočný uhol
                <input type="number" step="0.01" name="initAngle" value="0">
            </label>

            <label>
                Počiatočná uhlová rýchlosť
                <input type="number" step="0.01" name="initAngularVelocity" value="0">
            </label>

            <button type="submit">Spustiť animáciu</button>
        </form>
    </section>

    <section class="card">
        <h2>Animácia</h2>
        <canvas id="ballBeamCanvas" width="1000" height="500"></canvas>
    </section>

    <section class="card">
        <h2>Graf</h2>
        <canvas id="ballBeamChart"></canvas>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/js/ball-beam.js"></script>
</body>
</html>