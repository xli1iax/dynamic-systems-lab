<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Dynamic System Simulator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<main class="home-page">
    <section class="hero">
        <div class="hero-badge">WEBTE2 • LS 2025/2026</div>

        <h1>Dynamic Systems Laboratory</h1>

        <p>
            Web application for CAS calculations, dynamic system simulations,
            synchronized graphs, request logging and API documentation.
        </p>
    </section>

    <section class="dashboard-section">
        <h2>Výpočty</h2>

        <div class="dashboard-grid single">
            <a href="/cas" class="dashboard-card">
                <div class="card-icon">⌘</div>
                <div>
                    <h3>CAS formulár</h3>
                    <p>Execute Octave/CAS commands manually using a web form.</p>
                    <span>Open CAS →</span>
                </div>
            </a>
        </div>
    </section>

    <section class="dashboard-section">
        <h2>Simulácie a grafy</h2>

        <div class="dashboard-grid">
            <a href="/animations/pendulum" class="dashboard-card">
                <div class="card-icon">⟲</div>
                <div>
                    <h3>Inverzné kyvadlo</h3>
                    <p>Interactive inverted pendulum animation with synchronized graph.</p>
                    <span>Open simulation →</span>
                </div>
            </a>

            <a href="/animations/ball-beam" class="dashboard-card">
                <div class="card-icon">●</div>
                <div>
                    <h3>Gulička na tyči</h3>
                    <p>Ball and beam dynamic system simulation with real-time data.</p>
                    <span>Open simulation →</span>
                </div>
            </a>
        </div>
    </section>

    <section class="dashboard-section">
        <h2>Dáta a administrácia</h2>

        <div class="dashboard-grid">
            <a href="/statistics" class="dashboard-card">
                <div class="card-icon">◔</div>
                <div>
                    <h3>Štatistika animácií</h3>
                    <p>View usage count and detailed anonymous activity records.</p>
                    <span>View statistics →</span>
                </div>
            </a>

            <a href="/logs" class="dashboard-card">
                <div class="card-icon">☰</div>
                <div>
                    <h3>Logy a CSV export</h3>
                    <p>Browse CAS requests, errors and export log data to CSV.</p>
                    <span>View logs →</span>
                </div>
            </a>
        </div>
    </section>

    <section class="dashboard-section">
        <h2>Dokumentácia</h2>

        <div class="dashboard-grid">
            <a href="/docs" class="dashboard-card">
                <div class="card-icon">{ }</div>
                <div>
                    <h3>OpenAPI dokumentácia</h3>
                    <p>Interactive API documentation for all backend endpoints.</p>
                    <span>Open docs →</span>
                </div>
            </a>

            <a href="/documentation" class="dashboard-card">
                <div class="card-icon">PDF</div>
                <div>
                    <h3>PDF dokumentácia</h3>
                    <p>Dynamically generated documentation with page numbering.</p>
                    <span>Open PDF →</span>
                </div>
            </a>
        </div>
    </section>
</main>

</body>
</html>