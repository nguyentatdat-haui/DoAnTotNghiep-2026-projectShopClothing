<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? str_replace('{location}', $city_name ?? '', env('PROJECT_TITLE') ?? ''); ?></title>
    <meta name="description" content="<?= $description ?? str_replace('{location}', $city_name ?? '', env('PROJECT_DESCRIPTION') ?? '') ?>">
    <meta name="keywords" content="<?= $keywords ?? str_replace('{location}', $city_name ?? '', env('PROJECT_KEYWORDS') ?? '') ?>">
    <meta property="og:title" content="<?= $title ?? str_replace('{location}', $city_name ?? '', env('PROJECT_TITLE') ?? '') ?>">
    <meta property="og:description" content="<?= $description ?? str_replace('{location}', $city_name ?? '', env('PROJECT_DESCRIPTION') ?? '') ?>">
    <meta property="og:image" content="<?= asset('images/uwaki-logo.webp') ?>">
    <meta property="og:keywords" content="<?= $keywords ?? str_replace('{location}', $city_name ?? '', env('PROJECT_KEYWORDS') ?? '') ?>">
    <?php if (isset($noindex) && $noindex): ?>
        <meta name="robots" content="noindex">
    <?php endif; ?>
    <link rel="canonical" href="<?= current_url() ?>">
    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/responsive-small-screen.css') ?>?v=<?= env('GLOBAL_STYLE_VERSION') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=<?= env('GLOBAL_STYLE_VERSION') ?>">
    
    <?php foreach ($styles ?? [] as $style): ?>
        <link rel="stylesheet" href="<?= asset('css/' . $style) ?>?v=<?= env('GLOBAL_STYLE_VERSION') ?>">
    <?php endforeach; ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">

        <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;700&display=swap" rel="stylesheet">


    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "<?= env('SITENAME') ?>",
            "alternateName": ["<?= env('ALTERNATE_SITENAME') ?>"],
            "url": "<?= base_url() ?>"
        }
    </script>

</head>

<body>
    <div id="site-wrapper">
        <!-- Header -->
        <?php View::include('blocks/header') ?>

        <!-- Main Content -->
        <main class="main-content">
            <?php
            // Get flash messages from session
            $flashMessage = null;
            if (isset($_SESSION['flash']['message'])) {
                $flashMessage = [
                    'message' => $_SESSION['flash']['message'],
                    'type' => $_SESSION['flash']['message_type'] ?? 'info'
                ];
                // Clear flash message after displaying
                unset($_SESSION['flash']['message'], $_SESSION['flash']['message_type']);
            }
            ?>

            <?php if ($flashMessage): 
                $type = $flashMessage['type'];
                $msg = $flashMessage['message'];
                $icons = ['success' => 'fa-check-circle', 'error' => 'fa-exclamation-circle', 'warning' => 'fa-exclamation-triangle', 'info' => 'fa-info-circle'];
                $icon = $icons[$type] ?? $icons['info'];
            ?>
                <div class="flash-toast flash-toast--<?= $type ?>" role="alert" data-flash-toast>
                    <span class="flash-toast-icon"><i class="fas <?= $icon ?>"></i></span>
                    <span class="flash-toast-message"><?= htmlspecialchars($msg) ?></span>
                    <?php if ($type === 'success' && (stripos($msg, 'cart') !== false || stripos($msg, 'added') !== false)): ?>
                        <a href="<?= rtrim(base_url(), '/') ?>/cart" class="flash-toast-action">View cart</a>
                    <?php endif; ?>
                    <button type="button" class="flash-toast-close" aria-label="Close" data-flash-close><i class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </main>

        <!-- Footer -->
        <?php View::include('blocks/footer') ?>
    </div>
    <!-- JavaScript -->
    <script src="<?= asset('js/app.js') ?>?v=<?= env('GLOBAL_STYLE_VERSION') ?>"></script>
    <?php foreach ($scripts ?? [] as $script): ?>
        <script src="<?= asset('js/' . $script) ?>?v=<?= env('GLOBAL_STYLE_VERSION') ?>"></script>
    <?php endforeach; ?>
    <script>
    (function() {
        var toast = document.querySelector('[data-flash-toast]');
        if (!toast) return;
        function dismiss() {
            toast.classList.add('is-dismissing');
            setTimeout(function() { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 300);
        }
        var closeBtn = toast.querySelector('[data-flash-close]');
        if (closeBtn) closeBtn.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    })();
    </script>
</body>

</html>