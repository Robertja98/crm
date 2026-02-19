


/**
 * Export filtered rows to a CSV file.
 *
 * @param string $filename Output CSV file path
 * @param array  $rows     Data rows (array of associative arrays)
 * @param array  $filters  Key-value pairs to filter rows (optional)
 * @param array  $schema   Column order/schema (optional, recommended)
 * @return bool  True on success, false on failure
 */
function exportCSVFiltered(
    string $filename,
    array $rows,
    array $filters = [],
    array $schema = []
) {
    $filtered = array_filter(
        $rows,
        function ($row) use ($filters) {
            foreach ($filters as $key => $value) {
                if (!isset($row[$key]) || $row[$key] !== $value) {
                    return false;
                }
            }
            return true;
        }
    );

    // If schema is not provided, use keys from the first filtered row
    if (empty($schema) && !empty($filtered)) {
        $schema = array_keys(reset($filtered));
    }

    return writeCSV($filename, array_values($filtered), $schema);
}
