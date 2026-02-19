<?php
// Security headers
header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Page metadata
$pageTitle = 'Contact Details';

// Include layout and dependencies
require_once('layout_start.php');
require_once 'db_mysql.php';

// DEBUG: Show session and request state for troubleshooting (after headers and includes)

// Load schemas and data

// Load schema
$schema = require 'contact_schema.php';
$contactId = $_GET['id'] ?? '';
$contact = null;
$conn = get_mysql_connection();
if ($contactId) {
  $fields = implode(',', array_map(function($f) { return '`' . $f . '`'; }, $schema));
  $safeId = mysqli_real_escape_string($conn, $contactId);
  $result = mysqli_query($conn, "SELECT $fields FROM contacts WHERE id = '$safeId'");
  if ($result && ($row = mysqli_fetch_assoc($result))) {
    $contact = $row;
  }
  if ($result) {
    mysqli_free_result($result);
  }
}

// Handle POST requests for updating contact
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = mysqli_real_escape_string($conn, $_POST['id']);
  $fields = [];
  foreach ($schema as $f) {
    $fields[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : null;
  }
  // Special handling for is_customer
  $fields['is_customer'] = isset($_POST['is_customer']) ? 'yes' : 'no';
  $fields['last_modified'] = date('Y-m-d H:i:s');
  $setClause = [];
  foreach ($fields as $k => $v) {
    $setClause[] = '"' . $k . '" = ' . ($v !== null ? "'" . pg_escape_string($v) . "'" : 'NULL');
  }
  $sql = "UPDATE contacts SET " . implode(', ', $setClause) . " WHERE id = '" . $id . "'";
  $updateResult = pg_query($conn, $sql);
  if ($updateResult) {
    $saveSuccess = true;
    // Reload contact
    $result = pg_query($conn, "SELECT $fields FROM contacts WHERE id = '" . $id . "'");
    if ($result && ($row = pg_fetch_assoc($result))) {
      $contact = $row;
    }
    pg_free_result($result);
  } else {
    $saveSuccess = false;
  }
}

// Safety: verify contact was loaded
if (!$contact) {
  // Try to fetch the record even if fields are missing
  $result = pg_query($conn, "SELECT * FROM contacts WHERE id = '" . pg_escape_string($contactId) . "'");
  if ($result && ($row = pg_fetch_assoc($result))) {
    $contact = $row;
    echo '<div style="background:#fffbe6;border:2px solid #ffc;padding:10px;margin:10px 0;">';
    echo '<strong>Warning:</strong> This contact record is incomplete. Some fields may be missing or blank.';
    echo '</div>';
    pg_free_result($result);
  } else {
    // Diagnostic: Show all available contact IDs
    $result = pg_query($conn, "SELECT id, first_name, last_name FROM contacts ORDER BY id LIMIT 20");
    echo '<div style="background:#fee;border:2px solid #c33;padding:10px;margin:10px 0;">';
    echo '<strong>Error: Contact not found.</strong><br>';
    echo 'Available Contact IDs:<br><ul>';
    while ($row = pg_fetch_assoc($result)) {
      echo '<li>ID: ' . htmlspecialchars($row['id']) . ' - ' . htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) . '</li>';
    }
    echo '</ul>';
    echo 'Try <code>contact_view.php?id=&lt;ID&gt;</code> with one of the above.';
    echo '</div>';
    pg_free_result($result);
    exit;
  }
}

// Load opportunities from PostgreSQL

$opportunities = [];
$opportunitySchema = ['id', 'contact_id', 'value', 'stage', 'probability', 'expected_close'];
$fields = implode(',', array_map(function($f) { return '`' . $f . '`'; }, $opportunitySchema));
$safeContactId = mysqli_real_escape_string($conn, $contactId);
$result = mysqli_query($conn, "SELECT $fields FROM opportunities WHERE contact_id = '$safeContactId'");
if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $opportunities[] = $row;
  }
  mysqli_free_result($result);
}


// NOTE: If a discussion_log entry's contact_id does not exist in the contacts table,
// those discussions will not appear for any contact. Example missing IDs:
//   - 690507e9c30e8
//   - CNT_20251003023317_ecf31f
// To display these, add matching contacts or map them to existing contacts.
// Load discussions from PostgreSQL

