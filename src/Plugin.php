<?php

namespace Grrr\WordpressPublish;

class Plugin
{
    public function activate(): void
    {
        flush_rewrite_rules();
    }

    public function deactivate(): void
    {
        flush_rewrite_rules();
    }

    public function init(): void
    {
        $config = Config::fromConstants();
        // Bootstrap components.
        $api = new Api(
            $config->applicationId,
            $config->privateKey,
            $config->workflowPath
        );
        $api->register();

        $plugin_url = plugin_dir_url(__FILE__);
        $assetsRootUrl = $plugin_url . "assets";
        (new Admin(__DIR__, $assetsRootUrl, "1.0.0", $api))->register();
    }
}
