function exportCSVFiltered($filename, $rows, $filters = []) {
    $filtered = array_filter($rows, function($row) use ($filters) {
        foreach ($filters as $key => $value) {
            if ($row[$key] !== $value) return false;
        }
        return true;
    });
    return writeCSV($filename, array_values($filtered));
}
