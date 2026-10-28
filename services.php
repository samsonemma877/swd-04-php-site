<?php
declare(strict_types=1);

$pageTitle = 'Services';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Our services</p>
        <h1>Care for every kind of day.</h1>
        <p>Choose a little regular help or a full-home refresh. We’ll shape the clean around you.</p>
    </div>
</section>

<section class="section">
    <div class="container card-grid">
        <?php foreach ($services as $service): ?>
            <?= renderServiceCard($service) ?>
        <?php endforeach; ?>
    </div>
</section>

<section class="highlight-section">
    <div class="container highlight-content">
        <div>
            <p class="eyebrow">Not sure what you need?</p>
            <h2>We’ll help you find the right clean.</h2>
        </div>
        <a class="button button-light" href="contact.php">Ask us a question</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
