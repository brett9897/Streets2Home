<?php

// Inialize session
session_start();
session_destroy();

// Delete certain session
unset($_SESSION['username']);
// Delete all session variables
// session_destroy();

// Jump to login page
header('Location: index.php');

?>
