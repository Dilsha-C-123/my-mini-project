<?php
session_start();
session_destroy(); // Destroy the session

// Redirect to home page (index.html)
header("Location: ../index.html");
exit();
?>
