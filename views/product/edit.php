<!-- views/product/edit.php -->
<?php
// views/product/edit.php
if (!isset($product) || !isset($categories) || !isset($csrfToken)) {
    die('Invalid request.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Antigravity</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="flex justify-between items-center mb-4">
            <h1>Edit Product</h1>
            <a href="index.php?route=products/index" class="btn" style="background: rgba(255,255,255,0.1);">Back to Products</a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="index.php?route=products/update/<?= $product->getId() ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($product->getName()) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?= htmlspecialchars($product->getDescription()) ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" step="0.01" id="price" name="price" value="<?= $product->getPrice() ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category->getId() ?>" <?= ($product->getCategoryId() == $category->getId()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category->getName()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="background: rgba(0,0,0,0.1); padding: 1rem; border-radius: 8px;">
                <label>Current Image</label>
                <div class="mt-4">
                    <?php if ($product->getImagePath()): ?>
                        <img src="<?= htmlspecialchars($product->getImagePath()) ?>" alt="Current Image" style="max-width: 150px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                        <p class="mt-4"><small style="color: var(--text-muted);">Upload a new image below to replace this one. Leave empty to keep it.</small></p>
                    <?php else: ?>
                        <p style="color: var(--text-muted);">No image uploaded.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="image">New Image</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif" style="padding: 0.5rem; background: rgba(0,0,0,0.1);">
            </div>

            <hr>

            <div class="flex gap-4">
                <button type="submit" class="btn">Update Product</button>
                <a href="index.php?route=products/show/<?= $product->getId() ?>" class="btn" style="background: rgba(255,255,255,0.1);">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>