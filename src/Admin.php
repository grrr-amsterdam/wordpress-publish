<?php namespace Grrr\WordpressPublish;

use Grrr\WordpressPublish\Api;
use Grrr\WordpressPublish\Renderer;
use Grrr\WordpressPublish\ValueObjects\RestRoute;
use stdClass;

class Admin
{
    const SLUG = "grrr-wordpress-publish";
    const JS_GLOBAL = "GRRR_WORDPRESS_PUBLISH";

    public function __construct(
        private string $basePath,
        private string $baseUrl,
        private string $version,
        private Api $api
    ) {
    }

    public function register(): void
    {
        add_action("admin_menu", [$this, "admin_menu"]);
        add_action("admin_enqueue_scripts", [$this, "register_assets"]);
        add_action("wp_before_admin_bar_render", [$this, "admin_bar"]);
    }

    public function admin_bar(): void
    {
        global $wp_admin_bar;
        $wp_admin_bar->add_node([
            "id" => static::SLUG,
            "title" => "Deploy",
            "href" => admin_url() . "admin.php?page=" . static::SLUG,
        ]);
    }

    public function admin_menu(): void
    {
        add_menu_page(
            "Deploy site",
            "Deploy",
            "edit_posts",
            static::SLUG,
            [$this, "render_admin"],
            $this->get_icon("rocket.svg")
        );
    }

    public function register_assets(): void
    {
        wp_register_style(
            static::SLUG,
            $this->get_asset_url("css/main.css"),
            [],
            $this->version
        );
        wp_register_script(
            static::SLUG,
            $this->get_asset_url("js/main.js"),
            ["jquery"],
            $this->version
        );
        wp_localize_script(static::SLUG, static::JS_GLOBAL, [
            "api" => [
                "nonce" => wp_create_nonce("wp_rest"),
                "endpoints" => $this->get_endpoints(),
            ],
        ]);

        // Use style on every admin page so we can overwrite the css of Simply Static
        wp_enqueue_style(static::SLUG);
    }

    public function render_admin(): void
    {
        wp_enqueue_script(static::SLUG);

        $forms = $this->get_form_data();
        $status = $this->api->poll_status();

        include trailingslashit($this->basePath) . "views/admin-page.php";
    }

    private function get_asset_url(string $path): string
    {
        return trailingslashit($this->baseUrl) . $path;
    }

    private function get_icon(string $filename): string
    {
        $icon = trailingslashit($this->basePath) . "assets/icons/" . $filename;
        $iconContents = file_get_contents($icon);

        return $iconContents
            ? "data:image/svg+xml;base64," . base64_encode($iconContents)
            : "";
    }

    private function get_endpoints(): array
    {
        return array_reduce(
            $this->api->routes(),
            function (array $acc, RestRoute $route) {
                $acc[$route->slug] = $route->url();
                return $acc;
            },
            []
        );
    }

    /**
     * @return iterable<stdClass>
     */
    protected function get_form_data(): iterable
    {
        return array_reduce(
            $this->api->routes(),
            function (array $acc, RestRoute $route) {
                $acc[$route->slug] = (object) [
                    "action" => $route->url(),
                    "method" => $route->method,
                ];
                return $acc;
            },
            []
        );
    }
}
