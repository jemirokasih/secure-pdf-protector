<?php
/*
Plugin Name: Secure PDF Protector
Plugin URI: https://github.com/jemirokasih/secure-pdf-protector
Description: Protect uploaded PDF files so only logged-in users can access them securely.
Version: 1.2.0
Author: Jemiro Kasih
Author URI: https://mzi.co.id
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: secure-pdf-protector
*/

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register rewrite endpoint
 */
add_action('init', function () {

    add_rewrite_rule(
        '^secure-pdf/?',
        'index.php?secure_pdf=1',
        'top'
    );
});

/**
 * Register query var
 */
add_filter('query_vars', function ($vars) {

    $vars[] = 'secure_pdf';

    return $vars;
});

/**
 * Secure PDF handler
 */
add_action('template_redirect', function () {

    if (!get_query_var('secure_pdf')) {
        return;
    }

    // Require login
    if (!is_user_logged_in()) {
        auth_redirect();
    }

    // Validate parameter
    if (!isset($_GET['file'])) {
        wp_die('Missing file parameter.');
    }

    $file_url = urldecode($_GET['file']);

    $upload_dir = wp_upload_dir();

    $baseurl = $upload_dir['baseurl'];
    $basedir = $upload_dir['basedir'];

    // Must come from uploads directory
    if (!str_starts_with($file_url, $baseurl)) {
        wp_die('Invalid file URL.');
    }

    // Convert URL to relative path
    $relative = str_replace($baseurl . '/', '', $file_url);

    // Prevent directory traversal
    $relative = str_replace(
        ['../', '..\\'],
        '',
        $relative
    );

    // Final local path
    $path = trailingslashit($basedir) . $relative;

    // File must exist
    if (!file_exists($path)) {
        wp_die('File not found.');
    }

    // Only allow PDF
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if ($ext !== 'pdf') {
        wp_die('File type not allowed.');
    }

    // Prevent caching
    nocache_headers();

    header('Content-Type: application/pdf');
    header(
        'Content-Disposition: inline; filename="' .
        basename($path) .
        '"'
    );

    header('Content-Length: ' . filesize($path));

    readfile($path);
    exit;
});

/**
 * Flush rewrite rules on activation
 */
register_activation_hook(__FILE__, function () {

    add_rewrite_rule(
        '^secure-pdf/?',
        'index.php?secure_pdf=1',
        'top'
    );

    flush_rewrite_rules();
});

/**
 * Flush rewrite rules on deactivation
 */
register_deactivation_hook(__FILE__, function () {

    flush_rewrite_rules();
});
