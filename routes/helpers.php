<?php

/**
 * Simple Router for BusBook Application
 * This provides Laravel-like routing for the XAMPP environment
 */

// Utilities
function view($template, $data = []) {
    extract($data);
    $path = __DIR__ . '/../resources/views/' . str_replace('.', '/', $template) . '.blade.php';
    if (file_exists($path)) {
        ob_start();
        include $path;
        return ob_get_clean();
    }
    return "View not found: " . $template;
}

function redirect($url) {
    $base = '/DMS_BOOKING';
    // Avoid double-prefixing if already includes base
    if (strpos($url, $base) !== 0) {
        $url = $base . $url;
    }
    header("Location: " . $url);
    exit;
}

function auth() {
    return $_SESSION['user'] ?? null;
}

function isAuth() {
    return isset($_SESSION['user']);
}

function dd($var) {
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    die;
}

function isAdmin() {
    return isset($_SESSION['user']) && ($_SESSION['user']['type'] ?? '') === 'admin';
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'DMS_BOOKING');
if ($conn->connect_error) {
    // Initialize database
    require_once __DIR__ . '/../database/init.php';
    $conn = new mysqli('localhost', 'root', '', 'DMS_BOOKING');
}

// Store in globals
$GLOBALS['db'] = $conn;
$GLOBALS['user'] = $_SESSION['user'] ?? null;
