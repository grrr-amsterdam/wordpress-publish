<?php namespace Grrr\WordpressPublish\ValueObjects;

/**
 * @property-read string $slug
 * @property-read string $namespace
 * @property-read string $method
 * @property-read string $callback_fn_name
 * @property-read string $capability
 */
final class RestRoute
{
    private string $slug;
    private string $namespace;
    private string $method;
    private string $callback_fn_name;
    private string $capability;

    public function __construct(array $args)
    {
        $this->validate($args);
        $this->namespace = $args["namespace"];
        $this->method = $args["method"];
        $this->slug = $args["slug"];
        $this->callback_fn_name = $args["callback_fn_name"];
        $this->capability = $args["capability"];
    }

    public function __get(string $name): mixed
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }

    public function url(): string
    {
        return rest_url($this->namespace . "/" . $this->slug);
    }

    private function validate(array $args): void
    {
        if (!isset($args["namespace"])) {
            throw new \Exception("Endpoint namespace is required");
        }
        if (!isset($args["slug"])) {
            throw new \Exception("Endpoint slug is required");
        }
        if (!isset($args["method"])) {
            throw new \Exception("Endpoint method is required");
        }
        if (!isset($args["callback_fn_name"])) {
            throw new \Exception("Endpoint callback_fn_name is required");
        }
        if (!isset($args["capability"])) {
            throw new \Exception("Endpoint capability is required");
        }
    }
}
