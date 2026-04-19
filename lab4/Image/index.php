<?php
declare(strict_types=1);

/**
 * Absolute filesystem path to the image directory.
 *
 * @var string
 */
$dir = __DIR__ . '/Images/';

/**
 * Web-accessible path to the image directory (used in <img src="">).
 *
 * @var string
 */
$dirWeb = 'Images/';

/**
 * Scan the image directory and retrieve all files and folders.
 *
 * @var array<int, string>|false $files
 */
$files = scandir($dir);

/**
 * Filtered list of valid JPG image filenames.
 *
 * @var array<int, string> $images
 */
if ($files === false) {
    $images = [];
} else {
    /**
     * Filter directory contents:
     * - Exclude "." and ".."
     * - Allow only .jpg files (case-insensitive)
     * - Ensure the file actually exists
     */
    $images = array_filter($files, function ($file) use ($dir) {
        if ($file === '.' || $file === '..') {
            return false;
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($ext !== 'jpg') {
            return false;
        }

        return is_file($dir . $file);
    });

    /**
     * Reindex array keys after filtering.
     *
     * @var array<int, string>
     */
    $images = array_values($images);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Gallery</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        header, footer { padding: 16px 20px; background: #f2f2f2; }
        nav { padding: 10px 20px; background: #e7e7e7; }
        nav a { margin-right: 12px; text-decoration: none; color: #333; }
        main { padding: 20px; }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
        }

        .card {
            width: 250px;
            height: 200px;
            border: 1px solid #ddd;
            padding: 8px;
            background: #fff;
            box-sizing: border-box;
        }

        .card img {
            width: 100%;
            height: 160px;
            object-fit: contain;
            display: block;
        }

        .caption {
            margin-top: 6px;
            font-size: 12px;
            color: #555;
            word-break: break-word;
        }

        .empty {
            padding: 12px;
            border: 1px dashed #999;
            background: #fafafa;
        }
    </style>
</head>
<body>

<header>
    <h1>My Photo Gallery</h1>
</header>

<nav>
    <a href="#">Home</a>
    <a href="#">Gallery</a>
    <a href="#">Contacts</a>
</nav>

<main>
    <h2>Images</h2>

    <?php if (count($images) === 0): ?>
        <div class="empty">
            No .jpg files were found in the <b>image</b> directory.
            Please ensure images are located in <code>./image/</code>.
        </div>
    <?php else: ?>
        <div class="gallery">
            <?php foreach ($images as $file): ?>
                <?php
                /**
                 * Generate a safe URL for the image file.
                 * rawurlencode() ensures proper encoding of filenames with spaces.
                 *
                 * @var string $url
                 */
                $url = $dirWeb . rawurlencode($file);
                ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($url) ?>" alt="<?= htmlspecialchars($file) ?>">
                    <div class="caption"><?= htmlspecialchars($file) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer>
    <small>© <?= date('Y') ?> PHP Gallery</small>
</footer>

</body>
</html>