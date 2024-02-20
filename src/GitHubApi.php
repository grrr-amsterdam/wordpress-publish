<?php namespace Grrr\WordpressPublish;

use WP_Error;
use WP_Http;

final class GitHubApi
{
    /**
     * @param JWT $jwt
     * @return array|WP_Error
     */
    public function getInstallation(
        JWT $jwt,
        string $organisation = "grrr-amsterdam"
    ): array|WP_Error {
        $response = (new WP_Http())->request(
            "https://api.github.com/app/installations",
            [
                "method" => "GET",
                "headers" => [
                    "Authorization" => "Bearer " . $jwt->createToken(),
                ],
            ]
        );

        if ($response instanceof WP_Error) {
            return $response;
        }

        if ($response["response"]["code"] >= 400) {
            return new WP_Error(
                $response["response"]["code"],
                $response["response"]["message"]
            );
        }

        $responseBody = json_decode($response["body"], true);
        if (!is_array($responseBody)) {
            return new WP_Error(500, "GitHub API response is not an array.");
        }

        foreach ($responseBody as $installation) {
            if ($installation["account"]["login"] === $organisation) {
                return $installation;
            }
        }

        return new WP_Error(
            404,
            "Organisation $organisation not found in installations."
        );
    }

    /**
     * @param JWT $jwt
     * @param string $accessTokenUrl
     * @return array|WP_Error
     */
    public function getAccessToken(JWT $jwt, string $accessTokenUrl)
    {
        $response = (new WP_Http())->request($accessTokenUrl, [
            "method" => "POST",
            "headers" => [
                "Authorization" => "Bearer " . $jwt->createToken(),
            ],
        ]);

        if ($response instanceof WP_Error) {
            return $response;
        }

        if ($response["response"]["code"] >= 400) {
            return new WP_Error(
                $response["response"]["code"],
                $response["response"]["message"]
            );
        }

        $responseBody = json_decode($response["body"], true);
        if (!is_array($responseBody)) {
            return new WP_Error(500, "GitHub API response is not an array.");
        }
        return $responseBody;
    }

    /**
     * @param string $accessToken
     * @param string $workflowPath
     * @return array|WP_Error
     */
    public function getRuns(
        string $accessToken,
        string $workflowPath
    ): array|WP_Error {
        $response = (new WP_Http())->request("$workflowPath/runs", [
            "method" => "GET",
            "headers" => [
                "Authorization" =>
                    "Basic " . base64_encode("x-access-token:{$accessToken}"),
            ],
        ]);

        if ($response instanceof WP_Error) {
            return $response;
        }

        if ($response["response"]["code"] >= 400) {
            return new WP_Error(
                $response["response"]["code"],
                "GitHub error: " . $response["response"]["message"]
            );
        }

        $responseBody = json_decode($response["body"], true);
        if (!is_array($responseBody)) {
            return new WP_Error(500, "GitHub API response is not an array.");
        }
        return $responseBody["workflow_runs"];
    }

    public function dispatchWorkflow(
        string $accessToken,
        string $workflowPath,
        string $ref
    ): bool|WP_Error {
        $body = json_encode(["ref" => $ref]);
        if ($body === false) {
            return new WP_Error(
                500,
                "Failed to encode body for Github workflow dispatch endpoint."
            );
        }
        $response = (new WP_Http())->request("$workflowPath/dispatches", [
            "method" => "POST",
            "body" => $body,
            "headers" => [
                "Authorization" =>
                    "Basic " . base64_encode("x-access-token:{$accessToken}"),
            ],
        ]);

        if ($response instanceof WP_Error) {
            return $response;
        }

        if ($response["response"]["code"] >= 400) {
            return new WP_Error(
                $response["response"]["code"],
                "GitHub error: " . $response["response"]["message"]
            );
        }

        return true;
    }
}
