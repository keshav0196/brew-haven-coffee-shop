
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brew Haven | Coffee Shop</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8f3ed;
            color: #38251c;
        }
        header {
            background: #38251c;
            color: white;
            padding: 22px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header a {
            color: white;
            text-decoration: none;
            margin-left: 18px;
        }
        .hero {
            text-align: center;
            padding: 100px 20px;
        }
        .hero h1 {
            font-size: 46px;
            margin-bottom: 15px;
        }
        .hero p {
            font-size: 18px;
            color: #70594c;
        }
        .menu-button {
            display: inline-block;
            background: #75482d;
            color: white;
            padding: 13px 24px;
            margin-top: 20px;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <strong>☕ Brew Haven</strong>
        <nav>
            <a href="index.php">Home</a>
            <a href="products.php">Menu</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            
<?php if (
    isset($_SESSION['user']) &&
    ($_SESSION['user']['role'] ?? '') === 'admin'
): ?>
    <a href="admin/dashboard.php">Admin Dashboard</a>
<?php endif; ?>

        </nav>
    </header>

    <main class="hero">
        <h1>Your Daily Cup of Happiness</h1>
        <p>Freshly brewed coffee, made just for you.</p>
        <p>Register and log in to enjoy 10% off your order!</p>
        <a class="menu-button" href="products.php">
            Explore Our Coffee
        </a>
    </main>
</body>
</html>
