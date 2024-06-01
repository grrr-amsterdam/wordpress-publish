<?php namespace Grrr\WordpressPublish;

class Exception extends \Exception
{
    public static function invalidPrivateKey(): self
    {
        return new self(
            "GRRR_WORDPRESS_PUBLISH_PRIVATE_KEY contains an invalid private key."
        );
    }

    public static function failedToSignData(): self
    {
        return new self("Failed to sign the data.");
    }
}
