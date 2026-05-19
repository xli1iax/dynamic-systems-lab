<!DOCTYPE html>
<html lang="<?= htmlspecialchars(getLang()) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('statistics') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

            <h1><?= t('statistics') ?></h1>

            <p><?= t('statistics_page_description') ?></p>
        </div>

        <section class="statistics-charts">

            <div class="simulation-card chart-card">
                <h2><?= t('animation_usage_chart') ?></h2>
                <canvas id="usageChart"></canvas>
            </div>

            <div class="simulation-card chart-card">
                <h2><?= t('countries_chart') ?></h2>
                <canvas id="countryChart"></canvas>
            </div>

            <div class="simulation-card chart-card wide-chart">
                <h2><?= t('time_chart') ?></h2>
                <canvas id="timeChart"></canvas>
            </div>

        </section>

        <section class="simulation-card">

            <h2><?= t('usage_details') ?></h2>

            <div id="statisticsList" class="statistics-list">
                <div class="loading-text">
                    Loading statistics...
                </div>
            </div>

        </section>

    </div>
</main>

<script>
    window.API_KEY = <?= json_encode(getApiKey()) ?>;
</script>

<script>

    let usageChart = null;
    let countryChart = null;
    let timeChart = null;

    async function loadStatistics() {

        const container = document.getElementById('statisticsList');

        try {

            const response = await fetch('/api/statistics/animations', {
                headers: {
                    'X-API-KEY': window.API_KEY
                }
            });

            const json = await response.json();

            const animations = json.data ?? [];

            container.innerHTML = '';

            const usageMap = {
                'Ball & Beam': 0,
                'Inverted Pendulum': 0
            };

            const allDetails = [];

            for (const animation of animations) {

                const rawName =
                    animation.animation_name ??
                    animation.name ??
                    'unknown';

                const animationName =
                    rawName === 'ball_beam'
                        ? 'Ball & Beam'
                        : rawName === 'inverted_pendulum'
                            ? 'Inverted Pendulum'
                            : rawName;

                const count =
                    animation.total_count ??
                    animation.count ??
                    0;

                usageMap[animationName] = count;

                const detailResponse = await fetch(
                    '/api/statistics/animations/' + rawName,
                    {
                        headers: {
                            'X-API-KEY': window.API_KEY
                        }
                    }
                );

                const detailJson = await detailResponse.json();

                const details = detailJson.data ?? [];

                allDetails.push(...details);

                const card = document.createElement('div');

                card.className = 'statistics-card';

                card.innerHTML = `
                    <div class="statistics-top">

                        <div>
                            <h2>${animationName}</h2>
                            <p>${count} uses</p>
                        </div>

                        <button class="details-btn">
                            Details
                        </button>

                    </div>

                    <div class="statistics-details hidden">

                        ${
                    details.length === 0
                        ? `
                                    <div class="statistics-row">
                                        No details available
                                    </div>
                                `
                        : details.map(item => `

                                    <div class="statistics-row">

                                        <div class="stat-chip">
                                            📅 ${item.created_at ?? item.used_at ?? '-'}
                                        </div>

                                        <div class="stat-chip">
                                            🌍 ${item.city ?? 'Unknown city'}
                                        </div>

                                        <div class="stat-chip">
                                            🏳️ ${item.country ?? item.state ?? 'Unknown country'}
                                        </div>

                                    </div>

                                `).join('')
                }

                    </div>
                `;

                const detailsBtn =
                    card.querySelector('.details-btn');

                const detailsBox =
                    card.querySelector('.statistics-details');

                detailsBtn.addEventListener('click', () => {
                    detailsBox.classList.toggle('hidden');
                });

                container.appendChild(card);
            }

            drawUsageChart(
                Object.keys(usageMap),
                Object.values(usageMap)
            );

            drawCountryChart(allDetails);

            drawTimeChart(allDetails);

        } catch (error) {

            container.innerHTML = `
                <div class="error-box">
                    ${error.message}
                </div>
            `;
        }
    }

    function drawUsageChart(labels, counts) {

        const ctx =
            document.getElementById('usageChart');

        if (usageChart) {
            usageChart.destroy();
        }

        usageChart = new Chart(ctx, {

            type: 'bar',

            data: {
                labels: labels,

                datasets: [{
                    label: 'Animation usage',
                    data: counts,
                    borderWidth: 2,
                    borderRadius: 12
                }]
            },

            options: {
                responsive: true,

                plugins: {
                    legend: {
                        labels: {
                            color: '#dce7ff'
                        }
                    }
                },

                scales: {

                    x: {
                        ticks: {
                            color: '#dce7ff'
                        },

                        grid: {
                            color: 'rgba(255,255,255,0.08)'
                        }
                    },

                    y: {
                        beginAtZero: true,

                        ticks: {
                            color: '#dce7ff',
                            precision: 0
                        },

                        grid: {
                            color: 'rgba(255,255,255,0.08)'
                        }
                    }
                }
            }
        });
    }

    function drawCountryChart(details) {

        const countryCounts = {};

        details.forEach(item => {

            const country =
                item.country ??
                item.state ??
                'Unknown';

            countryCounts[country] =
                (countryCounts[country] ?? 0) + 1;
        });

        const labels = Object.keys(countryCounts);

        const counts = Object.values(countryCounts);

        const ctx =
            document.getElementById('countryChart');

        if (countryChart) {
            countryChart.destroy();
        }

        countryChart = new Chart(ctx, {

            type: 'doughnut',

            data: {
                labels: labels.length
                    ? labels
                    : ['No data'],

                datasets: [{
                    label: 'Countries',
                    data: counts.length
                        ? counts
                        : [1],

                    borderWidth: 2
                }]
            },

            options: {
                responsive: true,

                plugins: {
                    legend: {
                        labels: {
                            color: '#dce7ff'
                        }
                    }
                }
            }
        });
    }

    function drawTimeChart(details) {

        const timeCounts = {};

        details.forEach(item => {

            const rawDate =
                item.created_at ??
                item.used_at;

            if (!rawDate) {
                return;
            }

            const date =
                rawDate.split(' ')[0];

            timeCounts[date] =
                (timeCounts[date] ?? 0) + 1;
        });

        const labels =
            Object.keys(timeCounts).sort();

        const counts =
            labels.map(label => timeCounts[label]);

        const ctx =
            document.getElementById('timeChart');

        if (timeChart) {
            timeChart.destroy();
        }

        timeChart = new Chart(ctx, {

            type: 'line',

            data: {
                labels: labels.length
                    ? labels
                    : ['No data'],

                datasets: [{
                    label: 'Usage over time',
                    data: counts.length
                        ? counts
                        : [0],

                    borderWidth: 3,
                    tension: 0.35,
                    pointRadius: 5
                }]
            },

            options: {
                responsive: true,

                plugins: {
                    legend: {
                        labels: {
                            color: '#dce7ff'
                        }
                    }
                },

                scales: {

                    x: {
                        ticks: {
                            color: '#dce7ff'
                        },

                        grid: {
                            color: 'rgba(255,255,255,0.08)'
                        }
                    },

                    y: {
                        beginAtZero: true,

                        ticks: {
                            color: '#dce7ff',
                            precision: 0
                        },

                        grid: {
                            color: 'rgba(255,255,255,0.08)'
                        }
                    }
                }
            }
        });
    }

    loadStatistics();

</script>

</body>
</html>