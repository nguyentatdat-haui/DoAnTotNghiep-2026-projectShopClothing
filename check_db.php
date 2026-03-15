<?php
try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $stmt = $pdo->query("SHOW DATABASES");
    $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Databases: " . implode(", ", $dbs) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
try {
    $pdo = new PDO("mysql:host=localhost", "root", "root");
    $stmt = $pdo->query("SHOW DATABASES");
    $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Databases (with root/root): " . implode(", ", $dbs) . "\n";
} catch (Exception $e) {
    echo "Error (with root/root): " . $e->getMessage() . "\n";
}
