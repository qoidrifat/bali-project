<?php

require_once __DIR__ . '/../includes/database.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "This tool is CLI-only.\n";
    exit(1);
}

$config = db_config();
$backupDir = __DIR__ . '/../storage/private/backups';

if (!is_dir($backupDir) && !mkdir($backupDir, 0775, true) && !is_dir($backupDir)) {
    fwrite(STDERR, "Cannot create backup directory.\n");
    exit(1);
}

$filename = $backupDir . '/bali-' . date('Ymd-His') . '.sql';
$mysqldump = getenv('BALI_MYSQLDUMP') ?: 'mysqldump';

if (PHP_OS_FAMILY === 'Windows' && $mysqldump === 'mysqldump') {
    $candidates = glob('C:/laragon/bin/mysql/*/bin/mysqldump.exe') ?: [];
    if ($candidates) {
        rsort($candidates);
        $mysqldump = $candidates[0];
    }
}

$command = [
    $mysqldump,
    '--host=' . $config['host'],
    '--user=' . $config['user'],
    '--single-transaction',
    '--quick',
    '--default-character-set=utf8mb4',
    $config['database'],
];

if ($config['password'] !== '') {
    putenv('MYSQL_PWD=' . $config['password']);
}

$descriptors = [
    1 => ['file', $filename, 'w'],
    2 => ['pipe', 'w'],
];

$process = proc_open($command, $descriptors, $pipes, __DIR__ . '/..');

if (!is_resource($process)) {
    fwrite(STDERR, "Database backup failed. mysqldump could not be started.\n");
    exit(1);
}

$stderr = stream_get_contents($pipes[2]);
fclose($pipes[2]);
$exitCode = proc_close($process);

if ($config['password'] !== '') {
    putenv('MYSQL_PWD');
}

if ($exitCode !== 0 || !is_file($filename) || filesize($filename) === 0) {
    if (is_file($filename)) {
        unlink($filename);
    }

    fwrite(STDERR, "Database backup failed. Make sure mysqldump is available.\n");
    if (trim((string) $stderr) !== '') {
        fwrite(STDERR, trim($stderr) . "\n");
    }
    exit(1);
}

echo "Backup created: " . str_replace('\\', '/', $filename) . PHP_EOL;
