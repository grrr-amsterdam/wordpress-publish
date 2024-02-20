<?php namespace Grrr\WordpressPublish;

class Config
{
    public function __construct(
        readonly string $applicationId,
        readonly string $privateKey,
        readonly string $workflowPath
    ) {
    }

    public static function fromConstants(): self
    {
        if (
            !defined("GRRR_WORDPRESS_PUBLISH_APPLICATION_ID") ||
            !defined("GRRR_WORDPRESS_PUBLISH_PRIVATE_KEY") ||
            !defined("GRRR_WORDPRESS_PUBLISH_WORKFLOW_PATH")
        ) {
            throw new \Exception(
                "Missing required constants for WordPress Publish."
            );
        }

        return new self(
            constant("GRRR_WORDPRESS_PUBLISH_APPLICATION_ID"),
            constant("GRRR_WORDPRESS_PUBLISH_PRIVATE_KEY"),
            constant("GRRR_WORDPRESS_PUBLISH_WORKFLOW_PATH")
        );
    }
}
