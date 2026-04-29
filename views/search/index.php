<?php
// views/search/index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Search Engine - Antigravity</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 1200px;">
        <div class="flex justify-between items-center mb-4">
            <h1>Data Search Module</h1>
            <a href="index.php?route=auth/dashboard" class="btn" style="background: rgba(255,255,255,0.1);">Back to Dashboard</a>
        </div>
        
        <div style="background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid var(--card-border);">
            <form action="index.php" method="GET" class="flex gap-4 items-center" style="flex-wrap: wrap;">
                <input type="hidden" name="route" value="search/search">
                
                <div style="flex: 1; min-width: 200px;">
                    <label>Keyword</label>
                    <input type="text" name="keyword" value="<?= htmlspecialchars($criteria['keyword'] ?? '') ?>" placeholder="Search...">
                </div>

                <div style="flex: 1; min-width: 200px;">
                    <label>Category</label>
                    <select name="category">
                        <option value="">-- All Categories --</option>
                        <?php if(!empty($categories)): foreach($categories as $cat): ?>
                            <option value="<?= $cat->getId() ?>" <?= ($criteria['category_id'] == $cat->getId()) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat->getName()) ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div style="width: 120px;">
                    <label>Min Price</label>
                    <input type="number" step="0.01" name="price_min" value="<?= htmlspecialchars((string)($criteria['price_min'] ?? '')) ?>" placeholder="0.00">
                </div>

                <div style="width: 120px;">
                    <label>Max Price</label>
                    <input type="number" step="0.01" name="price_max" value="<?= htmlspecialchars((string)($criteria['price_max'] ?? '')) ?>" placeholder="0.00">
                </div>

                <div class="flex gap-4 items-center" style="margin-top: 1.5rem;">
                    <button type="submit" class="btn">Filter Results</button>
                    <a href="index.php?route=search/search" class="btn" style="background: transparent; border: 1px solid var(--card-border);">Reset</a>
                </div>
            </form>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): foreach ($products as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td>
                            <?php if(!empty($p['image_path'])): ?>
                                <img src="<?= htmlspecialchars($p['image_path']) ?>" width="40" style="border-radius: 4px;" alt="img">
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 0.85em;">No Img</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: 600;"><?= htmlspecialchars($p['name']) ?></td>
                        <td><span style="background: rgba(139,92,246,0.2); color: var(--primary); padding: 4px 8px; border-radius: 4px; font-size: 0.85em;"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></span></td>
                        <td style="color: var(--success); font-weight: bold;">$<?= number_format($p['price'], 2) ?></td>
                        <td>
                            <a href="index.php?route=products/show/<?= $p['id'] ?>" style="color: var(--primary); font-weight: 600;">View Detail</a>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">No records matched the advanced filter.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div class="flex gap-4 mt-4" style="justify-content: center;">
                <?php 
                    $queryParams = $_GET;
                    unset($queryParams['page']);
                    $queryString = http_build_query($queryParams);
                    
                    for ($i = 1; $i <= $totalPages; $i++): 
                ?>
                    <a href="index.php?<?= $queryString ?>&page=<?= $i ?>" class="btn" style="padding: 0.5rem 1rem; <?= ($page == $i) ? 'background: var(--primary-hover);' : 'background: rgba(255,255,255,0.1);' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
