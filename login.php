
<?php
session_start();
require_once "config/database.php";
$loginType = $_POST['login_type'] ?? '';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $loginType = $_POST['login_type'] ?? 'customer';

    if (!in_array($loginType, ['customer', 'admin'], true)) {
        $error = "Please select a valid login type.";
    } else {
        $stmt = $pdo->prepare(
            "SELECT id, name, email, password, role
             FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (
            $user &&
            password_verify($password, $user['password']) &&
            $user['role'] === $loginType
        ) {
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            
if ($user['role'] === 'admin') {
    header("Location: index.php");
} else {
    header("Location: index.php");
}
exit;

        }

        $error = "Invalid email, password, or login type.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Brew Haven</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f3ed;
            color: #38251c;
            margin: 0;
            padding: 45px 16px;
        }
        form {
            max-width: 400px;
            margin: auto;
            padding: 28px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 18px #0001;
        }
        input[type="email"],
        input[type="password"] {
            box-sizing: border-box;
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
        }
        .options {
            display: flex;
            gap: 12px;
            margin: 12px 0 20px;
        }
        .options label {
            flex: 1;
            padding: 14px 8px;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #75482d;
            color: white;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
        }
        .error { color: #b00020; }
    </style>
</head>
<body>
<form method="post">
    <h1>☕ Brew Haven</h1>
    <h2>Welcome Back</h2>
    <p>Choose how you want to log in.</p>

    <?php if ($error): ?>
        <p class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <div class="options">
        <label>
            <input type="radio" name="login_type"
                   value="customer" checked>
            Customer Login
        </label>
        <label>
            <input type="radio" name="login_type"
                   value="admin">
            Admin Login
        </label>
    </div>

    <label for="email">Email address</label>
    <input id="email" type="email" name="email"
           autocomplete="username" required>

    <label for="password">Password</label>
    <input id="password" type="password" name="password"
           autocomplete="current-password" required>

    <button type="submit">Login</button>

    <p>New customer? <a href="register.php">Create an account</a></p>
    <p><a href="index.php">Back to coffee shop</a></p>
</form>
</body>
</html>
