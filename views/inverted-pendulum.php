<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Inverzné kyvadlo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<main class="simulation-page">
    <div class="simulation-shell">

        <div class="simulation-header">
            <a href="/" class="back-btn">← Dashboard</a>
            <h1>Inverzné kyvadlo</h1>
            <p>Simulácia dynamického systému s animáciou a synchronizovaným grafom.</p>
        </div>

        <section class="simulation-controls">
            <div class="input-group">
                <label for="r">Target position (r)</label>
                <input type="number" id="r" value="0.2" step="0.1">
            </div>

            <div class="input-group">
                <label for="duration">Duration (s)</label>
                <input type="number" id="duration" value="10" step="1">
            </div>

            <div class="input-group">
                <label for="step">Step</label>
                <input type="number" id="step" value="0.05" step="0.01">
            </div>

            <button id="runBtn">Spustiť simuláciu</button>
            <button id="resetBtn">Reset state</button>
        </section>

        <section class="simulation-layout">
            <div class="simulation-card">
                <h2>Animácia</h2>
                <canvas id="pendulumCanvas" width="700" height="400"></canvas>
            </div>

            <div class="simulation-card">
                <h2>Graf</h2>
                <canvas id="chartCanvas"></canvas>
            </div>
        </section>

    </div>
</main>

