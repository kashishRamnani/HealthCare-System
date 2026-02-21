<?php
session_start();

// Store data in the session
$_SESSION['username'] = 'Kashish';

// Access session data
echo $_SESSION['username']; // Output: Kashish
?>
