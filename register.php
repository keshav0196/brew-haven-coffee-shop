
<?php
session_start();
require_once "config/database.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($password) < 8) {
        $error = "Enter a name, valid email, and password of at least 8 characters.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = "This email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password, role)
                 VALUES (?, ?, ?, 'customer')"
            );
            $stmt->execute([$name, $email, $hash]);

            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Brew Haven</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f3ed;
            color: #38251c;
            margin: 0;
            padding: 40px 16px;
        }
        form {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 28px;
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
    <h1>Create Account ☕</h1>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <label for="name">Full name</label>
    <input id="name" name="name" required maxlength="100"
           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

    <label for="email">Email address</label>
    <input id="email" type="email" name="email" required maxlength="150"
           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

    <label for="password">Password (minimum 8 characters)</label>
    <input id="password" type="password" name="password"
           minlength="8" required>

    <button type="submit">Register</button>

    <p>Already registered? <a href="login.php">Log in</a></p>
    <a href="products.php">Back to menu</a>
</form>
</body>
</html>