<script>
    const API_KEY = <?= json_encode(getApiKey()) ?>;

    let animationId = null;
    let isRunning = false;

    const canvas = document.getElementById('pendulumCanvas');
    const ctx = canvas.getContext('2d');
    const chartCtx = document.getElementById('chartCanvas');

    const runBtn = document.getElementById('runBtn');
    const resetBtn = document.getElementById('resetBtn');

    const chart = new Chart(chartCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Position',
                    data: [],
                    borderColor: '#5ea8ff',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.2
                },
                {
                    label: 'Angle',
                    data: [],
                    borderColor: '#d38bff',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.2
                }
            ]
        },
        options: {
            responsive: true,
            animation: false,
            scales: {
                x: {
                    ticks: { color: 'rgba(245,248,255,0.6)', maxTicksLimit: 8 },
                    grid: { color: 'rgba(255,255,255,0.05)' }
                },
                y: {
                    min: -0.1,
                    max: Math.max(1, Number(document.getElementById('r').value) * 1.2),
                    ticks: { color: 'rgba(245,248,255,0.6)' },
                    grid: { color: 'rgba(255,255,255,0.05)' }
                }
            },
            plugins: {
                legend: {
                    labels: { color: 'rgba(245,248,255,0.8)' }
                }
            }
        }
    });

    function stopAnimation() {
        if (animationId !== null) {
            cancelAnimationFrame(animationId);
            animationId = null;
        }

        isRunning = false;
        runBtn.disabled = false;
    }

    function safeNumber(value, fallback = 0) {
        const number = Number(value);
        return Number.isFinite(number) ? number : fallback;
    }

    function calculateScale(positions, target, initPosition) {
        const values = [
            Math.abs(target),
            Math.abs(initPosition),
            ...positions.map(value => Math.abs(value))
        ];

        const maxValue = Math.max(...values, 0.35);
        return (canvas.width - 170) / (2 * maxValue * 1.2);
    }

    function roundedRect(ctx, x, y, width, height, radius) {
        const r = Math.min(radius, width / 2, height / 2);

        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.lineTo(x + width - r, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + r);
        ctx.lineTo(x + width, y + height - r);
        ctx.quadraticCurveTo(x + width, y + height, x + width - r, y + height);
        ctx.lineTo(x + r, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - r);
        ctx.lineTo(x, y + r);
        ctx.quadraticCurveTo(x, y, x + r, y);
        ctx.closePath();
    }

    function drawPendulum(position, angle, target, initPosition = 0, fixedScale = null) {
        position = safeNumber(position);
        angle = safeNumber(angle);
        target = safeNumber(target);
        initPosition = safeNumber(initPosition);

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const centerX = canvas.width / 2;
        const trackY = 250;
        const rodLength = 150;
        const scale = fixedScale ?? calculateScale([position], target, initPosition);

        const cartCenterX = centerX + position * scale;
        const targetX = centerX + target * scale;
        const startX = centerX + initPosition * scale;

        const pivotX = cartCenterX;
        const pivotY = trackY - 48;

        const bg = ctx.createRadialGradient(centerX, 160, 50, centerX, 160, 430);
        bg.addColorStop(0, 'rgba(94,168,255,0.14)');
        bg.addColorStop(1, 'rgba(0,0,0,0)');
        ctx.fillStyle = bg;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.strokeStyle = 'rgba(255,255,255,0.08)';
        ctx.lineWidth = 1;

        for (let x = 60; x < canvas.width; x += 45) {
            ctx.beginPath();
            ctx.moveTo(x, 60);
            ctx.lineTo(x, canvas.height - 40);
            ctx.stroke();
        }

        ctx.strokeStyle = 'rgba(255,255,255,0.25)';
        ctx.lineWidth = 3;
        ctx.beginPath();
        ctx.moveTo(45, trackY);
        ctx.lineTo(canvas.width - 45, trackY);
        ctx.stroke();

        ctx.strokeStyle = 'rgba(255,255,255,0.25)';
        ctx.setLineDash([5, 7]);
        ctx.beginPath();
        ctx.moveTo(startX, 55);
        ctx.lineTo(startX, canvas.height - 45);
        ctx.stroke();
        ctx.setLineDash([]);

        ctx.fillStyle = 'rgba(255,255,255,0.65)';
        ctx.font = '12px Arial';
        ctx.fillText('start', startX + 7, 72);

        ctx.strokeStyle = 'rgba(94,168,255,0.75)';
        ctx.setLineDash([8, 8]);
        ctx.beginPath();
        ctx.moveTo(targetX, 45);
        ctx.lineTo(targetX, canvas.height - 45);
        ctx.stroke();
        ctx.setLineDash([]);

        ctx.fillStyle = '#5ea8ff';
        ctx.font = 'bold 13px Arial';
        ctx.fillText('target', targetX + 8, 62);

        const cartWidth = 100;
        const cartHeight = 44;
        const cartX = cartCenterX - cartWidth / 2;
        const cartY = trackY - cartHeight;

        ctx.fillStyle = 'rgba(0,0,0,0.28)';
        ctx.beginPath();
        ctx.ellipse(cartCenterX, trackY + 22, 58, 12, 0, 0, Math.PI * 2);
        ctx.fill();

        const cartGradient = ctx.createLinearGradient(cartX, cartY, cartX, cartY + cartHeight);
        cartGradient.addColorStop(0, '#78bdff');
        cartGradient.addColorStop(1, '#2f6fe0');

        ctx.fillStyle = cartGradient;
        roundedRect(ctx, cartX, cartY, cartWidth, cartHeight, 13);
        ctx.fill();

        ctx.strokeStyle = 'rgba(255,255,255,0.35)';
        ctx.lineWidth = 2;
        ctx.stroke();

        ctx.fillStyle = '#e7f0ff';
        ctx.beginPath();
        ctx.arc(cartX + 24, trackY + 7, 8, 0, Math.PI * 2);
        ctx.arc(cartX + cartWidth - 24, trackY + 7, 8, 0, Math.PI * 2);
        ctx.fill();

        ctx.fillStyle = '#1e293b';
        ctx.beginPath();
        ctx.arc(cartX + 24, trackY + 7, 3, 0, Math.PI * 2);
        ctx.arc(cartX + cartWidth - 24, trackY + 7, 3, 0, Math.PI * 2);
        ctx.fill();

        const visualAngle = Math.max(-0.9, Math.min(0.9, angle * 25));

        const endX = pivotX + rodLength * Math.sin(visualAngle);
        const endY = pivotY - rodLength * Math.cos(visualAngle);

        ctx.strokeStyle = 'rgba(211,139,255,0.17)';
        ctx.lineWidth = 16;
        ctx.beginPath();
        ctx.moveTo(pivotX, pivotY);
        ctx.lineTo(endX, endY);
        ctx.stroke();

        ctx.strokeStyle = '#d38bff';
        ctx.lineWidth = 6;
        ctx.beginPath();
        ctx.moveTo(pivotX, pivotY);
        ctx.lineTo(endX, endY);
        ctx.stroke();

        const pivotGradient = ctx.createRadialGradient(pivotX - 5, pivotY - 5, 4, pivotX, pivotY, 18);
        pivotGradient.addColorStop(0, '#ffffff');
        pivotGradient.addColorStop(0.45, '#75b7ff');
        pivotGradient.addColorStop(1, '#2d6ee0');

        ctx.fillStyle = pivotGradient;
        ctx.beginPath();
        ctx.arc(pivotX, pivotY, 15, 0, Math.PI * 2);
        ctx.fill();

        const ballGradient = ctx.createRadialGradient(endX - 8, endY - 9, 5, endX, endY, 27);
        ballGradient.addColorStop(0, '#ffffff');
        ballGradient.addColorStop(0.45, '#dbeafe');
        ballGradient.addColorStop(1, '#8b9dff');

        ctx.fillStyle = ballGradient;
        ctx.beginPath();
        ctx.arc(endX, endY, 23, 0, Math.PI * 2);
        ctx.fill();

        ctx.strokeStyle = 'rgba(255,255,255,0.75)';
        ctx.lineWidth = 2;
        ctx.stroke();

        ctx.fillStyle = 'rgba(245,248,255,0.86)';
        ctx.font = '14px Arial';
        ctx.fillText(`position: ${position.toFixed(3)} m`, 24, 30);
        ctx.fillText(`angle: ${angle.toFixed(4)} rad`, 24, 54);
        ctx.fillText(`target: ${target.toFixed(2)} m`, 24, 78);
        ctx.fillText(`each run starts from zero`, 24, 102);
    }

    async function runSimulation() {
        if (isRunning) {
            return;
        }

        stopAnimation();

        const r = safeNumber(document.getElementById('r').value, 0.2);
        const duration = safeNumber(document.getElementById('duration').value, 10);
        const step = safeNumber(document.getElementById('step').value, 0.05);

        if (duration <= 0 || step <= 0) {
            alert('Duration and step must be positive numbers.');
            return;
        }

        const initPosition = 0;

        const payload = {
            r: r,
            duration: duration,
            step: step,
            initPosition: 0,
            initVelocity: 0,
            initAngle: 0,
            initAngularVelocity: 0
        };

        isRunning = true;
        runBtn.disabled = true;

        try {
            const response = await fetch('/api/animations/pendulum', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-API-KEY': API_KEY
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.error || 'Simulation failed');
            }

            const data = result.data;

            const times = data.time.map(Number);
            const positions = data.position.map(Number);
            const angles = data.angle.map(Number);
            const target = Number(data.target);

            chart.data.labels = times.map(t => t.toFixed(2));
            chart.data.datasets[0].data = positions;
            chart.data.datasets[1].data = angles;
            chart.options.scales.y.min = Math.min(-0.1, target * 0.1);
            chart.options.scales.y.max = Math.max(0.3, target * 1.25);
            chart.update();

            const fixedScale = calculateScale(positions, target, initPosition);
            const startTime = performance.now();
            const durationMs = duration * 1000;

            function animate(now) {
                const progress = Math.min((now - startTime) / durationMs, 1);

                const index = Math.min(
                    positions.length - 1,
                    Math.floor(progress * (positions.length - 1))
                );

                drawPendulum(
                    positions[index],
                    angles[index],
                    target,
                    initPosition,
                    fixedScale
                );

                if (progress < 1) {
                    animationId = requestAnimationFrame(animate);
                } else {
                    stopAnimation();
                    drawPendulum(
                        positions[positions.length - 1],
                        angles[angles.length - 1],
                        target,
                        initPosition,
                        fixedScale
                    );
                }
            }

            animationId = requestAnimationFrame(animate);

        } catch (error) {
            stopAnimation();
            alert(error.message);
        }
    }

    function resetSimulation() {
        stopAnimation();

        chart.data.labels = [];
        chart.data.datasets[0].data = [];
        chart.data.datasets[1].data = [];
        chart.update();

        drawPendulum(0, 0, safeNumber(document.getElementById('r').value, 0.2), 0);
    }

    runBtn.addEventListener('click', runSimulation);
    resetBtn.addEventListener('click', resetSimulation);

    drawPendulum(0, 0, 0.2, 0);
</script>

</body>
</html>
