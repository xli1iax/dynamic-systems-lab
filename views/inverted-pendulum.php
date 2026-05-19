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

    let finalState = [0, 0, 0, 0];
    let animationId = null;

    const canvas = document.getElementById('pendulumCanvas');
    const ctx = canvas.getContext('2d');
    const chartCtx = document.getElementById('chartCanvas');

    const runBtn = document.getElementById('runBtn');
    const resetBtn = document.getElementById('resetBtn');

    resetBtn.addEventListener('click', () => {
        finalState = [0, 0, 0, 0];

        if (animationId) {
            cancelAnimationFrame(animationId);
        }

        drawPendulum(
            0,
            0,
            Number(document.getElementById('r').value),
            0
        );
    });

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

    function getScale(position, target, initPosition = 0) {
        const maxValue = Math.max(
            Math.abs(position),
            Math.abs(target),
            Math.abs(initPosition),
            0.35
        );

        return (canvas.width - 160) / (2 * maxValue * 1.25);
    }

    function drawPendulum(position, angle, target, initPosition = 0) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const centerX = canvas.width / 2;
        const pivotY = 150;
        const rodLength = 175;
        const scale = getScale(position, target, initPosition);

        const pivotX = centerX + position * scale;
        const targetX = centerX + target * scale;
        const initX = centerX + initPosition * scale;

        const bg = ctx.createRadialGradient(centerX, pivotY, 40, centerX, pivotY, 430);
        bg.addColorStop(0, 'rgba(94,168,255,0.14)');
        bg.addColorStop(1, 'rgba(0,0,0,0)');
        ctx.fillStyle = bg;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.strokeStyle = 'rgba(255,255,255,0.08)';
        ctx.lineWidth = 1;

        for (let x = 60; x < canvas.width; x += 45) {
            ctx.beginPath();
            ctx.moveTo(x, 70);
            ctx.lineTo(x, canvas.height - 40);
            ctx.stroke();
        }

        ctx.strokeStyle = 'rgba(255,255,255,0.20)';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(50, pivotY);
        ctx.lineTo(canvas.width - 50, pivotY);
        ctx.stroke();

        ctx.strokeStyle = 'rgba(255,255,255,0.25)';
        ctx.setLineDash([5, 7]);
        ctx.beginPath();
        ctx.moveTo(initX, 55);
        ctx.lineTo(initX, canvas.height - 45);
        ctx.stroke();
        ctx.setLineDash([]);

        ctx.fillStyle = 'rgba(255,255,255,0.65)';
        ctx.font = '12px Arial';
        ctx.fillText('start', initX + 7, 72);

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

        const visualAngle = Math.max(-0.9, Math.min(0.9, Number(angle) * 25));

        const endX = pivotX + rodLength * Math.sin(visualAngle);
        const endY = pivotY + rodLength * Math.cos(visualAngle);

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
        ctx.fillText(`auto zoom: ON`, 24, 102);
    }

    async function runSimulation() {
        if (animationId) cancelAnimationFrame(animationId);

        const initPosition = Number(finalState[0]);

        const payload = {
            r: parseFloat(document.getElementById('r').value),
            duration: parseFloat(document.getElementById('duration').value),
            step: parseFloat(document.getElementById('step').value),
            initPosition: Number(finalState[0]),
            initVelocity: Number(finalState[1]),
            initAngle: Number(finalState[2]),
            initAngularVelocity: Number(finalState[3])
        };

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
            alert(result.error || 'Simulation failed');
            return;
        }

        const data = result.data;

        const times = data.time.map(Number);
        const positions = data.position.map(Number);
        const angles = data.angle.map(Number);
        const target = Number(data.target);

        finalState = data.finalState.map(Number);

        chart.data.labels = times.map(t => t.toFixed(2));
        chart.data.datasets[0].data = positions;
        chart.data.datasets[1].data = angles;
        chart.update();

        const startTime = performance.now();
        const durationMs = payload.duration * 1000;

        function animate(now) {
            const progress = Math.min((now - startTime) / durationMs, 1);

            const index = Math.min(
                positions.length - 1,
                Math.floor(progress * (positions.length - 1))
            );

            drawPendulum(positions[index], angles[index], target, initPosition);

            if (progress < 1) {
                animationId = requestAnimationFrame(animate);
            }
        }

        animationId = requestAnimationFrame(animate);
    }

    runBtn.addEventListener('click', runSimulation);

    drawPendulum(0, 0, 0.2, 0);
</script>

</body>
</html>