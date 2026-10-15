
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit;
}

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (!$productId || $quantity === false || $quantity === null
    || $quantity < 0 || $quantity > 99) {
    header("Location: cart.php");
    exit;
}

if (!isset($_SESSION['cart'][$productId])) {
    header("Location: cart.php");
    exit;
}

if ($quantity === 0) {
    unset($_SESSION['cart'][$productId]);
} else {
    $_SESSION['cart'][$productId] = $quantity;
}

header("Location: cart.php");
exit;
