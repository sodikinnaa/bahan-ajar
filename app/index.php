<?php
require_once 'config/config.php';
require_once 'lib/Auth.php';

Auth::startSession();

if (Auth::isLoggedIn()) {
    include 'templates/dashboard.php';
} else {
    include 'templates/login.php';
}
?>
