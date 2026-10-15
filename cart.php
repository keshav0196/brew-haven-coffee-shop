
<?php
session_start();
require_once "config/database.php";

$cart = $_SESSION['cart'] ?? [];
$items = [];
$subtotal = 0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare(
        "SELECT id, name, price, image
         FROM products
         WHERE is_available = 1
         AND id IN ($placeholders)"
    );
    $stmt->execute($ids);

    foreach ($stmt->fetchAll() as $product) {
        $id = $product['id'];
        $quantity = (int) ($cart[$id] ?? 0);

        if ($quantity < 1) {
            continue;
        }

        $product['quantity'] = $quantity;
        $product['line_total'] = $product['price'] * $quantity;
        $subtotal += $product['line_total'];
        $items[] = $product;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart | Brew Haven</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f3ed;
            color: #38251c;
            margin: 0;
            padding: 30px 7%;
        }
        h1 { text-align: center; }
        .item {
            background: white;
            padding: 18px;
            margin: 14px 0;
            border-radius: 10px;
        }
        .item img {
            width: 90px;
            height: 75px;
            object-fit: cover;
            vertical-align: middle;
            margin-right: 12px;
        }
        button, .btn {
            background: #75482d;
            color: white;
            padding: 10px 16px;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .total { font-size: 22px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🛒 Your Shopping Cart</h1>

    <p><a href="products.php">← Continue shopping</a></p>

    <?php if (empty($items)): ?>
        <p>Your cart is empty. Add some coffee first!</p>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="item">
                <?php if (!empty($item['image'])): ?>
                    <img src="<?= htmlspecialchars($item['image']) ?>"
                         alt="<?= htmlspecialchars($item['name']) ?>">
                <?php endif; ?>

                <strong><?= htmlspecialchars($item['name']) ?></strong>
                <p>Quantity: <?= $item['quantity'] ?></p>
                <p>
                    Rp<?= number_format($item['price'], 0, ',', '.') ?>
                    × <?= $item['quantity'] ?>
                    = Rp<?= number_format($item['line_total'], 0, ',', '.') ?>
                </p>

                <form action="update-cart.php" method="post">
                    <input type="hidden" name="product_id"
                           value="<?= (int) $item['id'] ?>">
                    <label>
                        Quantity:
                        <input type="number" name="quantity"
                               min="0" max="99"
                               value="<?= $item['quantity'] ?>" required>
                    </label>
                    <button type="submit">Update</button>
                </form>
            </div>
        <?php endforeach; ?>

        <p class="total">
            Subtotal: Rp<?= number_format($subtotal, 0, ',', '.') ?>
        </p>
        <p>Your login discount will be calculated at checkout.</p>
        <a class="btn" href="checkout.php">Proceed to Checkout</a>
    <?php endif; ?>
</body>
</html>
