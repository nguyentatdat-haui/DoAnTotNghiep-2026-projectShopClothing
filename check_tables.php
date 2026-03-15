<?php
$pdo = new PDO("mysql:host=localhost;dbname=clothing_shop", "root", "");
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Tables in clothing_shop:\n";
foreach ($tables as $table) {
    echo "- $table\n";
}
