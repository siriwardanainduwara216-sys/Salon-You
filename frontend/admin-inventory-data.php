<?php
// ============================================================
// admin-inventory-data.php
// All inventory related queries live here.
// $conn must be set before including this file.
// ============================================================

$inventory_list = [];
$sql = "SELECT id, item_name, category, quantity, unit, uses_per_unit, low_stock_threshold, price, updated_at
        FROM inventory
        ORDER BY quantity ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $inventory_list[] = $row;
    }
}