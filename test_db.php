<?php
// test_db.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// This links back to your newly fixed config file
require 'config.php';

if ($conn) {
    echo "<div style='font-family: sans-serif; padding: 20px; background-color: #dcfce3; color: #166534; border-radius: 8px; border: 1px solid #22c55e;'>";
    echo "<h1>✅ Database Connection Successful!</h1>";
    echo "<p>Connected to database: <strong>" . DB_NAME . "</strong></p>";
    echo "</div>";
} else {
    echo "<div style='font-family: sans-serif; padding: 20px; background-color: #fee2e2; color: #991b1b; border-radius: 8px; border: 1px solid #ef4444;'>";
    echo "<h1>❌ Database Connection Failed!</h1>";
    echo "</div>";
}
?>