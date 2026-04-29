<?php
$product ??= null;
if (!$product) die('Product not found.');
$categoryName ??= null;
$flash ??= [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product->getName()) ?> - Antigravity</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="flex justify-between items-center mb-4">
            <h1><?= htmlspecialchars($product->getName()) ?></h1>
            <div class="flex gap-4">
                <a href="index.php?route=products/edit/<?= $product->getId() ?>" class="btn" style="background: var(--primary);">Edit Product</a>
                <a href="index.php?route=products/index" class="btn" style="background: rgba(255,255,255,0.1);">Back to List</a>
            </div>
        </div>

        <?php if (!empty($flash['success'])): ?>
            <div class="success"><?= $flash['success'] ?></div>
        <?php endif; ?>

        <hr>

        <div class="flex gap-4" style="align-items: flex-start; gap: 2rem;">
            <div style="flex: 1; background: rgba(0,0,0,0.1); padding: 2rem; border-radius: 8px;">
                <p class="mb-4"><strong style="color: var(--text-muted);">Description:</strong><br><br><?= nl2br(htmlspecialchars($product->getDescription())) ?></p>
                <p class="mb-4"><strong style="color: var(--text-muted);">Price:</strong><br><span style="font-size: 1.5rem; color: var(--success); font-weight: bold;">$<?= number_format($product->getPrice(), 2) ?></span></p>
                <p><strong style="color: var(--text-muted);">Category:</strong><br><span style="background: rgba(139,92,246,0.2); color: var(--primary); padding: 4px 8px; border-radius: 4px;"><?= htmlspecialchars($categoryName ?? 'None') ?></span></p>
            </div>

            <?php if ($product->getImagePath()): ?>
                <div style="flex: 1; text-align: center;">
                    <img src="<?= htmlspecialchars($product->getImagePath()) ?>" style="max-width: 100%; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);" alt="Product Image">
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>