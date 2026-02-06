<?php
require_once 'csv_handler.php';

function calculateForecasts($filename = 'opportunities.csv') {
    $schema = require __DIR__ . '/opportunity_schema.php';
    $opportunities = readCSV($filename, $schema);
    $results = [];
    $stageTotals = [];

    foreach ($opportunities as $opp) {
        $value = floatval($opp['value']);
        $probability = floatval($opp['probability']);
        $forecast = round($value * ($probability / 100), 2);

        $stage = $opp['stage'] ?? 'Unspecified';

        // Add to grouped stage totals
        if (!isset($stageTotals[$stage])) {
            $stageTotals[$stage] = ['count' => 0, 'total_forecast' => 0];
        }
        $stageTotals[$stage]['count']++;
        $stageTotals[$stage]['total_forecast'] += $forecast;

        // Add to individual forecast list
        $results[] = [
            'id' => $opp['id'] ?? '',
            'contact_id' => $opp['contact_id'] ?? '',
            'stage' => $stage,
            'value' => $value,
            'forecast' => $forecast
        ];
    }

    return [
        'individual' => $results,
        'by_stage' => $stageTotals
    ];
}
?>
