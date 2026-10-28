<?php
declare(strict_types=1);

$pageTitle = 'Home';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container hero-content">
        <p class="eyebrow">A little more time for what matters</p>
        <h1>A clean home feels like a fresh start.</h1>
        <p class="hero-copy">Enjoy a beautifully cared-for space with friendly, reliable cleaning tailored to your routine.</p>
        <a class="button" href="services.php">Explore our services</a>
    </div>
</section>

<section class="section">
    <div class="container intro-grid">
        <div>
            <p class="eyebrow">Welcome to Prime Cleaners</p>
            <h2>Thoughtful cleaning. Lovely results.</h2>
        </div>
        <div>
            <p>From the weekly essentials to a detailed seasonal refresh, our local team brings care and attention to every visit. Tell us what you need and we’ll help make home feel its best.</p>
            <a class="text-link" href="about.php">Get to know us <span aria-hidden="true">&rarr;</span></a>
        </div>
    </div>
</section>

<section class="highlight-section">
    <div class="container highlight-content">
        <div>
            <p class="eyebrow">Ready for a calmer week?</p>
            <h2>Let us take cleaning off your list.</h2>
        </div>
        <a class="button button-light" href="contact.php">Get in touch</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
