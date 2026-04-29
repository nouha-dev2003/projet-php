<?php
// No die() anywhere – safe defaults for everything
$products ??= [];
$totalPages ??= 1;
$page ??= 1;
$search ??= '';
$categories ??= [];
$csrfToken ??= $_SESSION['csrf_token'] ?? '';
$flash ??= [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Antigravity</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 1200px;">
        <div class="flex justify-between items-center mb-4">
            <h1>Products</h1>
            <a href="index.php?route=auth/dashboard" class="btn" style="background: rgba(255,255,255,0.1);">Back to Dashboard</a>
        </div>

        <?php if(!empty($flash['success'])): ?>
            <div class="success"><?= htmlspecialchars($flash['success']) ?></div>
        <?php endif; ?>
        <?php if(!empty($flash['error'])): ?>
            <div class="error"><?= htmlspecialchars($flash['error']) ?></div>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" class="flex gap-4 items-center">
                <input type="hidden" name="route" value="products/index">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search products..." style="width: 300px;">
                <button type="submit" class="btn">Search</button>
            </form>
            <a href="index.php?route=products/create" class="btn btn-success" style="background-color: var(--success);">+ Add Product</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p->getId() ?></td>
                    <td style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($p->getName()) ?></td>
                    <td><span style="color: var(--success); font-weight: bold;">$<?= number_format($p->getPrice(), 2) ?></span></td>
                    <td><span style="background: rgba(139,92,246,0.2); color: var(--primary); padding: 4px 8px; border-radius: 4px; font-size: 0.85em;"><?= htmlspecialchars($categories[$p->getCategoryId()] ?? 'None') ?></span></td>
                    <td>
                        <div class="flex gap-4">
                            <a href="index.php?route=products/show/<?= $p->getId() ?>" style="color: var(--primary);">View</a>
                            <a href="index.php?route=products/edit/<?= $p->getId() ?>" style="color: #f59e0b;">Edit</a>
                            <form method="POST" action="index.php?route=products/delete/<?= $p->getId() ?>" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <button type="submit" onclick="return confirm('Delete this product?')" style="background: transparent; color: var(--danger); padding: 0; font-weight: normal;">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                    <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
        <div class="flex gap-4 mt-4" style="justify-content: center;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?route=products/index&page=<?= $i ?>&search=<?= urlencode($search) ?>" class="btn" style="padding: 0.5rem 1rem; <?= $i == $page ? 'background: var(--primary-hover);' : 'background: rgba(255,255,255,0.1);' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>