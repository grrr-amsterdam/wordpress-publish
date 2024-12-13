<?php namespace Grrr\WordpressPublish;

class Config
{
    public function __construct(
        public readonly string $applicationId,
        public readonly string $privateKey,
        public readonly string $owner,
        public readonly string $repository,
        public readonly string $workflowPath,
        public readonly string $ref
    ) {
    }

    public static function fromConstants(): self
    {
        if (
            self::hasConstants([
                "GRRR_WORDPRESS_PUBLISH_APPLICATION_ID",
                "GRRR_WORDPRESS_PUBLISH_PRIVATE_KEY",
                "GRRR_WORDPRESS_PUBLISH_WORKFLOW",
                "GRRR_WORDPRESS_PUBLISH_OWNER",
                "GRRR_WORDPRESS_PUBLISH_REPOSITORY",
            ])
        ) {
            return new self(
                constant("GRRR_WORDPRESS_PUBLISH_APPLICATION_ID"),
                constant("GRRR_WORDPRESS_PUBLISH_PRIVATE_KEY"),
                constant("GRRR_WORDPRESS_PUBLISH_OWNER"),
                constant("GRRR_WORDPRESS_PUBLISH_REPOSITORY"),
                constant("GRRR_WORDPRESS_PUBLISH_WORKFLOW"),
                defined("GRRR_WORDPRESS_PUBLISH_REF")
                    ? constant("GRRR_WORDPRESS_PUBLISH_REF")
                    : ""
            );
        }

        throw new \Exception(
            "Missing required constants. See README.md for the available and required constants."
        );
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
