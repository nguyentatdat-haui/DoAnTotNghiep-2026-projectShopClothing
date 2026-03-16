<?php
try {
    $dsn = "mysql:host=127.0.0.1;port=3307;dbname=clothing_shop";
    $pdo = new PDO($dsn, "root", "");
    echo "Connected to clothing_shop on 3307 successfully!\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(", ", $tables) . "\n";
} catch (Exception $e) {
    echo "Error on 3307: " . $e->getMessage() . "\n";
}
