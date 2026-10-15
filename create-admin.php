
<?php
require_once "config/database.php";

$email = "admin@brewhaven.local";
$name = "Brew Haven Admin";
$temporaryPassword = "ChangeMe123!";

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    exit("Admin account already exists. Delete this setup file.");
}

$hash = password_hash($temporaryPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password, role)
     VALUES (?, ?, ?, 'admin')"
);

$stmt->execute([$name, $email, $hash]);

echo "Admin account created. Delete create-admin.php now.";
