
<?php
session_start();
require_once "../config/database.php";

if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'admin'
) {
    http_response_code(403);
    exit("Access denied. Administrator login required.");
}

$stmt = $pdo->query(
    "SELECT id, name, price, is_available
     FROM products ORDER BY id DESC"
);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Brew Haven</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f3ed;
            color: #38251c;
            padding: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th { background: #38251c; color: white; }
        a { color: #75482d; }
        .button {
            display: inline-block;
            background: #75482d;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <h1>☕ Brew Haven Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</p>

    <a class="button" href="add-product.php">+ Add Product</a>
    <a href="../products.php">View Customer Menu</a>
    <a href="../logout.php">Logout</a>

    <h2>Products</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Availability</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= (int)$product['id'] ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td>Rp<?= number_format((float)$product['price'], 0, ',', '.') ?></td>
<td>
    <?= $product['is_available'] ? 'Available' : 'Unavailable' ?>
    | 
    <a href="edit-product.php?id=<?= (int)$product['id'] ?>">
        Edit
    </a>
</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

