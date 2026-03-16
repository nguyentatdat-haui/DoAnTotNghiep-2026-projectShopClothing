<?php
require_once __DIR__ . '/app/bootstrap.php';

use Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    $newEmail = 'datadmin@gmai.com';
    $newPassword = '123456';
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Check if admin exist
    $stmt = $db->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        $stmt = $db->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
        $stmt->execute([$newEmail, $hashedPassword, $admin['id']]);
        echo "Successfully updated admin account to: $newEmail with password: $newPassword\n";
    } else {
        // Create if not exist
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin', $newEmail, $hashedPassword, 'admin']);
        echo "Successfully created admin account: $newEmail with password: $newPassword\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
