<?php
include_once 'navbar.php';
include_once 'layout_start.php';

// Get customer ID from GET
$id = isset($_GET['id']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['id']) : null;

if (!$id) {
    echo "<p>Error: No customer ID specified.</p>";
    include_once 'layout_end.php';
    exit;
}

$csvFile = "deliveries_$id.csv";

// Create file if it doesn't exist
if (!file_exists($csvFile)) {
    $fp = fopen($csvFile, 'w');
    fputcsv($fp, ['Date', 'Company', 'Tank #', 'Size']); // Header row
    fclose($fp);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['delivery_date'];
    $company = $_POST['company'];
    $tank_number = $_POST['tank_number'];
    $tank_size = $_POST['tank_size'];

    $newEntry = [$date, $company, $tank_number, $tank_size];
    $fp = fopen($csvFile, 'a');
    fputcsv($fp, $newEntry);
    fclose($fp);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delivery Archive - Customer #<?php echo htmlspecialchars($id); ?></title>
  <script>
    function sortTable(n) {
      const table = document.getElementById("deliveryTable");
      let switching = true, dir = "asc", switchcount = 0;
      while (switching) {
        switching = false;
        const rows = table.rows;
        for (let i = 1; i < rows.length - 1; i++) {
          let x = rows[i].getElementsByTagName("TD")[n];
          let y = rows[i + 1].getElementsByTagName("TD")[n];
          let shouldSwitch = dir === "asc" ? x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase() : x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase();
          if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
            break;
          }
        }
        if (switchcount === 0 && dir === "asc") {
          dir = "desc";
          switching = true;
        }
      }
    }
  </script>
</head>
<body>
  <h2>📦 Delivery Archive for Customer #<?php echo htmlspecialchars($id); ?></h2>

  <form method="post">
    <label><strong>Add New Delivery:</strong></label><br>
    <input type="date" name="delivery_date" required>
    <input type="text" name="company" required>
    <input type="text" name="tank_number" placeholder="Tank #" required>
    <input type="text" name="tank_size" placeholder="Size" required>
	<input type="text" name="number" placeholder="Number of Tanks" required>
    <button type="submit">➕ Add Delivery</button>
  </form>

  <br>
  customer_view.php?id=<?php echo urlencode($id); ?>
    <button>🔙 Back to Customer Details</button>
  </a>

  <br><br>
  <table id="deliveryTable" border="1">
    <thead>
      <tr>
        <th onclick="sortTable(0)">Date</th>
        <th onclick="sortTable(1)">Company</th>
        <th onclick="sortTable(2)">Tank #</th>
        <th onclick="sortTable(3)">Size</th>
		 <th onclick="sortTable(4)">Number of Tanks</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $rows = array_map('str_getcsv', file($csvFile));
      array_shift($rows); // Remove header
      if (count($rows) > 0) {
          foreach ($rows as $row) {
              echo "<tr>";
              foreach ($row as $cell) {
                  echo "<td>" . htmlspecialchars($cell) . "</td>";
              }
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='4'>No delivery records found.</td></tr>";
      }
      ?>
    </tbody>
  </table>
</body>
</html>
<?php include_once 'layout_end.php'; ?>