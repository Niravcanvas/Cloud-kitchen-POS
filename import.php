<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'cake_cafe_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = file_get_contents(__DIR__ . '/dump.sql');
$statements = explode(';', $sql);

$errors = [];
foreach ($statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement)) {
        if (!$conn->query($statement)) {
            $errors[] = $conn->error;
        }
    }
}

if (empty($errors)) {
    echo "✅ Database imported successfully! Delete import.php now.";
} else {
    echo "Done with some errors:<br>";
    foreach ($errors as $e) echo "- $e<br>";
}
?>