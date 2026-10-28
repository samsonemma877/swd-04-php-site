<?php
declare(strict_types=1);

$pageTitle = 'About';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">About us</p>
        <h1>Good people, doing good work.</h1>
        <p>We believe a clean space can make everyday life feel a little lighter.</p>
    </div>
</section>

<section class="section">
    <div class="container content-narrow">
        <h2>A local team that cares</h2>
        <p>Prime Cleaners Ltd was created to make dependable home cleaning feel personal and straightforward. We take time to understand your priorities, arrive ready to work, and treat every home with respect.</p>
        <p>Our approach is simple: clear communication, thoughtful attention to detail, and a clean that fits your life. Whether you need regular help or a one-off refresh, we’re happy to find the right option with you.</p>
        <a class="text-link" href="contact.php">Tell us what you need <span aria-hidden="true">&rarr;</span></a>
    </div>
</section>

<section class="section team-section">
    <div class="container">
        <div class="section-heading">
            <p class="eyebrow">Meet the team</p>
            <h2>People who care about the details.</h2>
        </div>
        <div class="team-grid">
            <?php foreach ($team as $member): ?>
                <article class="team-card">
                    <h3><?= e($member['name']) ?></h3>
                    <p class="team-role"><?= e($member['role']) ?></p>
                    <p><?= e($member['bio']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
