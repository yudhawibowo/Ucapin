<?php
/**
 * Admin Authentication Helper
 * Include this file at the top of admin pages to require login
 */
session_start();

function requireAdminAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

function getAdminUsername() {
    return $_SESSION['admin_username'] ?? 'Admin';
}

function logoutAdmin() {
    session_start();
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
