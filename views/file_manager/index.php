<?php
// views/file_manager/index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager - Antigravity</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 1000px;">
        <div class="flex justify-between items-center mb-4">
            <h1>File Manager</h1>
            <div class="flex gap-4">
                <a href="index.php?route=filemanager/create" class="btn btn-success" style="background: var(--success);">+ Upload New File</a>
                <a href="index.php?route=auth/dashboard" class="btn" style="background: rgba(255,255,255,0.1);">Dashboard</a>
            </div>
        </div>

        <?php if (!empty($flash['success'])): ?><div class="success"><?= $flash['success'] ?></div><?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Filename</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                <tr>
                    <td style="width: 80px; text-align: center;">
                        <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file->getStoredName())): ?>
                            <img src="<?= htmlspecialchars($file->getStoredName()) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                        <?php else: ?>
                            <span style="font-size: 1.5rem;">📄</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($file->getOriginalName()) ?></td>
                    <td>
                        <a href="index.php?route=filemanager/delete&id=<?= $file->getId() ?>" 
                           onclick="return confirm('Delete this file?')" style="color: var(--danger); font-weight: 600;">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($files)): ?>
                    <tr><td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-muted);">No files uploaded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>