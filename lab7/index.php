<?php

require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/handler.php';

// Handle POST (add / delete)
handleRequest();

$mode    = $_GET['mode'] ?? 'native';
$recipes = getRecipes();

// ────────────────────────────────────────────────
// MODE 1: Нативные PHP-шаблоны
// ────────────────────────────────────────────────
if ($mode === 'native') {

    // Рендеринг через output buffering
    function renderTemplate(string $path, array $vars = []): string {
        extract($vars);
        ob_start();
        require $path;
        return ob_get_clean();
    }

    $tplDir = __DIR__ . '/templates/native/';

    $content = renderTemplate($tplDir . 'form.php')
             . renderTemplate($tplDir . 'list.php', ['recipes' => $recipes]);

    echo renderTemplate($tplDir . 'layout.php', [
        'title'   => 'Рецепты — Нативный PHP',
        'content' => $content,
    ]);

    exit;
}

// ────────────────────────────────────────────────
// MODE 2: Twig
// ────────────────────────────────────────────────
require_once __DIR__ . '/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates/twig');
$twig   = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true,
]);

// ── Кастомный фильтр: format_duration ──────────
// {{ prep_time | format_duration }}  →  "1 ч 30 мин"
$twig->addFilter(new \Twig\TwigFilter('format_duration', function (int $minutes): string {
    return formatDuration($minutes);
}));

echo $twig->render('index.html.twig', [
    'recipes' => $recipes,
]);
