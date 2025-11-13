<?php
// Copy this file to db.php and fill in your local MySQL details.
// This file is intentionally tracked; db.php is ignored by git.

$DB_HOST = '127.0.0.1';        // e.g. 127.0.0.1
$DB_PORT = 3306;                // e.g. 3306
$DB_NAME = 'eportfolio_chrisystematixx'; // your database name
$DB_USER = 'root';              // your MySQL username
$DB_PASS = '';                  // your MySQL password

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $dsnServer = "mysql:host={$DB_HOST};port={$DB_PORT};charset=utf8mb4";
    $pdoServer = new PDO($dsnServer, $DB_USER, $DB_PASS, $options);
    $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$DB_NAME}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);

    $exists = $pdo->query("SHOW TABLES LIKE 'profile'")->fetch();
    if (!$exists) {
        $schemaPath = __DIR__ . '/../sql/schema.sql';
        if (is_file($schemaPath)) {
            $sql = file_get_contents($schemaPath);
            $statements = [];
            $buffer = '';
            $len = strlen($sql);
            $inString = false;
            $stringChar = '';
            for ($i = 0; $i < $len; $i++) {
                $ch = $sql[$i];
                $next = $i + 1 < $len ? $sql[$i + 1] : '';
                if (!$inString && $ch === '-' && $next === '-') {
                    while ($i < $len && $sql[$i] !== "\n") { $i++; }
                    continue;
                }
                if (!$inString && $ch === '#') {
                    while ($i < $len && $sql[$i] !== "\n") { $i++; }
                    continue;
                }
                if (!$inString && $ch === '/' && $next === '*') {
                    $i += 2;
                    while ($i < $len && !($sql[$i] === '*' && ($i + 1 < $len && $sql[$i + 1] === '/'))) { $i++; }
                    $i++;
                    continue;
                }
                if ($ch === '\'' || $ch === '"') {
                    if ($inString && $ch === $stringChar) {
                        $esc = 0; $j = $i - 1;
                        while ($j >= 0 && $sql[$j] === '\\') { $esc++; $j--; }
                        if ($esc % 2 === 0) { $inString = false; $stringChar = ''; }
                    } elseif (!$inString) {
                        $inString = true; $stringChar = $ch;
                    }
                }
                if (!$inString && $ch === ';') {
                    $statements[] = trim($buffer);
                    $buffer = '';
                } else {
                    $buffer .= $ch;
                }
            }
            $buffer = trim($buffer);
            if ($buffer !== '') { $statements[] = $buffer; }
            foreach ($statements as $stmt) {
                if ($stmt !== '') { $pdo->exec($stmt); }
            }
        }
    }
    // lightweight migrations for new columns
    try {
        $cols = $pdo->query("SHOW COLUMNS FROM profile")->fetchAll();
        $names = array_map(fn($c) => $c['Field'], $cols ?: []);
        if (!in_array('facebook_url', $names, true)) {
            $pdo->exec("ALTER TABLE profile ADD COLUMN facebook_url VARCHAR(255) NULL AFTER location");
        }
        if (!in_array('instagram_url', $names, true)) {
            $pdo->exec("ALTER TABLE profile ADD COLUMN instagram_url VARCHAR(255) NULL AFTER facebook_url");
        }
    } catch (Throwable $e) { /* ignore */ }
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Database connection failed. Update inc/db.php with correct MySQL credentials or start MySQL.';
    exit;
}
