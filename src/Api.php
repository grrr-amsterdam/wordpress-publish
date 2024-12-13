<?php

namespace Grrr\WordpressPublish;

use DateTime;
use Grrr\WordpressPublish\Run;
use Grrr\WordpressPublish\ValueObjects\RestRoute;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class Api
{
    const ROUTES = [
        [
            "namespace" => "grrr/wordpress-publish/v1",
            "slug" => "poll",
            "method" => "POST",
            "callback_fn_name" => "poll_status",
            "capability" => "edit_posts",
        ],
        [
            "namespace" => "grrr/wordpress-publish/v1",
            "slug" => "deploy",
            "method" => "POST",
            "callback_fn_name" => "deploy",
            "capability" => "edit_posts",
        ],
    ];

    const TRANSIENT_NAME_ACCESS_TOKEN = "wordpress-publish-access-token";

    private JWT $jwt;

    /**
     * @var array<RestRoute>
     */
    private $routes = [];

    public function __construct(
        string $applicationId,
        string $privateKey,
        private string $workflowPath,
        private string $ref,
        private GitHubApi $githubApi
    ) {
        $this->jwt = new JWT($privateKey, $applicationId);
        $this->routes = array_map(
            fn($endpoint) => new RestRoute($endpoint),
            self::ROUTES
        );
    }

    public function register(): void
    {
        add_action("rest_api_init", [$this, "register_api_endpoints"]);
    }

    /**
     * @return array<RestRoute>
     */
    public function routes(): array
    {
        return $this->routes;
    }

    public function register_api_endpoints(): void
    {
        foreach ($this->routes as $route) {
            register_rest_route($route->namespace, $route->slug, [
                "methods" => [$route->method],
                "callback" => [$this, $route->callback_fn_name],
                "permission_callback" => fn() => current_user_can(
                    $route->capability
                ),
            ]);
        }
    }

    /**
     * @param WP_REST_Request<array>|null $request
     * @return WP_REST_Response|WP_Error
     */
    public function poll_status(
        ?WP_REST_Request $request = null
    ): WP_REST_Response|WP_Error|Run {
        $accessToken = $this->getGitHubAccessToken();
        if ($accessToken instanceof WP_Error) {
            return $accessToken;
        }

        $workflowRuns = $this->githubApi->getRuns(
            $accessToken,
            $this->workflowPath
        );
        if ($workflowRuns instanceof WP_Error) {
            return $workflowRuns;
        }

        $runs = Runs::createFromGitHubResponse($workflowRuns);

        $lastRun = $runs->getLastRun();
        if (!$lastRun) {
            return new WP_Error(404, "No runs found.");
        }

        if ($request) {
            return new WP_REST_Response($lastRun, 200);
        }
        return $lastRun;
    }

    /**
     * @param WP_REST_Request<array>|null $request
     * @return WP_REST_Response|WP_Error
     */
    public function deploy(
        ?WP_REST_Request $request
    ): WP_Error|WP_REST_Response|array {
        $accessToken = $this->getGitHubAccessToken();
        if ($accessToken instanceof WP_Error) {
            return $accessToken;
        }
        $workflowRuns = $this->githubApi->getRuns(
            $accessToken,
            $this->workflowPath
        );
        if ($workflowRuns instanceof WP_Error) {
            return $workflowRuns;
        }

        $runs = Runs::createFromGitHubResponse($workflowRuns);

        $lastRun = $runs->getLastRun();

        if ($lastRun && $lastRun->isPublishing()) {
            if ($request) {
                return new WP_REST_Response([], 200);
            }
            return [];
        }

        $deployed = $this->githubApi->dispatchWorkflow(
            $accessToken,
            $this->workflowPath,
            $this->ref
        );

        if ($deployed instanceof WP_Error) {
            return $deployed;
        }

        if ($request) {
            return new WP_REST_Response([], 200);
        }
        return [];
    }

    protected function getGitHubAccessToken(): string|WP_Error
    {
        /** @var string|false $accessToken */
        $accessToken = get_transient(self::TRANSIENT_NAME_ACCESS_TOKEN);

        if ($accessToken !== false) {
            return $accessToken;
        }

        $installation = $this->githubApi->getInstallation($this->jwt);
        if ($installation instanceof WP_Error) {
            return $installation;
        }

        $accessToken = $this->githubApi->getAccessToken(
            $this->jwt,
            $installation["access_tokens_url"]
        );

        if ($accessToken instanceof WP_Error) {
            return $accessToken;
        }

        set_transient(
            self::TRANSIENT_NAME_ACCESS_TOKEN,
            $accessToken["token"],
            (new DateTime($accessToken["expires_at"]))->getTimestamp() -
                (new DateTime())->getTimestamp()
        );

        /** @var string $token */
        $token = $accessToken["token"];
        return $token;
    }
}
