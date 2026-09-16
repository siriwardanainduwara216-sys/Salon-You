<?php

$closed_dates_list = [];
$sql = "SELECT id, closed_date, reason FROM salon_closed_dates
        WHERE closed_date >= CURDATE()
        ORDER BY closed_date ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $closed_dates_list[] = $row;
    }
}