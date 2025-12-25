<?php
include 'db_config.php';

$sql = "SELECT * FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($users as $user) {
    echo $user['name'] . "<br>";
}
?>
