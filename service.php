<?php
declare(strict_types=1);

$pageTitle = 'Service details';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

$requestedSlug = isset($_GET['slug']) && is_string($_GET['slug']) ? $_GET['slug'] : '';
$service = null;

foreach ($services as $item) {
    if ($item['slug'] === $requestedSlug) {
        $service = $item;
        $pageTitle = $item['title'];
        break;
    }
}

if ($service === null) {
    http_response_code(404);
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <?php if ($service !== null): ?>
            <p class="eyebrow">Service details</p>
            <h1><?= e($service['title']) ?></h1>
            <p><?= e($service['description']) ?></p>
        <?php else: ?>
            <p class="eyebrow">Service not found</p>
            <h1>We couldn’t find that service.</h1>
            <p>The service may have moved or the link may be incorrect. Browse our current services to find what you need.</p>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container content-narrow">
        <?php if ($service !== null): ?>
            <div class="service-detail">
                <span class="service-icon" aria-hidden="true"><?= e($service['icon']) ?></span>
                <p><?= e($service['description']) ?></p>
                <p class="service-price">From <?= e(formatPrice((float) $service['price'])) ?></p>
                <a class="button" href="contact.php">Ask about this service</a>
            </div>
        <?php else: ?>
            <p>Try returning to the services page to explore the available cleaning options.</p>
        <?php endif; ?>
        <a class="text-link" href="services.php"><span aria-hidden="true">&larr;</span> Back to all services</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
