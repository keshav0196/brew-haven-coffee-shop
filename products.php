
<?php
require_once "config/database.php";
require_once "includes/auth.php";

$stmt = $pdo->query(
    "SELECT * FROM products
     WHERE is_available = 1
     ORDER BY id DESC"
);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Menu | Brew Haven</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8f3ed;
            color: #38251c;
        }

        header {
            background: #38251c;
            color: white;
            padding: 20px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header a {
            color: white;
            text-decoration: none;
            margin-left: 16px;
        }

        h1 { text-align: center; margin-top: 35px; }

        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 24px;
            padding: 30px 7%;
        }

        .card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 14px #00000012;
        }

        .card img {
            width: 100%;
            height: 190px;
            object-fit: cover;
        }

        .details { padding: 18px; }

        .price {
            font-weight: bold;
            font-size: 20px;
            color: #75482d;
        }

        button {
            background: #75482d;
            color: white;
            border: 0;
            padding: 12px 16px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }

        .empty { text-align: center; padding: 40px; }
    </style>
</head>
<body>
<header>
    <strong>☕ Brew Haven</strong>
<nav>
    <a href="index.php">Home</a>
    <a href="products.php">Menu</a>
    <a href="cart.php">Cart</a>

    <?php if ($isLoggedIn): ?>
        <span>
            Hi, <?= htmlspecialchars($currentUser['name']) ?>!
        </span>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
    
<?php if (isset($_SESSION['user'])
    && ($_SESSION['user']['role'] ?? '') === 'admin'): ?>
    <a href="admin/dashboard.php">Admin Dashboard</a>
<?php endif; ?>

</nav>
</header>

<h1>Our Coffee Menu ☕</h1>

<div class="menu">
    <?php if (empty($products)): ?>
        <p class="empty">No coffee products available yet.</p>
    <?php endif; ?>

    <?php foreach ($products as $product): ?>
        <article class="card">
            <?php if (!empty($product['image'])): ?>
                <img
                    src="<?= htmlspecialchars($product['image']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                >
            <?php endif; ?>

            <div class="details">
                <h2><?= htmlspecialchars($product['name']) ?></h2>

                <p>
                    <?= htmlspecialchars($product['description'] ?? '') ?>
                </p>

                <p class="price">
                    Rp<?= number_format((float)$product['price'], 0, ',', '.') ?>
                </p>

               
<form action="add-to-cart.php" method="post">
    <input
        type="hidden"
        name="product_id"
        value="<?= (int) $product['id'] ?>"
    >
    <button type="submit">Add to Cart</button>
</form>
            </div>
        </article>
    <?php endforeach; ?>
</div>
</body>
</html>