$discussions = [];
$discussionSchema = require 'discussion_schema.php';
$fields = implode(',', array_map(function($f) { return '`' . $f . '`'; }, $discussionSchema));
$result = mysqli_query($conn, "SELECT $fields FROM discussion_log WHERE contact_id = '$safeContactId'");
if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $discussions[] = $row;
  }
  mysqli_free_result($result);
}
if (!is_array($discussions)) $discussions = [];
$contactDiscussions = $discussions;

// Helper: resolve customer by ID
function findCustomerById($customers, $cid) {
    foreach ($customers as $c) {
        if ($c['customer_id'] === $cid) return $c;
    }
    return null;
}

// Helper: Get initials for avatar
function getInitials($firstName, $lastName) {
    $f = strtoupper(substr($firstName ?? '', 0, 1));
    $l = strtoupper(substr($lastName ?? '', 0, 1));
    return $f . $l;
}

// Helper: Parse tags from CSV
function parseTags($tagString) {
    if (empty($tagString)) return [];
    return array_filter(array_map('trim', explode(',', $tagString)));
}

// Helper: Get contact's opportunities
function getContactOpportunities($contact, $opportunities) {
    if (!is_array($opportunities)) return [];
    return array_filter($opportunities, function($opp) use ($contact) {
      return ($opp['contact_id'] ?? '') === ($contact['id'] ?? '');
    });
if (!is_array($contactOpportunities)) $contactOpportunities = [];
}

// ...existing code...
?>

