<?php

/**
 * WordPress Publish
 *
 * Plugin Name: WordPress Publish
 * Description: Allow starting a GitHub workflow from WordPress
 * Author:      Ramiro Hammen <ramiro@grrr.nl>
 * Version:     1.1.0
 */

use Grrr\WordpressPublish\Plugin;

if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly
}

if (is_readable(__DIR__ . "/vendor/autoload.php")) {
    require __DIR__ . "/vendor/autoload.php";
}

define("GRRR_WORDPRESS_PUBLISH_PLUGIN_FILE", __FILE__);

$plugin = new Plugin();
$plugin->init();
