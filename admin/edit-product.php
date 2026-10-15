
<?php
session_start();
require_once "../config/database.php";

if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'admin'
) {
    http_response_code(403);
    exit("Access denied. Administration login Required");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    exit("Product not found.");
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

    if (
        $name === '' || $description === '' ||
        $price === false || $price === null || $price <= 0
    ) {
        $error = "Enter a valid name, description and price.";
    } else {
        
$availability = isset($_POST['is_available']) ? 1 : 0;

$stmt = $pdo->prepare(
    "UPDATE products
     SET name = ?, description = ?, price = ?, is_available = ?
     WHERE id = ?"
);

$stmt->execute([
    $name,
    $description,
    $price,
    $availability,
    $id
]);

        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Coffee | Brew Haven</title>
</head>
<body style="font-family:Arial;max-width:500px;margin:40px auto">
    <h1>Edit Coffee ☕</h1>

    <?php if ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Coffee name</label><br>
        <input name="name" maxlength="150" required
               value="<?= htmlspecialchars($_POST['name'] ?? $product['name']) ?>"
               style="width:100%;padding:10px"><br><br>

        <label>Description</label><br>
        <textarea name="description" required
                  style="width:100%;padding:10px"><?= htmlspecialchars($_POST['description'] ?? $product['description']) ?></textarea><br><br>

        <label>Price in Rp</label><br>
        <input type="number" name="price" min="0.01" step="0.01"
               required
               value="<?= htmlspecialchars((string)($_POST['price'] ?? $product['price'])) ?>"
               style="width:100%;padding:10px"><br><br>
        
<label>
    <input
        type="checkbox"
        name="is_available"
        value="1"
        <?= isset($_POST['is_available'])
            ? 'checked'
            : (!isset($_POST['name']) && $product['is_available']
                ? 'checked'
                : '') ?>
    >
    Available for customers to order
</label>
<br><br>


        <button type="submit">Save Changes</button>
    </form>

    <p><a href="dashboard.php">← Back to Dashboard</a></p>
</body>
</html>
