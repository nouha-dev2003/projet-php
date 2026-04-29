<!-- views/product/create.php -->
<?php
// views/product/create.php
if (!isset($categories) || !isset($csrfToken)) {
    die('Invalid request.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product - Antigravity</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="flex justify-between items-center mb-4">
            <h1>Create New Product</h1>
            <a href="index.php?route=products/index" class="btn" style="background: rgba(255,255,255,0.1);">Back to Products</a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="index.php?route=products/store" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter product name">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Enter product description"></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" step="0.01" id="price" name="price" required placeholder="0.00">
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category->getId() ?>"><?= htmlspecialchars($category->getName()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif" style="padding: 0.5rem; background: rgba(0,0,0,0.1);">
            </div>

            <hr>

            <div class="flex gap-4">
                <button type="submit" class="btn">Create Product</button>
                <a href="index.php?route=products/index" class="btn btn-danger" style="background: rgba(255,255,255,0.1); color: var(--text-main);">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>