<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT * FROM paths");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['id'] . ": " . $row['title'] . "\n";
}
?>