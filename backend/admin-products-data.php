<?php

// ADMIN PRODUCTS DATA
// Fetches all data needed by admin-products.php


// Fetch all products 
$products_list = [];
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products_list[] = $row;
    }
}


// ADMIN PRODUCTS DATA


// Fetch all products 
$products_list = [];
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $products_list[] = $row;
}

//Low stock products (stock_quantity <= 5) 
$low_stock_products = [];
$sql = "SELECT id, product_name, stock_quantity FROM products WHERE is_active = 1 AND stock_quantity <= 5 ORDER BY stock_quantity ASC";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $low_stock_products[] = $row;
}
?>