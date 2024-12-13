<?php

namespace Grrr\WordpressPublish;

class Plugin
{
    public function init(): void
    {
        if (is_admin()) {
            if (!function_exists("get_plugin_data")) {
                require_once ABSPATH . "wp-admin/includes/plugin.php";
            }
            $plugin_data = get_plugin_data(__FILE__);
            $version = $plugin_data["Version"];
        } else {
            $version = "not-available";
        }

        // Bootstrap components.
        $config = Config::fromConstants();
        $api = new Api(
            $config->applicationId,
            $config->privateKey,
            $config->workflowPath,
            $config->ref ?: "main",
            new GitHubApi($config->owner, $config->repository)
        );
        $api->register();

        $plugin_url = plugin_dir_url(__FILE__);
        $assetsRootUrl = $plugin_url . "assets";
        (new Admin(__DIR__, $assetsRootUrl, $version, $api))->register();

        (new ScheduledPublish())->register();
    }
}
