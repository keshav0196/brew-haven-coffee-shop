
<?php
session_start();
require_once "config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: products.php");
    exit;
}

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if (!$productId || $productId < 1) {
    header("Location: products.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT id FROM products
     WHERE id = ? AND is_available = 1"
);
$stmt->execute([$productId]);

if (!$stmt->fetch()) {
    header("Location: products.php");
    exit;
}

$_SESSION['cart'] ??= [];
$currentQuantity = (int) ($_SESSION['cart'][$productId] ?? 0);

if ($currentQuantity < 99) {
    $_SESSION['cart'][$productId] = $currentQuantity + 1;
}

header("Location: cart.php");
exit;
