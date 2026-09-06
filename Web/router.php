<?php
/**
 * Router script for PHP built-in web server running WordPress
 */
$root = __DIR__;
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Common admin shortcuts
if ($path === '/nav-menus.php') {
    header('Location: /wp-admin/nav-menus.php', true, 302);
    exit;
}

if ($path === '/admin' || $path === '/login') {
    header('Location: /wp-admin/', true, 302);
    exit;
}

// If physical file exists, let PHP serve it
if (file_exists($root . $path) && !is_dir($root . $path)) {
    return false;
}

// If directory exists with index.php
if (is_dir($root . $path) && file_exists($root . $path . '/index.php')) {
    return false;
}

// Pass all other URLs (pretty permalinks, query routes) to WordPress index.php
$_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require_once $root . '/index.php';
