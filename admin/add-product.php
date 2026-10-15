
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

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

    if (
        $name === '' ||
        $description === '' ||
        $price === false ||
        $price === null ||
        $price <= 0
    ) {
        $error = "Enter a name, description, and valid price.";
    } elseif (
        !isset($_FILES['image']) ||
        $_FILES['image']['error'] !== UPLOAD_ERR_OK
    ) {
        $error = "Please select a coffee image.";
    } else {
        $tmp = $_FILES['image']['tmp_name'];
        $imageInfo = getimagesize($tmp);
        $mime = $imageInfo['mime'] ?? '';

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (
            !$imageInfo ||
            !isset($allowed[$mime]) ||
            $_FILES['image']['size'] > 3 * 1024 * 1024
        ) {
            $error = "Use a JPG, PNG, or WebP image under 3 MB.";
        } else {
            $uploadDir = "../uploads/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = bin2hex(random_bytes(16))
                . "." . $allowed[$mime];

            if (!move_uploaded_file($tmp, $uploadDir . $filename)) {
                $error = "Image upload failed. Check folder permissions.";
            } else {
                $imagePath = "uploads/" . $filename;

                try {
                    $stmt = $pdo->prepare(
                        "INSERT INTO products
                         (name, description, price, image, is_available)
                         VALUES (?, ?, ?, ?, 1)"
                    );

                    $stmt->execute([
                        $name,
                        $description,
                        $price,
                        $imagePath
                    ]);

                    header("Location: dashboard.php");
                    exit;
                } catch (Throwable $e) {
                    @unlink($uploadDir . $filename);
                    error_log($e->getMessage());
                    $error = "Could not save the product. Check the database columns.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Product | Brew Haven</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f3ed;
            color: #38251c;
            padding: 30px;
        }
        form {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
        }
        input, textarea {
            box-sizing: border-box;
            width: 100%;
            padding: 11px;
            margin: 8px 0 18px;
        }
        button {
            background: #75482d;
            color: white;
            padding: 12px 18px;
            border: 0;
            cursor: pointer;
        }
        .error { color: #b00020; }
    </style>
</head>
<body>
    <form method="post" enctype="multipart/form-data">
        <h1>☕ Add a Coffee Product</h1>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <label for="name">Coffee name</label>
        <input id="name" name="name" maxlength="150" required>

        <label for="description">Description</label>
        <textarea id="description" name="description"
                  rows="4" required></textarea>

        <label for="price">Price in rupiah (Rp)</label>
        <input id="price" type="number" name="price"
               min="0.01" step="0.01" required>

        <label for="image">Coffee image (JPG, PNG, or WebP; max 3 MB)</label>
        <input id="image" type="file" name="image"
               accept=".jpg,.jpeg,.png,.webp" required>

        <button type="submit">Save Product</button>
        <p><a href="dashboard.php">← Back to Dashboard</a></p>
    </form>
</body>
</html>
