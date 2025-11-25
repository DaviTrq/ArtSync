<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($project->title) ?> - Portfólio</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 100%);
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }

        .portfolio-public {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .portfolio-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .portfolio-header h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .portfolio-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
        }

        .media-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .media-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        .media-item img,
        .media-item video {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .media-item audio {
            width: 100%;
            padding: 20px;
        }

        .download-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
        }

        .download-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .download-all {
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <div class="portfolio-public">
        <div class="portfolio-header">
            <h1><?= htmlspecialchars($project->title) ?></h1>
            <?php if ($project->description): ?>
                <p><?= nl2br(htmlspecialchars($project->description)) ?></p>
            <?php endif; ?>
        </div>

        <div class="media-gallery">
            <?php foreach ($project->media as $media): ?>
                <div class="media-item">
                    <?php if ($media->fileType === 'image'): ?>
                        <img src="<?= htmlspecialchars($media->filePath) ?>" alt="Mídia">
                    <?php elseif ($media->fileType === 'video'): ?>
                        <video src="<?= htmlspecialchars($media->filePath) ?>" controls></video>
                    <?php else: ?>
                        <audio src="<?= htmlspecialchars($media->filePath) ?>" controls></audio>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars($media->filePath) ?>" download class="download-btn" title="Download">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="download-all">
            <p style="color: rgba(255,255,255,0.6); margin-bottom: 10px;">
                Total: <?= count($project->media) ?> mídia(s)
            </p>
        </div>
    </div>
</body>

</html>