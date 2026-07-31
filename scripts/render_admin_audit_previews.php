<?php
/**
 * Render authenticated admin pages to static HTML previews for visual QA.
 * This is read-only: it uses a simulated admin session and only writes files
 * under the requested output directory.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "CLI only.\n";
    exit(1);
}

$root = realpath(__DIR__ . '/..');
$outputDir = $argv[1] ?? '';

if (!$root || $outputDir === '') {
    fwrite(STDERR, "Usage: php scripts/render_admin_audit_previews.php <output-dir>\n");
    exit(1);
}

$outputDir = rtrim(str_replace('\\', '/', $outputDir), '/');

if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true)) {
    fwrite(STDERR, "Unable to create output directory: {$outputDir}\n");
    exit(1);
}

chdir($root);
session_id('codex-admin-visual-audit');
session_start();
$_SESSION['user'] = [
    'id' => 1,
    'name' => 'Codex Admin',
    'email' => 'admin@example.test',
    'role' => 'admin',
];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/bali-project/admin/index.php';

$pages = [
    'dashboard' => 'admin/index.php',
    'reports' => 'admin/reports/index.php',
    'users' => 'admin/users/index.php',
    'roles' => 'admin/roles/index.php',
    'destinations' => 'admin/destinations/index.php',
    'categories' => 'admin/categories/index.php',
    'gallery' => 'admin/gallery/index.php',
    'articles' => 'admin/articles/index.php',
    'tickets' => 'admin/tickets/index.php',
    'bookings' => 'admin/bookings/index.php',
    'invoices' => 'admin/invoices/index.php',
    'payments' => 'admin/payments/index.php',
    'messages' => 'admin/messages/index.php',
    'settings' => 'admin/settings/index.php',
    'activity' => 'admin/activity/index.php',
];

function render_admin_audit_page(string $root, string $pagePath): string
{
    $_GET = [];
    $_POST = [];
    $_REQUEST = [];
    $_SERVER['REQUEST_METHOD'] = 'GET';

    ob_start();
    include $root . '/' . $pagePath;
    $html = ob_get_clean();

    $html = str_replace(
        ['<base href="../" />', '<base href="../../" />'],
        '<base href="http://localhost/bali-project/" />',
        $html
    );

    return $html;
}

$manifest = [];

foreach ($pages as $slug => $pagePath) {
    $target = "{$outputDir}/{$slug}.html";
    $html = render_admin_audit_page($root, $pagePath);
    file_put_contents($target, $html);
    $manifest[] = [
        'slug' => $slug,
        'page' => $pagePath,
        'html' => $target,
    ];
}

file_put_contents(
    "{$outputDir}/manifest.json",
    json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

echo "Rendered " . count($manifest) . " admin previews to {$outputDir}\n";
