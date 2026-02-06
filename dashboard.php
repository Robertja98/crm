<?php include_once(__DIR__ . '/layout_start.php'); ?>
<?php $currentPage = basename(__FILE__); include_once(__DIR__ . '/navbar.php'); ?>
<?php
require_once 'csv_handler.php';
require_once 'forecast_calc.php';

$contacts = readCSV('contacts.csv', require __DIR__ . '/contact_schema.php');
$opportunities = readCSV('opportunities.csv', require __DIR__ . '/opportunity_schema.php');
$forecastData = calculateForecasts();
$forecasts = $forecastData['individual'];
$forecastByStage = $forecastData['by_stage'];

$totalContacts = count($contacts);
$totalValue = array_sum(array_column($opportunities, 'value'));
$totalForecast = array_sum(array_column($forecasts, 'forecast'));
$accuracy = $totalValue > 0 ? ($totalForecast / $totalValue) * 100 : 0;

// Identify top forecast stage
$topStage = '';
$maxForecast = 0;
foreach ($forecastByStage as $stage => $data) {
    if ($data['total_forecast'] > $maxForecast) {
        $maxForecast = $data['total_forecast'];
        $topStage = $stage;
    }
}

echo "<h2>CRM Dashboard</h2>";
echo "<div class='dashboard-card'><strong>Total Contacts:</strong> $totalContacts</div>";
echo "<div class='dashboard-card'><strong>Total Opportunity Value:</strong> $" . number_format($totalValue, 2) . "</div>";
echo "<div class='dashboard-card'><strong>Total Forecast Value:</strong> $" . number_format($totalForecast, 2) . "</div>";
echo "<div class='dashboard-card'><strong>Forecast Accuracy:</strong> " . number_format($accuracy, 1) . "%</div>";
echo "<div class='dashboard-card'><strong>Top Forecast Stage:</strong> $topStage</div>";

// Pipeline breakdown
$stages = [];
foreach ($opportunities as $opp) {
    $stage = $opp['stage'];
    $value = floatval($opp['value']);
    if (!isset($stages[$stage])) {
        $stages[$stage] = ['count' => 0, 'value' => 0];
    }
    $stages[$stage]['count']++;
    $stages[$stage]['value'] += $value;
}

echo "<h3>Pipeline Breakdown</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Stage</th><th>Count</th><th>Total Value</th></tr>";
foreach ($stages as $stage => $data) {
    echo "<tr>";
    echo "<td>$stage</td>";
    echo "<td>{$data['count']}</td>";
    echo "<td>$" . number_format($data['value'], 2) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Forecast by Stage</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Stage</th><th>Count</th><th>Total Forecast</th></tr>";
foreach ($forecastByStage as $stage => $data) {
    echo "<tr>";
    echo "<td>$stage</td>";
    echo "<td>{$data['count']}</td>";
    echo "<td>$" . number_format($data['total_forecast'], 2) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