<style>
  * { box-sizing: border-box; }
  
  /* Override content-container padding for this page */
  .content-container { padding: 0 !important; }
  
  .contact-header { max-width: 100% !important; margin: 0 !important; padding: 0 32px !important; width: 100% !important; }
  .contact-banner { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 18px; border-radius: 8px; display: flex; align-items: start; gap: 14px; margin-bottom: 16px; flex-wrap: wrap; }
  .contact-avatar { width: 50px; height: 50px; border-radius: 50%; background: <?= $avatarColor ?>; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; flex-shrink: 0; border: 2px solid white; }
  .contact-header-info { flex: 1; min-width: 180px; }
  .contact-header-info h1 { margin: 0 0 8px 0; font-size: 20px; font-weight: 600; }
  .contact-header-info p { margin: 4px 0; font-size: 11px; opacity: 0.9; }
  .contact-header-info a { color: white; text-decoration: none; }
  .contact-status { display: inline-block; padding: 4px 10px; background: rgba(255,255,255,0.2); border-radius: 20px; font-size: 11px; font-weight: 600; margin-top: 8px; }
  .quick-actions { display: flex; gap: 6px; margin-top: 10px; flex-wrap: wrap; }
  .quick-actions button { background: white; color: #333; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: 600; transition: background 0.2s; }
  .quick-actions button:hover { background: #f0f0f0; }
  .quick-actions button:active { transform: scale(0.98); }
  .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 24px; }
  .stat-card { background: white; padding: 8px; border-radius: 6px; border-left: 4px solid #3B82F6; font-size: 11px; }
  .stat-label { color: #999; text-transform: uppercase; font-weight: 600; font-size: 10px; }
  .stat-value { font-size: 15px; font-weight: 600; color: #1a1a1a; margin-top: 4px; }
  
  /* Accordion Styles */
  .accordion { margin-bottom: 16px; }
  .accordion-header { background: white; padding: 18px 24px; border-radius: 8px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; border: 2px solid #e5e7eb; transition: all 0.3s; user-select: none; }
  .accordion-header:hover { border-color: #3B82F6; background: #f9fafb; }
  .accordion-header.active { border-color: #3B82F6; background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%); }
  .accordion-title { font-size: 16px; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 12px; }
  .accordion-icon { font-size: 18px; color: #6b7280; transition: transform 0.3s; }
  .accordion-header.active .accordion-icon { transform: rotate(90deg); color: #3B82F6; }
  .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; }
  .accordion-content.active { max-height: 5000px; transition: max-height 0.6s ease-in; }
  .accordion-body { background: white; padding: 24px; border: 2px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px; margin-top: -8px; }
  .section { margin-bottom: 25px; }
  .section:last-child { margin-bottom: 0; }
  .section-title { font-size: 12px; font-weight: 700; color: #1a1a1a; text-transform: uppercase; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #e0e0e0; }
  .field-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
  .field { }
  .field-label { font-size: 11px; color: #999; text-transform: uppercase; font-weight: 600; margin-bottom: 3px; }
  .field-value { font-size: 13px; color: #1a1a1a; word-break: break-word; line-height: 1.4; }
  .field-value.empty { color: #ccc; font-style: italic; }
  .opportunity { background: #f8f9fa; padding: 12px; border-radius: 4px; margin-bottom: 10px; border-left: 3px solid #10B981; }
  .opportunity-title { font-weight: 600; color: #1a1a1a; font-size: 13px; }
  .opportunity-details { font-size: 12px; color: #666; margin-top: 4px; }
  .opportunity-value { font-weight: 600; color: #28a745; font-size: 14px; margin-top: 6px; }
  .timeline-item { padding: 12px 0; border-bottom: 1px solid #e0e0e0; display: flex; gap: 12px; }
  .timeline-item:last-child { border-bottom: none; }
  .timeline-icon { font-size: 16px; flex-shrink: 0; width: 20px; text-align: center; }
  .timeline-content { flex: 1; }
  .timeline-title { font-weight: 600; color: #1a1a1a; font-size: 13px; }
  .timeline-time { font-size: 11px; color: #999; margin-top: 2px; }
  .tags-container { display: flex; flex-wrap: wrap; gap: 6px; }
  .tag { background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
  .tag-new { background: #f0f0f0; color: #666; border: 1px dashed #999; padding: 4px 8px; border-radius: 12px; font-size: 11px; cursor: pointer; }
  .tag-remove { cursor: pointer; }
  .form-section { width: 100%; margin-bottom: 28px; background: #f9fafb; padding: 28px; border-radius: 10px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
  .form-section h3 { font-size: 15px; font-weight: 700; margin: -28px -28px 20px -28px; padding: 16px 28px; background: linear-gradient(135deg, #f3f4f6 0%, #ffffff 100%); border-bottom: 2px solid #e5e7eb; border-radius: 8px 8px 0 0; color: #1f2937; }
  .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; width: 100%; }
  .form-group { display: flex; flex-direction: column; }
  .form-group label { display: block; font-size: 12px; font-weight: 700; margin-bottom: 12px; color: #111827; text-transform: uppercase; letter-spacing: 0.6px; }
  .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 18px 16px; border: 1.5px solid #d1d5db; border-radius: 8px; font-family: inherit; font-size: 15px; transition: all 0.2s; background: white; line-height: 1.6; min-height: 60px; box-sizing: border-box; text-align: left; }
  .form-group input::placeholder, .form-group textarea::placeholder { color: #9ca3af; }
  .form-group textarea { resize: none; min-height: 150px; padding: 20px; overflow-y: auto; }
  .form-group input:hover, .form-group textarea:hover, .form-group select:hover { border-color: #9ca3af; }
  .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: #3B82F6; background: #eff6ff; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
  .form-group input:disabled, .form-group textarea:disabled, .form-group select:disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }
  .activity-item { padding: 10px 0; border-bottom: 1px solid #e0e0e0; font-size: 12px; }
  .activity-item:last-child { border-bottom: none; }
  .activity-time { color: #999; font-size: 10px; }
  .activity-text { color: #333; margin-top: 3px; }
  .submit-actions { display: flex; gap: 12px; margin-top: 28px; flex-wrap: wrap; }
  .submit-actions button, .submit-actions a { padding: 14px 32px; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px; text-decoration: none; display: inline-block; transition: all 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
  .btn-primary { background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white; }
  .btn-primary:hover { background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); transform: translateY(-2px); }
  .btn-primary:active { transform: translateY(0); }
  .btn-secondary { background: #f3f4f6; color: #374151; border: 1.5px solid #d1d5db; }
  .btn-secondary:hover { background: #e5e7eb; }
  .btn-secondary:active { background: #d1d5db; }
  .success-alert { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px; }

  /* Mobile Responsiveness */
  @media (max-width: 900px) {
    .contact-banner { flex-direction: column; }
    .contact-avatar { width: 60px; height: 60px; font-size: 20px; }
    .contact-banner h1 { font-size: 22px; }
    .field-grid { grid-template-columns: 1fr; }
    .form-grid { grid-template-columns: repeat(2, 1fr); }
    .stats-bar { grid-template-columns: repeat(2, 1fr); }
    .quick-actions { gap: 6px; }
    .quick-actions button { padding: 5px 10px; font-size: 11px; }
    .accordion-header { padding: 14px 18px; }
    .accordion-title { font-size: 14px; }
  }

  @media (max-width: 600px) {
    .contact-header { padding: 0 16px !important; }
    .contact-banner { padding: 15px; gap: 12px; }
    .contact-avatar { width: 50px; height: 50px; font-size: 18px; }
    .contact-banner h1 { font-size: 18px; }
    .stats-bar { grid-template-columns: 1fr; gap: 8px; }
    .stat-card { padding: 10px; }
    .form-grid { grid-template-columns: 1fr; }
    .accordion-header { padding: 12px 16px; }
    .accordion-title { font-size: 13px; }
    .accordion-body { padding: 16px; }
    .form-section { padding: 20px; }
    .submit-actions { gap: 6px; }
    .submit-actions button, .submit-actions a { padding: 8px 12px; font-size: 12px; }
  }
  /* Auto-expand textareas */
  .auto-expand-textarea {
    overflow: hidden;
    resize: none;
  }
</style>

<div class="contact-header">
  <div style="margin-bottom: 15px; font-size: 13px;">
    <a href="contacts_list.php" style="color: #3B82F6; text-decoration: none; font-weight: 600;">← Back to Contacts</a>
  </div>

  <!-- Contact Banner -->
  <div class="contact-banner">
    <div class="contact-avatar"><?= $initials ?></div>
    <div class="contact-header-info">
      <h1><?= htmlspecialchars(trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''))) ?: 'Unknown Contact' ?></h1>
      <p><?= htmlspecialchars($contact['company'] ?? 'Company not assigned') ?></p>
      <?php if ($contact['phone']): ?>
        <p>☎ <?= htmlspecialchars($contact['phone']) ?></p>
      <?php endif; ?>
      <?php if ($contact['email']): ?>
        <p><a href="mailto:<?= htmlspecialchars($contact['email']) ?>">✉ <?= htmlspecialchars($contact['email']) ?></a></p>
      <?php endif; ?>
      <span class="contact-status" style="background-color: <?= $statusColor ?>;">✓ <?= $status ?></span>
      <div class="quick-actions">
        <button onclick="alert('Email: <?= htmlspecialchars($contact['email'] ?? 'no email') ?>')">✉ Email</button>
        <button onclick="alert('Call: <?= htmlspecialchars($contact['phone'] ?? 'no phone') ?>')">☎ Call</button>
        <button onclick="alert('Add task for <?= htmlspecialchars($contact['first_name'] ?? 'Contact') ?>')">+ Task</button>
        <button onclick="alert('Create opportunity for <?= htmlspecialchars($contact['company'] ?? 'this contact') ?>')">💼 Opp</button>
      </div>
    </div>
  </div>

  <?php if ($saveSuccess): ?>
    <div class="success-alert">✓ Changes saved successfully.</div>
  <?php endif; ?>

  <!-- Stats Bar -->
  <div class="stats-bar">
    <div class="stat-card">
      <div class="stat-label">Status</div>
      <div class="stat-value"><?= $status ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Created</div>
      <div class="stat-value"><?= substr($createdAt, 0, 10) ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Opportunities</div>
      <div class="stat-value"><?= is_array($contactOpportunities) ? count($contactOpportunities) : 0 ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Value</div>
      <div class="stat-value"><?= $opportunityValue > 0 ? formatCurrency($opportunityValue) : '—' ?></div>
    </div>
  </div>

  <!-- ACCORDION SECTIONS -->
  
  <!-- Overview Section -->
  <div class="accordion">
    <div class="accordion-header active" onclick="toggleAccordion(this)">
      <div class="accordion-title">
        <span>📋</span>
        <span>Overview</span>
      </div>
      <div class="accordion-icon">▶</div>
    </div>
    <div class="accordion-content active">
      <div class="accordion-body">
        <div class="section">
          <div class="section-title">📍 Location & Contact</div>
          <div class="field-grid">
            <div class="field">
              <div class="field-label">Email</div>
              <div class="field-value <?= empty($contact['email']) ? 'empty' : '' ?>">
                <?= $contact['email'] ? '<a href="mailto:' . htmlspecialchars($contact['email']) . '" style="color:#3B82F6;">' . htmlspecialchars($contact['email']) . '</a>' : '—' ?>
              </div>
            </div>
            <div class="field">
              <div class="field-label">Phone</div>
              <div class="field-value <?= empty($contact['phone']) ? 'empty' : '' ?>">
                <?= $contact['phone'] ? '<a href="tel:' . htmlspecialchars($contact['phone']) . '" style="color:#3B82F6;">' . htmlspecialchars($contact['phone']) . '</a>' : '—' ?>
              </div>
            </div>
            <div class="field">
              <div class="field-label">City</div>
              <div class="field-value <?= empty($contact['city']) ? 'empty' : '' ?>"><?= htmlspecialchars($contact['city'] ?? '—') ?></div>
            </div>
            <div class="field">
              <div class="field-label">Province</div>
              <div class="field-value <?= empty($contact['province']) ? 'empty' : '' ?>"><?= htmlspecialchars($contact['province'] ?? '—') ?></div>
            </div>
            <div class="field">
              <div class="field-label">Postal Code</div>
              <div class="field-value <?= empty($contact['postal_code']) ? 'empty' : '' ?>"><?= htmlspecialchars($contact['postal_code'] ?? '—') ?></div>
            </div>
            <div class="field">
              <div class="field-label">Country</div>
              <div class="field-value <?= empty($contact['country']) ? 'empty' : '' ?>"><?= htmlspecialchars($contact['country'] ?? '—') ?></div>
            </div>
          </div>
        </div>

        <div class="section">
          <div class="section-title">🔖 Tags</div>
          <div class="tags-container">
            <?php foreach ($tags as $tag): ?>
              <span class="tag"><?= htmlspecialchars($tag) ?></span>
            <?php endforeach; ?>
            <?php if (empty($tags)): ?>
              <p style="font-size: 12px; color: #999; margin: 0;">No tags</p>
            <?php endif; ?>
          </div>
        </div>

        <div class="section">
          <div class="section-title">📊 Quick Stats</div>
          <div class="field-grid">
            <div class="field">
              <div class="field-label">Total Value</div>
              <div class="field-value" style="color: #28a745; font-weight: 600; font-size: 16px;">
                <?= formatCurrency($opportunityValue) ?>
              </div>
            </div>
            <div class="field">
              <div class="field-label">Open Opportunities</div>
              <div class="field-value" style="color: #3B82F6; font-weight: 600; font-size: 16px;">
                <?= is_array($contactOpportunities) ? count($contactOpportunities) : 0 ?>
              </div>
            </div>
            <div class="field">
              <div class="field-label">Discussions</div>
              <div class="field-value" style="color: #8B5CF6; font-weight: 600; font-size: 16px;">
                <?= is_array($contactDiscussions) ? count($contactDiscussions) : 0 ?>
              </div>
            </div>
            <div class="field">
              <div class="field-label">Member Since</div>
              <div class="field-value" style="color: #666; font-size: 13px;">
                <?= date('M d, Y', strtotime($createdAt)) ?>
              </div>
            </div>
          </div>
        </div>

        <div class="section">
          <div class="section-title">📝 Notes</div>
          <div style="background: #f8f9fa; padding: 10px; border-radius: 4px; font-size: 13px; line-height: 1.5;">
            <?= $contact['notes'] ? htmlspecialchars($contact['notes']) : '<span style="color: #ccc;">No notes</span>' ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Details Section -->
  <div class="accordion">
    <div class="accordion-header" onclick="toggleAccordion(this)">
      <div class="accordion-title">
        <span>✏️</span>
        <span>Edit Contact Details</span>
      </div>
      <div class="accordion-icon">▶</div>
    </div>
    <div class="accordion-content">
      <div class="accordion-body">
        <form method="post">
          <input type="hidden" name="id" value="<?= htmlspecialchars($contact['id']) ?>">

          <!-- Quick Status Section -->
          <div class="form-section" style="background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%); border: 1px solid #bfdbfe; padding: 14px; margin-bottom: 16px;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; font-size: 12px;">
              <div>
                <div style="font-weight: 700; color: #1e40af; margin-bottom: 4px;">📅 Created</div>
                <div style="color: #666;"><?= date('M d, Y', strtotime($createdAt)) ?></div>
              </div>
              <div>
                <div style="font-weight: 700; color: #1e40af; margin-bottom: 4px;">🔄 Last Modified</div>
                <div style="color: #666;"><?= $lastModified ? date('M d, Y', strtotime($lastModified)) : '—' ?></div>
              </div>
              <div>
                <div style="font-weight: 700; color: #1e40af; margin-bottom: 4px;">💼 Opportunities</div>
                <div style="color: #666;"><?= is_array($contactOpportunities) ? count($contactOpportunities) : 0 ?> active</div>
              </div>
              <div>
                <div style="font-weight: 700; color: #1e40af; margin-bottom: 4px;">💬 Discussions</div>
                <div style="color: #666;"><?= is_array($contactDiscussions) ? count($contactDiscussions) : 0 ?> logged</div>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3>👤 Personal Information</h3>
            <div class="form-grid">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($contact['first_name'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($contact['last_name'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($contact['email'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>">
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3>🏢 Company Information</h3>
            <div class="form-grid">
              <div class="form-group" style="grid-column: 1 / -1;">
                <label>Company</label>
                <input type="text" name="company" value="<?= htmlspecialchars($contact['company'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Tank Number</label>
                <input type="text" name="tank_number" value="<?= htmlspecialchars($contact['tank_number'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Delivery Date</label>
                <input type="text" name="delivery_date" placeholder="YYYY-MM-DD" value="<?= htmlspecialchars($contact['delivery_date'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Status</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                  <option value=""></option>
                  <option value="Active" <?= ($contact['status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option>
                  <option value="Inactive" <?= ($contact['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                  <option value="Prospect" <?= ($contact['status'] ?? '') === 'Prospect' ? 'selected' : '' ?>>Prospect</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3>📍 Address</h3>
            <div class="form-group" style="margin-bottom: 12px;">
              <label>Address</label>
              <input type="text" name="address" value="<?= htmlspecialchars($contact['address'] ?? '') ?>">
            </div>
            <div class="form-grid">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="city" value="<?= htmlspecialchars($contact['city'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Province/State</label>
                <input type="text" name="province" value="<?= htmlspecialchars($contact['province'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Postal Code</label>
                <input type="text" name="postal_code" value="<?= htmlspecialchars($contact['postal_code'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" value="<?= htmlspecialchars($contact['country'] ?? '') ?>">
              </div>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; width: 100%;">
            <div class="form-section">
              <h3>🔖 Tags</h3>
              <div class="tags-container" style="margin-bottom: 10px;">
                <?php foreach ($tags as $tag): ?>
                  <span class="tag"><?= htmlspecialchars($tag) ?> <span class="tag-remove" onclick="removeTag(this)">✕</span></span>
                <?php endforeach; ?>
                <span class="tag-new" onclick="addNewTag(this)">+ Add tag</span>
              </div>
              <input type="hidden" name="tags" id="tags_input" value="<?= htmlspecialchars($contact['tags'] ?? '') ?>">
            </div>

            <div class="form-section">
              <h3>⭐ Customer Status</h3>
              <div style="padding: 10px 0;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                  <input type="checkbox" name="is_customer" value="1" <?= ($isCustomer ? 'checked' : '') ?> style="width: 16px; height: 16px; cursor: pointer;">
                  <span>Is an Active Customer</span>
                </label>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3>📝 Notes</h3>
            <div class="form-group">
              <label>Additional Notes</label>
              <textarea name="notes" rows="5"><?= htmlspecialchars($contact['notes'] ?? '') ?></textarea>
            </div>
          </div>

          <div class="submit-actions">
            <button type="submit" class="btn-primary">💾 Save Changes</button>
            <a href="contacts_list.php" class="btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Opportunities Section -->
  <div class="accordion">
    <div class="accordion-header" onclick="toggleAccordion(this)">
      <div class="accordion-title">
        <span>💼</span>
        <span>Opportunities (<?= is_array($contactOpportunities) ? count($contactOpportunities) : 0 ?>)</span>
      </div>
      <div class="accordion-icon">▶</div>
    </div>
    <div class="accordion-content">
      <div class="accordion-body">
        <div class="section">
          <div class="section-title">💼 Linked Opportunities</div>
          <?php if (!empty($contactOpportunities)): ?>
            <?php foreach ($contactOpportunities as $opp): ?>
              <div class="opportunity">
                <div class="opportunity-title">Opportunity #<?= htmlspecialchars($opp['id']) ?></div>
                <div class="opportunity-details">
                  <strong>Stage:</strong> <?= htmlspecialchars($opp['stage'] ?? '—') ?> 
                  (<?= htmlspecialchars($opp['probability'] ?? '0') ?>%)
                </div>
                <div class="opportunity-details" style="margin-top: 4px;">
                  <strong>Expected Close:</strong> <?= htmlspecialchars($opp['expected_close'] ?? '—') ?>
                </div>
                <div class="opportunity-value">
                  <?= formatCurrency($opp['value'] ?? 0) ?>
                </div>
              </div>
            <?php endforeach; ?>
            <div style="padding: 10px; background: #f0f7ff; border-radius: 4px; margin-top: 12px; font-size: 12px; color: #666;">
              <strong style="color: #1976d2;">Total Opportunity Value:</strong> <?= formatCurrency($opportunityValue) ?>
            </div>
          <?php else: ?>
            <div style="padding: 15px; background: #f8f9fa; border-radius: 4px; color: #999; text-align: center; font-size: 13px;">
              No opportunities linked to this contact yet.
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Discussions Section -->
  <div class="accordion">
    <div class="accordion-header" onclick="toggleAccordion(this)">
      <div class="accordion-title">
        <span>💬</span>
        <span>Discussions (<?= is_array($contactDiscussions) ? count($contactDiscussions) : 0 ?>)</span>
      </div>
      <div class="accordion-icon">▶</div>
    </div>
    <div class="accordion-content">
      <div class="accordion-body">
        <!-- Add Discussion Form -->
        <div class="section">
          <div class="section-title">➕ Log Discussion</div>
          <form method="post" action="discussion_logger.php" style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
            <input type="hidden" name="contact_id" value="<?= htmlspecialchars($contact['id']) ?>">
            
            <div class="form-group" style="margin-bottom: 12px;">
              <label>Author</label>
              <input type="text" name="author" placeholder="Your name" value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '' ?>" required style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
              <label>Discussion Notes</label>
              <textarea name="entry_text" placeholder="What was discussed?" required style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; font-family: inherit; font-size: 13px;" rows="4"></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
              <label>Visibility</label>
              <select name="visibility" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; font-family: inherit; font-size: 13px;">
                <option value="public">Public (Visible to all)</option>
                <option value="internal">Internal (Team only)</option>
                <option value="private">Private (Only me)</option>
              </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label>Linked Opportunity (Optional)</label>
              <select name="linked_opportunity_id" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; font-family: inherit; font-size: 13px;">
                <option value="">— No opportunity</option>
                <?php foreach ($contactOpportunities as $opp): ?>
                  <option value="<?= htmlspecialchars($opp['id']) ?>">Opportunity #<?= htmlspecialchars($opp['id']) ?> (<?= htmlspecialchars($opp['stage']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>

            <button type="submit" style="margin-top: 12px; background: #3B82F6; color: white; border: none; padding: 10px 18px; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">💬 Log Discussion</button>
          </form>
        </div>

        <!-- Discussion History -->
        <div class="section">
          <div class="section-title">📝 Discussion History</div>
          <?php if (!empty($contactDiscussions)): ?>
            <?php foreach ($contactDiscussions as $disc): ?>
              <div class="timeline-item" style="padding: 15px; margin-bottom: 12px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #3B82F6;">
                <div style="width: 100%;">
                  <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 6px;">
                    <strong style="color: #1a1a1a; font-size: 14px;"><?= htmlspecialchars($disc['author'] ?? 'Unknown') ?></strong>
                    <span style="background: <?= ($disc['visibility'] ?? 'public') === 'public' ? '#e3f2fd' : (($disc['visibility'] ?? '') === 'internal' ? '#fff3cd' : '#f8d7da') ?>; color: <?= ($disc['visibility'] ?? 'public') === 'public' ? '#1976d2' : (($disc['visibility'] ?? '') === 'internal' ? '#856404' : '#721c24') ?>; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; text-transform: uppercase;">
                      <?= htmlspecialchars($disc['visibility'] ?? 'public') ?>
                    </span>
                  </div>
                  <div style="color: #666; font-size: 11px; margin-bottom: 8px;">
                    📅 <?= htmlspecialchars($disc['timestamp'] ?? '—') ?>
                  </div>
                  <div style="color: #1a1a1a; font-size: 13px; line-height: 1.5; margin-bottom: 6px;">
                    <?= nl2br(htmlspecialchars($disc['entry_text'] ?? '')) ?>
                  </div>
                  <?php if (!empty($disc['linked_opportunity_id'])): ?>
                    <div style="margin-top: 8px; padding: 8px; background: white; border-left: 3px solid #10B981; border-radius: 3px; font-size: 11px; color: #666;">
                      <strong>📎 Linked to Opportunity #<?= htmlspecialchars($disc['linked_opportunity_id']) ?></strong>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div style="padding: 20px; background: #f8f9fa; border-radius: 4px; color: #999; text-align: center; font-size: 13px;">
              No discussions logged yet. Start by adding the first discussion above.
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
</div>

<script>
  // Accordion toggle function
  function toggleAccordion(header) {
    const content = header.nextElementSibling;
    
    // Toggle current accordion
    header.classList.toggle('active');
    content.classList.toggle('active');
  }

  // Tag management
  function addNewTag(element) {
    const tagName = prompt('Enter tag name:');
    if (tagName && tagName.trim()) {
      const tagsInput = document.getElementById('tags_input');
      const currentTags = tagsInput.value ? tagsInput.value.split(',').map(t => t.trim()) : [];
      currentTags.push(tagName.trim());
      tagsInput.value = currentTags.join(',');
      location.reload();
    }
  }

  function removeTag(element) {
    const tagsInput = document.getElementById('tags_input');
    const tagName = element.previousSibling.textContent.trim();
    const currentTags = tagsInput.value.split(',').map(t => t.trim());
    const filtered = currentTags.filter(t => t !== tagName);
    tagsInput.value = filtered.join(',');
    location.reload();
  }

  // Auto-expand textareas based on content
  const textareas = document.querySelectorAll('.form-group textarea');
  textareas.forEach(textarea => {
    function adjustHeight() {
      textarea.style.height = 'auto';
      textarea.style.height = Math.max(150, textarea.scrollHeight) + 'px';
    }
    
    textarea.addEventListener('input', adjustHeight);
    textarea.addEventListener('change', adjustHeight);
    adjustHeight(); // Initial adjustment
  });

  // Auto-adjust input widths based on content
  const inputs = document.querySelectorAll('.form-group input[type="text"], .form-group input[type="email"], .form-group input[type="tel"], .form-group input[type="number"]');
  inputs.forEach(input => {
    function adjustWidth() {
      const length = input.value.length;
      const charWidth = 9;
      const minWidth = 280;
      input.style.minWidth = Math.max(minWidth, length * charWidth + 60) + 'px';
    }
    
    input.addEventListener('input', adjustWidth);
    input.addEventListener('change', adjustWidth);
    adjustWidth(); // Initial adjustment
  });
</script>

<?php include_once('layout_end.php'); ?>
