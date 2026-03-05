<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;

$sql = "SELECT * FROM packages WHERE status = 'active' ORDER BY price ASC";
if ($limit > 0) {
    $sql .= " LIMIT $limit";
}

$result = mysqli_query($conn, $sql);
$packages = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $packages[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'packages' => $packages,
    'count' => count($packages)
]);

mysqli_close($conn);
?>
