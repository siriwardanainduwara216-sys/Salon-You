<?php
// ============================================================
// services-data.php
// Fetches all services (grouped by category) and staff members.
// $conn must be set before including this file.
// ============================================================

// ---- All services, grouped by category ----
$services_by_category = [
    'mens-haircuts' => [],
    'beard-cuts' => [],
    'ladies-haircuts' => [],
    'coloring' => [],
    'facials' => [],
    'other' => [],
];

$sql = "SELECT id, service_name, category, image_url, price FROM services ORDER BY service_name ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cat = $row['category'];
        if (!isset($services_by_category[$cat])) {
            $services_by_category[$cat] = [];
        }
        $services_by_category[$cat][] = $row;
    }
}

// ---- All staff members (employees) ----
$staff_list = [];
$sql = "SELECT id, name FROM users WHERE role = 'employee' AND status = 'active' ORDER BY name ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $staff_list[] = $row;
    }
}

// ---- Category display labels ----
$category_labels = [
    'mens-haircuts'   => "Men's Haircuts",
    'beard-cuts'      => 'Beard Cuts',
    'ladies-haircuts' => 'Ladies Haircuts',
    'coloring'        => 'Coloring',
    'facials'         => 'Facials',
];