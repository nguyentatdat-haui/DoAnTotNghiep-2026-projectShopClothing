<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メンテナンス中 - ウェブサイト</title>
    <link rel="stylesheet" href="<?= asset('css/maintenance.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php
    // Ensure CityHelper is available
    require_once __DIR__ . '/../../Helpers/CityHelper.php';
    ?>
    <div class="container">
        <div class="illustration">
            <img src="<?= asset('images/maintain.png') ?>" alt="Maintenance Illustration">
        </div>
        <p class="maintenance-message">ウェブサイトは現在メンテナンス中です</p>
        <?php 
        // Only show back to home button if not a 404 error (when city info is available)
        $cityInfo = null;
        try {
            $cityInfo = CityHelper::getCityInfoByName(CityHelper::getCityNameFromUrl());
        } catch (Exception $e) {
            // Ignore errors, cityInfo will remain null
        }
        if ($cityInfo !== null) : ?>
            <a href="<?= base_url() ?>" class="back-to-home-button">ホームページに戻る</a>
        <?php endif; ?>
    </div>
</body>
</html>