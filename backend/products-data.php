<?php
$active_products = [];
$sql = "SELECT id, product_name, description, image_url, price FROM products WHERE is_active = 1 ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $active_products[] = $row;
}
?>