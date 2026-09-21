<?php


// Staff okkoma fetch karanawa 
$staff_list = [];
$sql = "SELECT id, name, email, phone, status, created_at
        FROM users
        WHERE role = 'employee'
        ORDER BY created_at DESC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $staff_list[] = $row;
    }
}