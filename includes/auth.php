
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user']);
$currentUser = $_SESSION['user'] ?? null;
