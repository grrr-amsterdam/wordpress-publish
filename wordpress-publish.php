<?php

/**
 * WordPress Publish
 *
 * Plugin Name: WordPress Publish
 * Description: Allow publishing Next.js frontend from WordPress
 * Author:      Ramiro Hammen <ramiro@grrr.nl>
 * Version:     0.1.0
 */

use Grrr\WordpressPublish\Plugin;

if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly
}

if (is_readable(__DIR__ . "/vendor/autoload.php")) {
    require __DIR__ . "/vendor/autoload.php";
}

$plugin = new Plugin();
$plugin->init();
