<?php namespace Grrr\WordpressPublish;

class Config
{
    public function __construct(
        readonly string $applicationId,
        readonly string $privateKey,
        readonly string $workflowPath,
        readonly string $ref
    ) {
    }

    public static function fromConstants(): self
    {
        if (
            self::hasConstants([
                "GRRR_WORDPRESS_PUBLISH_APPLICATION_ID",
                "GRRR_WORDPRESS_PUBLISH_PRIVATE_KEY",
                "GRRR_WORDPRESS_PUBLISH_WORKFLOW_PATH",
            ])
        ) {
            return new self(
                constant("GRRR_WORDPRESS_PUBLISH_APPLICATION_ID"),
                constant("GRRR_WORDPRESS_PUBLISH_PRIVATE_KEY"),
                constant("GRRR_WORDPRESS_PUBLISH_WORKFLOW_PATH"),
                defined("GRRR_WORDPRESS_PUBLISH_REF")
                    ? constant("GRRR_WORDPRESS_PUBLISH_REF")
                    : ""
            );
        }

        // Make it backwards compatible with the old constants
        if (
            self::hasConstants([
                "GITHUB_DEPLOY_APPLICATION_ID",
                "GITHUB_DEPLOY_PRIVATE_KEY",
                "GITHUB_DEPLOY_WORKFLOW_PATH",
            ])
        ) {
            return new self(
                constant("GITHUB_DEPLOY_APPLICATION_ID"),
                constant("GITHUB_DEPLOY_PRIVATE_KEY"),
                constant("GITHUB_DEPLOY_WORKFLOW_PATH"),
                ""
            );
        }

        throw new \Exception("Missing required constants.");
    }

    protected static function hasConstants(array $constantNames): bool
    {
        $definedConstants = array_filter(
            $constantNames,
            fn($name) => defined($name)
        );
        return $constantNames === $definedConstants;
    }
}
