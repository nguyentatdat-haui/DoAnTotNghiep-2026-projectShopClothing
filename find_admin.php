<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=clothing_shop", "root", "");
    $stmt = $pdo->query("SELECT email, name, role FROM users LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Users in database:\n";
    foreach ($users as $user) {
        echo "Email: " . $user['email'] . " | Name: " . $user['name'] . " | Role: " . $user['role'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
