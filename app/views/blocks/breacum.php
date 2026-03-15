<?php
// Centralized breadcrumb configuration.
// You can override any value by passing $breadcrumbTitle or $breadcrumbBanner when including this block.
$__breadcrumbMap = [
    'services' => [
        'title' => '会社概要',
        'banner' => 'images/banner.webp',
    ],
];

// Detect page key robustly: priority -> explicit $breadcrumbKey -> any URI segment that matches a known key
$__uriPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$__segments = $__uriPath === '' ? [] : array_values(array_filter(explode('/', $__uriPath)));

$__pageKey = $breadcrumbKey ?? $page ?? null;
if ($__pageKey === null) {
    foreach ($__segments as $__seg) {
        if (isset($__breadcrumbMap[$__seg])) {
            $__pageKey = $__seg;
            break;
        }
    }
}

// Resolve config with graceful fallback
$__config = $__breadcrumbMap[$__pageKey] ?? [
    'title' => $breadcrumbTitle ?? ($title ?? ''),
    'banner' => $breadcrumbBanner ?? 'images/banner.webp',
];

// Allow explicit overrides from include parameters
if (isset($breadcrumbTitle)) {
    $__config['title'] = $breadcrumbTitle;
}
if (isset($breadcrumbBanner)) {
    $__config['banner'] = $breadcrumbBanner;
}
?>

<div class="contact-section-banner">
    <img src="<?= asset($__config['banner']) ?>" alt="<?= htmlspecialchars($__config['title']) ?>" class="banner-image">
    <div class="contact-section-banner-content">
        <h2 class="section-title"><?= htmlspecialchars($__config['title']) ?> <span class="underline"></span></h2>
    </div>
    
</div>

<div class="contact-section-breadcrumb">
    <p>ホーム</p>
    <p style="margin: 0 10px;margin-top: 16px;">
        <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.19205 7.44229L1.94205 13.6923C1.88398 13.7504 1.81504 13.7964 1.73917 13.8278C1.6633 13.8593 1.58198 13.8755 1.49986 13.8755C1.41774 13.8755 1.33642 13.8593 1.26055 13.8278C1.18468 13.7964 1.11574 13.7504 1.05767 13.6923C0.999603 13.6342 0.95354 13.5653 0.922113 13.4894C0.890687 13.4135 0.874512 13.3322 0.874512 13.2501C0.874512 13.168 0.890687 13.0867 0.922113 13.0108C0.95354 12.9349 0.999603 12.866 1.05767 12.8079L6.86627 7.0001L1.05767 1.19229C0.940396 1.07502 0.874512 0.915956 0.874512 0.750103C0.874512 0.584251 0.940396 0.425191 1.05767 0.307916L8.19205 6.55792C8.25016 6.61596 8.29626 6.68489 8.32771 6.76077C8.35916 6.83664 8.37535 6.91797 8.37535 7.0001C8.37535 7.08224 8.35916 7.16357 8.32771 7.23944C8.29626 7.31531 8.25016 7.38425 8.19205 7.44229Z" fill="#1C1C1C"></path>
        </svg>
    </p>
    <p><?= htmlspecialchars($__config['title']) ?></p>
    
    <?php if (isset($breadcrumbDetailTitle) && !empty($breadcrumbDetailTitle)): ?>
        <p style="margin: 0 10px;margin-top: 16px;">
            <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.19205 7.44229L1.94205 13.6923C1.88398 13.7504 1.81504 13.7964 1.73917 13.8278C1.6633 13.8593 1.58198 13.8755 1.49986 13.8755C1.41774 13.8755 1.33642 13.8593 1.26055 13.8278C1.18468 13.7964 1.11574 13.7504 1.05767 13.6923C0.999603 13.6342 0.95354 13.5653 0.922113 13.4894C0.890687 13.4135 0.874512 13.3322 0.874512 13.2501C0.874512 13.168 0.890687 13.0867 0.922113 13.0108C0.95354 12.9349 0.999603 12.866 1.05767 12.8079L6.86627 7.0001L1.05767 1.19229C0.940396 1.07502 0.874512 0.915956 0.874512 0.750103C0.874512 0.584251 0.940396 0.425191 1.05767 0.307916L8.19205 6.55792C8.25016 6.61596 8.29626 6.68489 8.32771 6.76077C8.35916 6.83664 8.37535 6.91797 8.37535 7.0001C8.37535 7.08224 8.35916 7.16357 8.32771 7.23944C8.29626 7.31531 8.25016 7.38425 8.19205 7.44229Z" fill="#1C1C1C"></path>
            </svg>
        </p>
        <p><?= htmlspecialchars($breadcrumbDetailTitle) ?></p>
    <?php endif; ?>
</div>