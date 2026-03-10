<?php
// Simple debugging script to see what's being received

// Log all received data
error_log("=== TEST_UPLOAD.PHP ===");
error_log("POST: " . print_r($_POST, true));
error_log("FILES: " . print_r($_FILES, true));
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("CONTENT_TYPE: " . $_SERVER['CONTENT_TYPE']);

// Return success
echo json_encode([
    'status' => 'ok',
    'post_keys' => array_keys($_POST),
    'files_keys' => array_keys($_FILES),
    'post_count' => count($_POST),
    'files_count' => count($_FILES)
]);
?>
