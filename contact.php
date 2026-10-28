<?php
declare(strict_types=1);

session_start();

$pageTitle = 'Contact';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];
$values = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'service' => '',
    'message' => '',
];
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach ($values as $field => $defaultValue) {
        $submittedValue = $_POST[$field] ?? $defaultValue;
        if (!is_string($submittedValue)) {
            $values[$field] = '';
            $errors[$field] = 'Please enter a valid value.';
            continue;
        }

        $values[$field] = trim($submittedValue);
    }

    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($submittedToken) || !hash_equals($csrfToken, $submittedToken)) {
        $errors['csrf'] = 'Your form session expired. Please check the form and submit it again.';
    }

    if ($values['name'] === '') {
        $errors['name'] = 'Please enter your name.';
    }

    if ($values['email'] === '') {
        $errors['email'] = 'Please enter your email address.';
    } elseif (filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($values['service'] === '') {
        $errors['service'] = 'Please choose a service.';
    } else {
        $serviceIsValid = false;
        foreach ($services as $service) {
            if ($service['slug'] === $values['service']) {
                $serviceIsValid = true;
                break;
            }
        }
        if (!$serviceIsValid) {
            $errors['service'] = 'Please choose one of the listed services.';
        }
    }

    if ($values['message'] === '') {
        $errors['message'] = 'Please enter a message.';
    } else {
        $messageLength = preg_match_all('/./us', $values['message']);
        if ($messageLength === false || $messageLength < 10 || $messageLength > 1000) {
            $errors['message'] = 'Your message must be between 10 and 1,000 characters.';
        }
    }

    if ($errors === []) {
        try {
            $jsonLine = json_encode([
                'timestamp' => date(DATE_ATOM),
                'name' => $values['name'],
                'email' => $values['email'],
                'phone' => $values['phone'],
                'service' => $values['service'],
                'message' => $values['message'],
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (JsonException $exception) {
            error_log('Unable to encode contact form submission: ' . $exception->getMessage());
            $errors['_form'] = 'We could not save your message right now. Please try again.';
            $jsonLine = null;
        }

        if ($jsonLine !== null) {
            $storageDirectory = __DIR__ . '/storage';
            $messagesFile = $storageDirectory . '/messages.jsonl';

            if (!is_dir($storageDirectory)
                && !mkdir($storageDirectory, 0750, true)
                && !is_dir($storageDirectory)
            ) {
                error_log('Unable to create contact form storage directory: ' . $storageDirectory);
                $errors['_form'] = 'We could not save your message right now. Please try again.';
            } else {
                $bytesWritten = file_put_contents(
                    $messagesFile,
                    $jsonLine . PHP_EOL,
                    FILE_APPEND | LOCK_EX
                );

                if ($bytesWritten === false) {
                    error_log('Unable to append contact form submission to: ' . $messagesFile);
                    $errors['_form'] = 'We could not save your message right now. Please try again.';
                } else {
                    header('Location: contact.php?sent=1');
                    exit;
                }
            }
        }
    }
}

$isSubmissionSuccessful = ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST'
    && isset($_GET['sent'])
    && $_GET['sent'] === '1';

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Contact</p>
        <h1>Let’s talk about your home.</h1>
        <p>Ask a question or tell us what kind of cleaning would make your week easier.</p>
    </div>
</section>

<section class="section">
    <div class="container contact-layout">
        <div class="contact-intro">
            <h2>We’d love to hear from you.</h2>
            <p>Send us a message and our friendly team will be in touch to discuss your needs.</p>
            <a class="contact-email-link" href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a>
        </div>
        <div class="form-panel">
            <?php if ($isSubmissionSuccessful): ?>
                <div class="alert alert-success" role="status">Thanks for reaching out. Your message has been received.</div>
            <?php endif; ?>

            <?php if (isset($errors['_form'])): ?>
                <div class="alert alert-error" role="alert"><?= e($errors['_form']) ?></div>
            <?php endif; ?>

            <?php if (isset($errors['csrf'])): ?>
                <div class="alert alert-error" role="alert"><?= e($errors['csrf']) ?></div>
            <?php endif; ?>

            <form action="contact.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <div class="form-field">
                    <label for="name">Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        autocomplete="name"
                        required
                        value="<?= e($values['name']) ?>"
                        <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="name-error"' : '' ?>
                    >
                    <?php if (isset($errors['name'])): ?>
                        <p class="field-error" id="name-error"><?= e($errors['name']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-field">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        value="<?= e($values['email']) ?>"
                        <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?>
                    >
                    <?php if (isset($errors['email'])): ?>
                        <p class="field-error" id="email-error"><?= e($errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-field">
                    <label for="phone">Phone <span class="optional">(optional)</span></label>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        autocomplete="tel"
                        value="<?= e($values['phone']) ?>"
                        <?= isset($errors['phone']) ? 'aria-invalid="true" aria-describedby="phone-error"' : '' ?>
                    >
                    <?php if (isset($errors['phone'])): ?>
                        <p class="field-error" id="phone-error"><?= e($errors['phone']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-field">
                    <label for="service">Service</label>
                    <select
                        id="service"
                        name="service"
                        required
                        <?= isset($errors['service']) ? 'aria-invalid="true" aria-describedby="service-error"' : '' ?>
                    >
                        <option value="">Choose a service</option>
                        <?php foreach ($services as $service): ?>
                            <option
                                value="<?= e($service['slug']) ?>"
                                <?= $values['service'] === $service['slug'] ? 'selected' : '' ?>
                            ><?= e($service['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['service'])): ?>
                        <p class="field-error" id="service-error"><?= e($errors['service']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-field">
                    <label for="message">Message</label>
                    <textarea
                        id="message"
                        name="message"
                        rows="6"
                        required
                        <?= isset($errors['message']) ? 'aria-invalid="true" aria-describedby="message-error"' : '' ?>
                    ><?= e($values['message']) ?></textarea>
                    <?php if (isset($errors['message'])): ?>
                        <p class="field-error" id="message-error"><?= e($errors['message']) ?></p>
                    <?php endif; ?>
                </div>

                <button class="button" type="submit">Send message</button>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
