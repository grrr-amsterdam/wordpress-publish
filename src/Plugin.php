<?php

namespace Grrr\WordpressPublish;

class Plugin
{
    const VERSION = "0.1.0";

    public function init(): void
    {
        // Bootstrap components.
        $config = Config::fromConstants();
        $api = new Api(
            $config->applicationId,
            $config->privateKey,
            $config->workflowPath,
            $config->ref ?: "main"
        );
        $api->register();

        $plugin_url = plugin_dir_url(__FILE__);
        $assetsRootUrl = $plugin_url . "assets";
        (new Admin(__DIR__, $assetsRootUrl, self::VERSION, $api))->register();
    }
}
