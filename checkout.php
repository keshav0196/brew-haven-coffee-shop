
<?php
session_start();
require_once "config/database.php";

$cart = $_SESSION['cart'] ?? [];
$items = [];
$subtotal = 0;

if (empty($cart)) {
    exit('Your cart is empty. <a href="products.php">Shop now</a>');
}

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $pdo->prepare(
    "SELECT id, name, price
     FROM products
     WHERE is_available = 1 AND id IN ($placeholders)"
);
$stmt->execute($ids);

foreach ($stmt->fetchAll() as $product) {
    $id = $product['id'];
    $quantity = (int) ($cart[$id] ?? 0);

    if ($quantity < 1 || $quantity > 99) {
        continue;
    }

    $product['quantity'] = $quantity;
    $product['line_total'] = $product['price'] * $quantity;
    $subtotal += $product['line_total'];
    $items[] = $product;
}

if (empty($items)) {
    exit('No valid products in your cart.');
}

$isLoggedIn = isset($_SESSION['user']);
$discount = $isLoggedIn ? round($subtotal * 0.10, 2) : 0;
$total = $subtotal - $discount;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        $userId = $isLoggedIn ? $_SESSION['user']['id'] : null;

        $stmt = $pdo->prepare(
            "INSERT INTO orders (user_id, subtotal, discount, total)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $subtotal, $discount, $total]);

        $orderId = $pdo->lastInsertId();

        $itemStmt = $pdo->prepare(
            "INSERT INTO order_items
             (order_id, product_id, product_name, price, quantity)
             VALUES (?, ?, ?, ?, ?)"
        );

        foreach ($items as $item) {
            $itemStmt->execute([
                $orderId,
                $item['id'],
                $item['name'],
                $item['price'],
                $item['quantity']
            ]);
        }

        $pdo->commit();
        unset($_SESSION['cart']);

        echo "<h1>Order placed successfully!</h1>";
        echo "<p>Order number: " . (int)$orderId . "</p>";
        echo "<p>Total: Rp" . number_format($total, 0, ',', '.') . "</p>";
        echo '<a href="products.php">Continue shopping</a>';
        exit;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
        http_response_code(500);
        exit('Unable to place your order. Please try again.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout | Brew Haven</title>
</head>
<body style="font-family:Arial;background:#f8f3ed;color:#38251c;padding:30px">
    <h1>Checkout ☕</h1>

    <p>Subtotal: Rp<?= number_format($subtotal, 0, ',', '.') ?></p>
    <p>Discount: Rp<?= number_format($discount, 0, ',', '.') ?></p>
    <h2>Total: Rp<?= number_format($total, 0, ',', '.') ?></h2>

    <?php if (!$isLoggedIn): ?>
        <p>Log in or register to receive 10% off future orders.</p>
        <p><a href="login.php">Log in</a> or
           <a href="register.php">Register</a></p>
    <?php else: ?>
        <p>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?>!
           Your 10% discount has been applied.</p>
    <?php endif; ?>

    <form method="post">
        <button type="submit">Place Order</button>
    </form>

    <p><a href="cart.php">Back to cart</a></p>
</body>
</html>
