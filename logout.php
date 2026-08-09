<?php
// Initialize the session environment
session_start();

// Unset all session variables cleanly
session_unset();

// Completely destroy the active session context
session_destroy();

// Safely redirect back to your main application front page
header("Location: index.php");

?>