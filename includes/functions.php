<?php
declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatPrice(float $amount): string
{
    return '₦' . number_format($amount, 2);
}

function renderServiceCard(array $service): string
{
    $slug = e(rawurlencode((string) $service['slug']));
    $title = e((string) $service['title']);
    $description = e((string) $service['description']);
    $price = e(formatPrice((float) $service['price']));
    $icon = e((string) $service['icon']);

    return <<<HTML
        <article class="service-card">
            <span class="service-icon" aria-hidden="true">{$icon}</span>
            <h2>{$title}</h2>
            <p>{$description}</p>
            <p class="service-price">From {$price}</p>
            <a class="text-link" href="service.php?slug={$slug}">View details <span aria-hidden="true">&rarr;</span></a>
        </article>
        HTML;
}
