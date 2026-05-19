<?php

function getLang(): string
{
    $lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'sk';

    if (!in_array($lang, ['sk', 'en'], true)) {
        $lang = 'sk';
    }

    $_SESSION['lang'] = $lang;
    return $lang;
}

function t(string $key): string
{
    $lang = getLang();

    $translations = [
        'sk' => [
            'home_title' => 'Dynamic Systems Laboratory',
            'home_description' => 'Webová aplikácia pre CAS výpočty, simulácie dynamických systémov, synchronizované grafy, logovanie požiadaviek a API dokumentáciu.',
            'calculations' => 'Výpočty',
            'cas_form' => 'CAS formulár',
            'cas_description' => 'Spúšťanie Octave/CAS príkazov pomocou webového formulára.',
            'open_cas' => 'Otvoriť CAS →',
            'simulations' => 'Simulácie a grafy',
            'pendulum' => 'Inverzné kyvadlo',
            'ball_beam' => 'Gulička na tyči',
            'statistics' => 'Štatistika animácií',
            'logs' => 'Logy a CSV export',
            'documentation' => 'Dokumentácia',
            'back_dashboard' => '← Dashboard',
            'ball_beam_page_description' => 'Animácia dynamického systému s grafom polohy guličky a uhla naklonenia tyče.',
            'target_position_r' => 'Cieľová pozícia r',
            'simulation_duration' => 'Trvanie simulácie',
            'simulation_step' => 'Krok simulácie',
            'initial_position' => 'Počiatočná pozícia',
            'initial_velocity' => 'Počiatočná rýchlosť',
            'initial_angle' => 'Počiatočný uhol',
            'initial_angular_velocity' => 'Počiatočná uhlová rýchlosť',
            'run_simulation' => 'Spustiť simuláciu',
            'animation' => 'Animácia',
            'graph' => 'Graf',
            'api_documentation' => 'API dokumentácia',
            'cas_page_description' => 'Formulár na odosielanie príkazov do CAS s podporou zvýraznenia syntaxe.',
            'cas_command' => 'Príkaz pre CAS',
            'send_request' => 'Odoslať požiadavku',
            'output' => 'Výstup',
            'export_csv' => 'Exportovať do CSV',
            'logs_page_description' => 'Export CAS požiadaviek a logov do CSV súboru.',
            'statistics_page_description' => 'Štatistiky používania animácií a detaily ich využitia.',
            'animation_usage_chart' => 'Využitie animácií',
            'countries_chart' => 'Použitie podľa krajín',
            'usage_details' => 'Detaily použitia',
            'time_chart' => 'Použitie podľa času',
        ],
        'en' => [
            'home_title' => 'Dynamic Systems Laboratory',
            'home_description' => 'Web application for CAS calculations, dynamic system simulations, synchronized graphs, request logging and API documentation.',
            'calculations' => 'Calculations',
            'cas_form' => 'CAS form',
            'cas_description' => 'Execute Octave/CAS commands manually using a web form.',
            'open_cas' => 'Open CAS →',
            'simulations' => 'Simulations and graphs',
            'pendulum' => 'Inverted pendulum',
            'ball_beam' => 'Ball and beam',
            'statistics' => 'Animation statistics',
            'logs' => 'Logs and CSV export',
            'documentation' => 'Documentation',
            'back_dashboard' => '← Dashboard',
            'ball_beam_page_description' => 'Dynamic system animation with a graph of ball position and beam angle.',
            'target_position_r' => 'Target position r',
            'simulation_duration' => 'Simulation duration',
            'simulation_step' => 'Simulation step',
            'initial_position' => 'Initial position',
            'initial_velocity' => 'Initial velocity',
            'initial_angle' => 'Initial angle',
            'initial_angular_velocity' => 'Initial angular velocity',
            'run_simulation' => 'Run simulation',
            'animation' => 'Animation',
            'graph' => 'Graph',
            'api_documentation' => 'API documentation',
            'cas_page_description' => 'Form for sending commands to CAS with syntax highlighting support.',
            'cas_command' => 'CAS command',
            'send_request' => 'Send request',
            'output' => 'Output',
            'export_csv' => 'Export to CSV',
            'logs_page_description' => 'Export CAS requests and logs into a CSV file.',
            'statistics_page_description' => 'Animation usage statistics and detailed usage information.',
            'animation_usage_chart' => 'Animation usage',
            'countries_chart' => 'Usage by countries',
            'usage_details' => 'Usage details',
            'time_chart' => 'Usage over time',
        ],
    ];

    return $translations[$lang][$key] ?? $key;
}

function langUrl(string $lang): string
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return $path . '?lang=' . $lang;
}