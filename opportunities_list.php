
<?php
require_once 'csv_handler.php';
$schema = require __DIR__ . '/opportunity_schema.php';
$opportunities = readCSV('opportunities.csv', $schema);
$contacts = readCSV('contacts.csv', require __DIR__ . '/contact_schema.php');

// Simple ID generator function
function generateSimpleOpportunityId(array $opportunities): int {
    $ids = array_column($opportunities, 'id');
    $numericIds = array_map('intval', $ids);
    return empty($numericIds) ? 1 : max($numericIds) + 1;
}

// Build contact lookup map with fallback label
$contactMap = [];
foreach ($contacts as $contact) {
    $fullName = trim($contact['first_name'] . ' ' . $contact['last_name']);
    $contactMap[trim($contact['id'])] = $fullName ?: 'Unnamed Contact';
}

// Example: Adding a new opportunity (you can replace this with form handling logic)
$newOpportunity = [
    'id' => generateSimpleOpportunityId($opportunities),
    'name' => 'New Opportunity',
    'contact_id' => '123',
    // Add other fields as needed based on your schema
];

// Optionally add the new opportunity to the list and save
// $opportunities[] = $newOpportunity;
// writeCSV('opportunities.csv', $opportunities, $schema);
?>


<?php include_once(__DIR__ . '/layout_start.php'); ?>
<?php $currentPage = basename(__FILE__); include_once(__DIR__ . '/navbar.php'); ?>

<div class="container">
  <h2>Opportunities List</h2>

  <?php if (empty($opportunities)): ?>
    <p>No opportunities found.</p>
  <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <?php foreach ($schema as $field): ?>
            <th><?= ucwords(str_replace('_', ' ', $field)) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($opportunities as $opp): ?>
          <tr>
            <?php foreach ($schema as $field): ?>
              <td>
                <?php
                  if ($field === 'contact_id') {
                      $contactId = trim($opp[$field] ?? '');
                      echo isset($contactMap[$contactId])
                          ? $contactMap[$contactId]
                          : 'Unknown';
                  } else {
                      echo htmlspecialchars($opp[$field] ?? '');
                  }
                ?>
              </td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
