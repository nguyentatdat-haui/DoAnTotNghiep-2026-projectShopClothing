<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=clothing_shop", "root", "");
    $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute(['admin@example.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo "Hash: " . $user['password'] . "\n";
    } else {
        echo "Admin user not found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
