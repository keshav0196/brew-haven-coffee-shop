
<?php
session_start();
require_once "config/database.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT id, name, email, password, role
         FROM users
         WHERE email = ? AND role = 'admin'"
    );
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $admin['id'],
            'name' => $admin['name'],
            'email' => $admin['email'],
            'role' => $admin['role']
        ];

        header("Location: admin/dashboard.php");
        exit;
    }

    $error = "Invalid administrator email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Brew Haven</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f3ed;
            color: #38251c;
            margin: 0;
            padding: 50px 16px;
        }
        form {
            max-width: 400px;
            margin: auto;
            padding: 28px;
            background: white;
            border-radius: 12px;
        }
        input {
            box-sizing: border-box;
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #75482d;
            color: white;
            border: 0;
            cursor: pointer;
        }
        .error { color: #b00020; }
    </style>
</head>
<body>
    <form method="post">
        <h1>☕ Admin Login</h1>
        <p>Sign in to manage Brew Haven.</p>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <label for="email">Administrator email</label>
        <input id="email" type="email" name="email" required>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <button type="submit">Login to Dashboard</button>

        <p><a href="index.php">Back to coffee shop</a></p>
    </form>
</body>
</html>
