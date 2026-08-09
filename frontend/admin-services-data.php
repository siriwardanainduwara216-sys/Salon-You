<?php
// ============================================================
// admin-services-data.php
// Queries for managing the services table.
// $conn must be set before including this file.
// ============================================================

$all_services_list = [];
$sql = "SELECT id, service_name, category, image_url, price, duration_mins
        FROM services
        ORDER BY category ASC, service_name ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $all_services_list[] = $row;
    }
}

// Category labels shown in the dropdown / badges
$category_labels = [
    'mens-haircuts'   => "Men's Haircuts",
    'beard-cuts'      => 'Beard Cuts',
    'ladies-haircuts' => 'Ladies Haircuts',
    'coloring'        => 'Coloring',
    'facials'         => 'Facials',
    'other'           => 'Other',
];

// ---- Group the flat services list by category, in the same order as category_labels ----
$services_grouped = [];
foreach ($category_labels as $cat_key => $cat_label) {
    $services_grouped[$cat_key] = [];
}
foreach ($all_services_list as $service) {
    $cat = $service['category'];
    if (!isset($services_grouped[$cat])) {
        $services_grouped[$cat] = []; // handles any category not in the predefined list
    }
    $services_grouped[$cat][] = $service;
}