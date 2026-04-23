<?php
$pageTitle = 'Mass Email';
require_once __DIR__ . '/layout_start.php';
require_once __DIR__ . '/db_mysql.php';

$phpMailerSrc = __DIR__ . '/vendor/phpmailer/phpmailer/src/';
if (file_exists($phpMailerSrc . 'Exception.php')) {
  require_once $phpMailerSrc . 'Exception.php';
  require_once $phpMailerSrc . 'PHPMailer.php';
  require_once $phpMailerSrc . 'SMTP.php';
}

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$conn = get_mysql_connection();
$currentUser = auth_current_user();
$authorName = $currentUser['username'] ?? 'System';

$errors = [];
$successMessage = '';
$failedRecipients = [];

$subject = trim($_POST['subject'] ?? '');
$messageTemplate = trim($_POST['message'] ?? "Hi {{first_name}},\n\nI hope you are doing well.\n\nBest regards,");
$discussionTemplate = trim($_POST['discussion_message'] ?? 'Mass email sent. Subject: {{subject}}');
$fromName = trim($_POST['from_name'] ?? ($authorName ?: 'CRM Team'));
$fromEmail = trim($_POST['from_email'] ?? '');
$selectedIds = $_POST['contact_ids'] ?? [];

$smtpHost = trim((string) getenv('SMTP_HOST'));
$smtpPort = (int) (getenv('SMTP_PORT') ?: 587);
$smtpUsername = trim((string) getenv('SMTP_USERNAME'));
$smtpPassword = trim((string) getenv('SMTP_PASSWORD'));
$smtpEncryption = strtolower(trim((string) (getenv('SMTP_ENCRYPTION') ?: 'tls')));

if ($fromEmail === '' && $smtpUsername !== '' && filter_var($smtpUsername, FILTER_VALIDATE_EMAIL)) {
  $fromEmail = $smtpUsername;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token. Please refresh the page and try again.';
    }

    if ($subject === '') {
        $errors[] = 'Subject is required.';
    }

    if ($messageTemplate === '') {
        $errors[] = 'Message is required.';
    }

    if (!is_array($selectedIds) || count($selectedIds) === 0) {
        $errors[] = 'Select at least one contact.';
    }

    $selectedIds = array_values(array_filter(array_map('intval', (array) $selectedIds), function ($id) {
        return $id > 0;
    }));

    if (count($selectedIds) === 0) {
        $errors[] = 'No valid contact IDs were selected.';
    }

    if ($fromEmail !== '' && !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'From Email is not valid.';
    }

    if ($smtpHost === '' || $smtpUsername === '' || $smtpPassword === '') {
      $errors[] = 'SMTP is not configured. Set SMTP_HOST, SMTP_USERNAME, and SMTP_PASSWORD in .env.';
    }

    if (!class_exists(PHPMailer::class)) {
      $errors[] = 'PHPMailer is missing. Ensure vendor/phpmailer/phpmailer is present.';
    }

    if (!in_array($smtpEncryption, ['tls', 'ssl', 'starttls'], true)) {
      $errors[] = 'SMTP_ENCRYPTION must be tls, ssl, or starttls.';
    }

    if ($fromEmail === '') {
      $errors[] = 'From Email is required when using SMTP.';
    }

    if (empty($errors)) {
        $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
        $types = str_repeat('i', count($selectedIds));
        $sql = "SELECT contact_id, first_name, last_name, company, email FROM contacts WHERE contact_id IN ($placeholders)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $errors[] = 'Failed to prepare contact query.';
        } else {
            $stmt->bind_param($types, ...$selectedIds);
            $stmt->execute();
            $result = $stmt->get_result();

            $contacts = [];
            while ($row = $result->fetch_assoc()) {
                $contacts[] = $row;
            }
            $stmt->close();

            if (empty($contacts)) {
                $errors[] = 'No matching contacts found.';
            } else {
                $discussionSql = "INSERT INTO discussion_log (contact_id, author, timestamp, entry_text, linked_opportunity_id, visibility) VALUES (?, ?, NOW(), ?, '', 'public')";
                $discussionStmt = $conn->prepare($discussionSql);

                if (!$discussionStmt) {
                    $errors[] = 'Failed to prepare discussion log statement.';
                } else {
                    $sentCount = 0;
                  $mailer = new PHPMailer(true);

                  try {
                    $mailer->isSMTP();
                    $mailer->Host = $smtpHost;
                    $mailer->SMTPAuth = true;
                    $mailer->Username = $smtpUsername;
                    $mailer->Password = $smtpPassword;
                    $mailer->Port = $smtpPort;

                    if ($smtpEncryption === 'ssl') {
                      $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    } else {
                      $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    }

                    $mailer->CharSet = 'UTF-8';
                    $mailer->isHTML(false);
                    $mailer->setFrom($fromEmail, $fromName !== '' ? $fromName : 'CRM Team');
                  } catch (Exception $e) {
                    $errors[] = 'SMTP setup failed: ' . $e->getMessage();
                  }

                  if (!empty($errors)) {
                    $discussionStmt->close();
                  } else {

                    foreach ($contacts as $contact) {
                      $to = trim((string) ($contact['email'] ?? ''));
                      if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                        $failedRecipients[] = ($contact['company'] ?: 'Unknown company') . ' - missing/invalid email';
                        continue;
                      }

                      $tokenValues = [
                        '{{first_name}}' => (string) ($contact['first_name'] ?? ''),
                        '{{last_name}}' => (string) ($contact['last_name'] ?? ''),
                        '{{company}}' => (string) ($contact['company'] ?? ''),
                        '{{email}}' => $to,
                        '{{subject}}' => $subject,
                      ];

                      $body = strtr($messageTemplate, $tokenValues);
                      $discussionMessage = strtr($discussionTemplate, $tokenValues);

                      try {
                        $mailer->clearAddresses();
                        $mailer->Subject = $subject;
                        $mailer->Body = $body;
                        $mailer->addAddress($to);
                        $sent = $mailer->send();
                      } catch (Exception $e) {
                        $sent = false;
                      }

                      if (!$sent) {
                        $failedRecipients[] = ($contact['company'] ?: 'Unknown company') . ' - ' . $to;
                        continue;
                      }

                      $contactId = (string) $contact['contact_id'];
                      $discussionStmt->bind_param('sss', $contactId, $authorName, $discussionMessage);
                      $discussionStmt->execute();
                      $sentCount++;
                    }

                    $discussionStmt->close();
                    $successMessage = 'Mass email complete. Sent: ' . $sentCount . '. Failed: ' . count($failedRecipients) . '.';
                  }
                }
            }
        }
    }
}

