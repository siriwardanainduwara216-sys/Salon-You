<?php
// ============================================================
// admin-service-inventory-data.php
// Queries for linking services to inventory item consumption.
// $conn must be set before including this file.
// ============================================================

// ---- All services (for the dropdown) ----
$all_services = [];
$sql = "SELECT id, service_name, price FROM services ORDER BY service_name ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $all_services[] = $row;
    }
}

// ---- All inventory items (for the dropdown) ----
$all_inventory = [];
$sql = "SELECT id, item_name, unit, uses_per_unit, quantity FROM inventory ORDER BY item_name ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $all_inventory[] = $row;
    }
}

// ---- Existing service-inventory usage mappings ----
$usage_list = [];
$sql = "SELECT siu.id, siu.uses_consumed, s.service_name, i.item_name, i.unit
        FROM service_inventory_usage siu
        JOIN services s ON siu.service_id = s.id
        JOIN inventory i ON siu.inventory_id = i.id
        ORDER BY s.service_name ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $usage_list[] = $row;
    }
}