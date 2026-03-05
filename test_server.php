<?php
// Start the server in the background
$server = proc_open('php spark serve', [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w']
], $pipes, 'c:\laragon\www\skripsi\SIMATIK');

if (!is_resource($server)) {
    die("Failed to start server\n");
}

// Wait for the server to start
sleep(3);

// Try to fetch the home page
$ch = curl_init('http://localhost:8080');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Output the results
echo "HTTP Status: $httpCode\n";
if ($httpCode != 200) {
    echo "Response Snippet: " . substr($response, 0, 500) . "...\n";
} else {
    echo "Server is responding correctly (200 OK)\n";
}

// Terminate the server
proc_terminate($server);
foreach ($pipes as $pipe) {
    fclose($pipe);
}
?>
