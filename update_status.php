<?php
// Add these lines at the beginning of your script
error_log('Received status: ' . print_r($_POST['status'], true), 0);
error_log('Entry ID: ' . $_POST['entry_id'], 0);

// Rest of your PHP code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && isset($_POST['entry_id'])) {
    $status = $_POST['status'][0]; // Use [0] to get the first element of the array
    $entry_id = $_POST['entry_id'];

    // Validate and sanitize the data if needed

    $filePath = __DIR__ . '/saved.txt';

    // Read saved data
    $savedData = file_get_contents($filePath);
    $entries = explode("\n", $savedData);

    // Validate entry_id
    if (!is_numeric($entry_id) || $entry_id < 0 || $entry_id >= count($entries)) {
        // Handle invalid entry_id
        error_log('Invalid entry_id', 0);
        echo 'Invalid entry_id';
    } else {
        // Update the status for the specified entry
        $lines = explode("\n", $entries[$entry_id]);

        // Find and update the 'status' line
        foreach ($lines as $index => $line) {
            $parts = explode(':', $line, 2);
            $key = trim($parts[0]);

            if ($key === 'status') {
                $lines[$index] = "status:" . $status;
                break;
            }
        }

        // Update the entry in the entries array
        $entries[$entry_id] = implode("\n", $lines);

        // Update the saved.txt file
        $updatedData = implode("\n", $entries);

        $result = file_put_contents($filePath, $updatedData);

        // Add these lines to debug
        error_log('Update result: ' . print_r($result, true), 0);
        error_log('Updated Data: ' . $updatedData, 0);

        if ($result !== false) {
            error_log('Data updated successfully', 0);
            echo 'Data updated successfully';
        } else {
            error_log('Failed to update saved.txt', 0);
            echo 'Failed to update data';
        }
    }
} else {
    // Handle invalid requests or direct access to the file
    error_log('Invalid request', 0);
    echo 'Invalid request';
}
?>
