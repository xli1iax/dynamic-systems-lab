const API_KEY = window.API_KEY;

console.log('ball-beam.js loaded');

const form = document.getElementById('ballBeamForm');
const canvas = document.getElementById('ballBeamCanvas');
const chartCanvas = document.getElementById('ballBeamChart');

let chart = null;
let animationTimer = null;

let scene;
let camera;
let renderer;
let beam;
let ball;
let support;
let floorGrid;

initBallBeam3D();

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
    animateBallBeam3D(data);
});

function initBallBeam3D() {
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x162334);

    const width = canvas.clientWidth || canvas.width;
    const height = canvas.clientHeight || canvas.height;

    camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
    camera.position.set(0, 4.5, 8);
    camera.lookAt(0, 0, 0);

    renderer = new THREE.WebGLRenderer({
        canvas: canvas,
        antialias: true,
        alpha: false
    });

    renderer.setSize(width, height, false);
    renderer.setPixelRatio(window.devicePixelRatio || 1);

    const ambientLight = new THREE.AmbientLight(0xffffff, 0.65);
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 1.1);
    directionalLight.position.set(4, 7, 5);
    scene.add(directionalLight);

    const backLight = new THREE.DirectionalLight(0x8bbcff, 0.6);
    backLight.position.set(-4, 3, -4);
    scene.add(backLight);

    beam = new THREE.Mesh(
        new THREE.BoxGeometry(6.5, 0.16, 0.34),
        new THREE.MeshStandardMaterial({
            color: 0xe8f1ff,
            roughness: 0.35,
            metalness: 0.08
        })
    );
    beam.position.y = 0;
    scene.add(beam);

    ball = new THREE.Mesh(
        new THREE.SphereGeometry(0.28, 48, 48),
        new THREE.MeshStandardMaterial({
            color: 0x5ea8ff,
            roughness: 0.25,
            metalness: 0.2
        })
    );
    scene.add(ball);

    support = new THREE.Mesh(
        new THREE.ConeGeometry(0.55, 1.2, 4),
        new THREE.MeshStandardMaterial({
            color: 0xd9dde4,
            roughness: 0.45
        })
    );
    support.position.y = -0.7;
    support.rotation.y = Math.PI / 4;
    scene.add(support);

    const floorGeometry = new THREE.PlaneGeometry(8, 3);
    const floorMaterial = new THREE.MeshStandardMaterial({
        color: 0x0f1b2a,
        roughness: 0.8,
        metalness: 0.05
    });

    const floor = new THREE.Mesh(floorGeometry, floorMaterial);
    floor.rotation.x = -Math.PI / 2;
    floor.position.y = -1.35;
    scene.add(floor);

    floorGrid = new THREE.GridHelper(8, 16, 0x5b6f86, 0x2f4055);
    floorGrid.position.y = -1.33;
    scene.add(floorGrid);

    render3D();

    window.addEventListener('resize', resize3D);
}

function resize3D() {
    const width = canvas.clientWidth || canvas.width;
    const height = canvas.clientHeight || canvas.height;

    camera.aspect = width / height;
    camera.updateProjectionMatrix();

    renderer.setSize(width, height, false);
    render3D();
}

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

function animateBallBeam3D(data) {
    if (animationTimer) {
        clearTimeout(animationTimer);
    }

    let index = 0;
    const stepMs = Math.max((data.time[1] - data.time[0]) * 1000, 16);

    function frame() {
        if (index >= data.time.length) {
            return;
        }

        const position = data.position[index];
        const angle = data.angle[index];

        drawScene3D(position, angle, data.target);

        index++;
        animationTimer = setTimeout(frame, stepMs);
    }

    frame();
}

function drawScene3D(position, angle, target = 0.25) {
    const visualScale = 12000;
    const maxVisualAngle = 0.38;

    let visualAngle = angle * visualScale;
    visualAngle = Math.max(-maxVisualAngle, Math.min(maxVisualAngle, visualAngle));

    beam.rotation.z = visualAngle;

    const safeTarget = target || 0.25;
    let ballX = (position / safeTarget) * 2.2;
    ballX = Math.max(-2.9, Math.min(2.9, ballX));

    const ballY = ballX * Math.sin(visualAngle) + 0.38;
    const rotatedX = ballX * Math.cos(visualAngle);

    ball.position.set(rotatedX, ballY, 0);
    ball.rotation.z -= 0.08;

    render3D();
}

function render3D() {
    renderer.render(scene, camera);
}