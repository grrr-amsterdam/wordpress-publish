# WordPress Publish

Allows cms users to publish Next.js frontend from WordPress.

It does so by providing an api to trigger a certain github action. This action will then build and deploy the frontend.

## Installation via Composer

### Composer

First, add the `repositories` directive to your `composer.json`:

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/grrr-amsterdam/wordpress-publish"
    }
]
```

Second, require this package in your project:

```sh
composer require grrr-amsterdam/wordpress-publish
```

Make sure to add the API URL from the redirects by defining some required constants (these should be secret, so don't add the values to your repository):

```php
define("GRRR_WORDPRESS_PUBLISH_APPLICATION_ID", "your-application-id");
define("GRRR_WORDPRESS_PUBLISH_PRIVATE_KEY", "secret-private-key-rsa-key");
define(
  "GRRR_WORDPRESS_PUBLISH_WORKFLOW_PATH",
  "https://api.github.com/repos/acme/example/actions/workflows/publish-production.yml"
);
```
