const API_KEY = '8d7c1f0a9b3e6d4c2f5a8e1b7c9d0f12';
console.log('ball-beam.js loaded');
const form = document.getElementById('ballBeamForm');
const canvas = document.getElementById('ballBeamCanvas');
const ctx = canvas.getContext('2d');

const chartCanvas = document.getElementById('ballBeamChart');
let chart = null;

form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const formData = new FormData(form);

    const payload = {
        r: Number(formData.get('r')),
        duration: Number(formData.get('duration')),
        step: Number(formData.get('step')),
        initPosition: Number(formData.get('initPosition')),
        initVelocity: Number(formData.get('initVelocity')),
        initAngle: Number(formData.get('initAngle')),
        initAngularVelocity: Number(formData.get('initAngularVelocity'))
    };

    const response = await fetch('/api/animations/ball-beam', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-API-KEY': API_KEY
        },
        body: JSON.stringify(payload)
    });

    const json = await response.json();

    if (!json.success) {
        alert(json.error || 'Chyba pri výpočte.');
        return;
    }

    const data = json.data;

    drawChart(data);
    animateBallBeam(data);
});

function drawChart(data) {
    if (chart) {
        chart.destroy();
    }

    chart = new Chart(chartCanvas, {
        type: 'line',
        data: {
            labels: data.time,
            datasets: [
                {
                    label: 'Poloha guličky',
                    data: data.position,
                    borderWidth: 2,
                    pointRadius: 0
                },
                {
                    label: 'Uhol tyče',
                    data: data.angle,
                    borderWidth: 2,
                    pointRadius: 0
                }
            ]
        },
        options: {
            animation: false,
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Čas [s]'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Hodnota'
                    }
                }
            }
        }
    });
}

function animateBallBeam(data) {
    let index = 0;

    function frame() {
        if (index >= data.time.length) {
            return;
        }

        const position = data.position[index];
        const angle = data.angle[index];

        drawScene(position, angle);

        index++;
        requestAnimationFrame(frame);
    }

    frame();
}

function drawScene(position, angle) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;

    const beamLength = 500;

    ctx.save();

    ctx.translate(centerX, centerY);

    // масштабируем угол
    ctx.rotate(angle * 20);

    // балка
    ctx.strokeStyle = '#111';
    ctx.lineWidth = 14;
    ctx.lineCap = 'round';

    ctx.beginPath();
    ctx.moveTo(-beamLength / 2, 0);
    ctx.lineTo(beamLength / 2, 0);
    ctx.stroke();

    const normalizedPosition = position * 700 - 180;

    ctx.fillStyle = '#2563eb';

    ctx.beginPath();
    ctx.arc(normalizedPosition, -20, 24, 0, Math.PI * 2);
    ctx.fill();

    ctx.restore();

    ctx.fillStyle = '#444';

    ctx.beginPath();
    ctx.moveTo(centerX - 40, centerY + 70);
    ctx.lineTo(centerX + 40, centerY + 70);
    ctx.lineTo(centerX, centerY + 10);
    ctx.closePath();
    ctx.fill();
}