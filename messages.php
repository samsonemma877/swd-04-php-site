<?php
declare(strict_types=1);

session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$pageTitle = 'Messages';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/config.php';

if (!isset($_SESSION['messages_csrf_token']) || !is_string($_SESSION['messages_csrf_token'])) {
    $_SESSION['messages_csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['messages_csrf_token'];
$isAuthenticated = ($_SESSION['messages_authenticated'] ?? false) === true;
$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($submittedToken) || !hash_equals($csrfToken, $submittedToken)) {
        $error = 'Your form session expired. Reload the page and try again.';
    } elseif (isset($_POST['logout'])) {
        unset($_SESSION['messages_authenticated']);
        session_regenerate_id(true);
        header('Location: messages.php');
        exit;
    } elseif (!$isAuthenticated) {
        $submittedPassword = $_POST['password'] ?? '';
        if (is_string($submittedPassword) && hash_equals($messagesPassword, $submittedPassword)) {
            session_regenerate_id(true);
            $_SESSION['messages_authenticated'] = true;
            header('Location: messages.php');
            exit;
        }

        $error = 'The password was not correct.';
    }
}

$isAuthenticated = ($_SESSION['messages_authenticated'] ?? false) === true;
$messages = [];
$readError = '';
$readWarnings = 0;

if ($isAuthenticated) {
    $messagesFile = __DIR__ . '/storage/messages.jsonl';

    if (is_file($messagesFile)) {
        $contents = file_get_contents($messagesFile);
        if ($contents === false) {
            error_log('Unable to read contact submissions from: ' . $messagesFile);
            $readError = 'Messages could not be loaded. Check the server error log or file permissions.';
        } else {
            $lines = preg_split('/\r\n|\n|\r/', $contents);
            if ($lines === false) {
                error_log('Unable to split contact submissions from: ' . $messagesFile);
                $readError = 'Messages could not be loaded. Check the server error log.';
            } else {
                foreach ($lines as $lineNumber => $line) {
                    if (trim($line) === '') {
                        continue;
                    }

                    try {
                        $message = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                    } catch (JsonException $exception) {
                        $readWarnings++;
                        error_log(
                            'Invalid contact submission JSON at line '
                            . ($lineNumber + 1)
                            . ': '
                            . $exception->getMessage()
                        );
                        continue;
                    }

                    if (!is_array($message)) {
                        $readWarnings++;
                        error_log('Invalid contact submission structure at line ' . ($lineNumber + 1));
                        continue;
                    }

                    $messages[] = [
                        'timestamp' => is_string($message['timestamp'] ?? null) ? $message['timestamp'] : '',
                        'name' => is_string($message['name'] ?? null) ? $message['name'] : '',
                        'email' => is_string($message['email'] ?? null) ? $message['email'] : '',
                        'phone' => is_string($message['phone'] ?? null) ? $message['phone'] : '',
                        'service' => is_string($message['service'] ?? null) ? $message['service'] : '',
                        'message' => is_string($message['message'] ?? null) ? $message['message'] : '',
                    ];
                }

                usort(
                    $messages,
                    static function (array $first, array $second): int {
                        $firstTime = strtotime($first['timestamp']) ?: 0;
                        $secondTime = strtotime($second['timestamp']) ?: 0;

                        return $secondTime <=> $firstTime;
                    }
                );
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Private area</p>
        <h1>Contact messages</h1>
        <p>Review submissions sent through the contact form.</p>
    </div>
</section>

<section class="section">
    <div class="container messages-container">
        <?php if ($error !== ''): ?>
            <div class="alert alert-error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if (!$isAuthenticated): ?>
            <div class="form-panel messages-login">
                <h2>Sign in to view messages</h2>
                <form action="messages.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                    <div class="form-field">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="current-password">
                    </div>
                    <button class="button" type="submit">View messages</button>
                </form>
            </div>
        <?php else: ?>
            <div class="messages-heading">
                <div>
                    <h2>Submissions</h2>
                    <span class="count-badge"><?= e((string) count($messages)) ?> <?= count($messages) === 1 ? 'message' : 'messages' ?></span>
                </div>
                <form action="messages.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                    <button class="button button-secondary" type="submit" name="logout" value="1">Sign out</button>
                </form>
            </div>

            <?php if ($readError !== ''): ?>
                <div class="alert alert-error" role="alert"><?= e($readError) ?></div>
            <?php elseif ($readWarnings > 0): ?>
                <div class="alert alert-error" role="alert">
                    <?= e((string) $readWarnings) ?> malformed entr<?= $readWarnings === 1 ? 'y was' : 'ies were' ?> skipped. See the server error log for details.
                </div>
            <?php endif; ?>

            <?php if ($messages === []): ?>
                <p class="empty-state">There are no contact submissions yet.</p>
            <?php elseif ($readError === ''): ?>
                <div class="table-scroll">
                    <table class="messages-table">
                        <thead>
                            <tr>
                                <th scope="col">Received</th>
                                <th scope="col">Name &amp; contact</th>
                                <th scope="col">Service</th>
                                <th scope="col">Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $message): ?>
                                <tr>
                                    <td><?= e($message['timestamp']) ?></td>
                                    <td>
                                        <strong><?= e($message['name']) ?></strong>
                                        <a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a>
                                        <?php if ($message['phone'] !== ''): ?>
                                            <span><?= e($message['phone']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= e($message['service']) ?></td>
                                    <td class="message-cell"><?= e($message['message']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
