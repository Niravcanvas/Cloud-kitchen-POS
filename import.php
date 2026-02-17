<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'cake_cafe_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Disable strict exception throwing
mysqli_report(MYSQLI_REPORT_OFF);

$sql = file_get_contents(__DIR__ . '/dump.sql');
$statements = explode(';', $sql);

$success = 0;
foreach ($statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement)) {
        if ($conn->query($statement)) {
            $success++;
        }
    }
}

echo "✅ Done! $success statements executed. Delete import.php now.";
?>