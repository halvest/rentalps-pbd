<?php
include '../config/db.php';

$sql = "SELECT p.kategori, COUNT(t.id_transaksi) AS transaksi_count
        FROM Playstation p
        LEFT JOIN Transaksi t ON p.id_playstation = t.id_playstation
        GROUP BY p.kategori";

$result = $conn->query($sql);
$data = ['transaksi_counts' => []];
while ($row = $result->fetch_assoc()) {
    $data['transaksi_counts'][] = $row['transaksi_count'];
}

header('Content-Type: application/json');
echo json_encode($data);
?>