<?php
// Start session if needed
session_start();

// Include Composer autoload
require __DIR__ . '/../vendor/autoload.php';

// Plain password typed by user
$passwordTyped = '123456'; // user input from login form
$hashedFromDB = '$2y$10$e0NRz1/1q1Xp4ZsF1ghY9O7fYtY6bD0x0k5Zz./4qxghmD1Kix5N6'; // DB value

if (password_verify($passwordTyped, $hashedFromDB)) {
    echo "Password matches!";
} else {
    echo "Password does NOT match!";
}

