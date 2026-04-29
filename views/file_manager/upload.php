<?php
// views/file_manager/upload.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File - Antigravity</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <h1 class="mb-4">Upload General File</h1>
        
        <form action="index.php?route=filemanager/store" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            
            <div class="form-group">
                <label>File Name (optional)</label>
                <input type="text" name="name" placeholder="Leave empty to use original name">
            </div>

            <div class="form-group">
                <label>Select File</label>
                <input type="file" name="file" required style="padding: 0.5rem; background: rgba(0,0,0,0.1);">
            </div>

            <hr>

            <div class="flex gap-4">
                <button type="submit" class="btn">Upload File</button>
                <a href="index.php?route=filemanager/index" class="btn" style="background: rgba(255,255,255,0.1);">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>