$contactRows = [];
$listSql = "SELECT contact_id, first_name, last_name, company, email FROM contacts WHERE email IS NOT NULL AND TRIM(email) <> '' ORDER BY company ASC, last_name ASC, first_name ASC";
$listResult = $conn->query($listSql);
if ($listResult) {
    while ($row = $listResult->fetch_assoc()) {
        $contactRows[] = $row;
    }
    $listResult->free();
}

$conn->close();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 m-0">Mass Email</h1>
  <span class="badge bg-secondary">Contacts with email: <?= count($contactRows) ?></span>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if ($successMessage !== ''): ?>
  <div class="alert alert-success"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!empty($failedRecipients)): ?>
  <div class="alert alert-warning">
    <strong>Failed Recipients</strong>
    <ul class="mb-0 mt-2">
      <?php foreach ($failedRecipients as $failed): ?>
        <li><?= htmlspecialchars($failed, ENT_QUOTES, 'UTF-8') ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" action="mass_email.php">
  <?php renderCSRFInput(); ?>

  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-8">
          <label for="subject" class="form-label">Subject</label>
          <input type="text" id="subject" name="subject" class="form-control" required value="<?= htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col-md-4">
          <label for="from_name" class="form-label">From Name</label>
          <input type="text" id="from_name" name="from_name" class="form-control" value="<?= htmlspecialchars($fromName, ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col-md-6">
          <label for="from_email" class="form-label">From Email (optional)</label>
          <input type="email" id="from_email" name="from_email" class="form-control" value="<?= htmlspecialchars($fromEmail, ENT_QUOTES, 'UTF-8') ?>">
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <label for="message" class="form-label">Email Message</label>
      <textarea id="message" name="message" class="form-control" rows="10" required><?= htmlspecialchars($messageTemplate, ENT_QUOTES, 'UTF-8') ?></textarea>
      <div class="form-text mt-2">Tokens supported: {{first_name}}, {{last_name}}, {{company}}, {{email}}, {{subject}}</div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <label for="discussion_message" class="form-label">Discussion Log Message</label>
      <textarea id="discussion_message" name="discussion_message" class="form-control" rows="3" required><?= htmlspecialchars($discussionTemplate, ENT_QUOTES, 'UTF-8') ?></textarea>
      <div class="form-text mt-2">This message is added to each contact in discussion log after a successful send.</div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Select Recipients</span>
      <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleAllContacts">Select All</button>
    </div>
    <div class="card-body" style="max-height: 360px; overflow: auto;">
      <?php if (empty($contactRows)): ?>
        <p class="text-muted mb-0">No contacts with email addresses found.</p>
      <?php else: ?>
        <?php foreach ($contactRows as $contact): ?>
          <?php
            $cid = (int) ($contact['contact_id'] ?? 0);
            $label = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
            if ($label === '') {
                $label = $contact['company'] ?: 'Unnamed Contact';
            }
            $isChecked = in_array($cid, $selectedIds, true);
          ?>
          <div class="form-check mb-2">
            <input class="form-check-input recipient-checkbox" type="checkbox" name="contact_ids[]" value="<?= $cid ?>" id="contact_<?= $cid ?>" <?= $isChecked ? 'checked' : '' ?>>
            <label class="form-check-label" for="contact_<?= $cid ?>">
              <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
              <span class="text-muted">(<?= htmlspecialchars($contact['company'] ?? '', ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($contact['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</span>
            </label>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Send Mass Email</button>
</form>

<script>
(function () {
  const toggleBtn = document.getElementById('toggleAllContacts');
  const checkboxes = document.querySelectorAll('.recipient-checkbox');

  if (!toggleBtn || checkboxes.length === 0) {
    return;
  }

  toggleBtn.addEventListener('click', function () {
    const allChecked = Array.from(checkboxes).every(function (cb) { return cb.checked; });
    checkboxes.forEach(function (cb) { cb.checked = !allChecked; });
    toggleBtn.textContent = allChecked ? 'Select All' : 'Unselect All';
  });
})();
</script>

<?php require_once __DIR__ . '/layout_end.php'; ?>
