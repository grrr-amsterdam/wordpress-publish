<?php namespace Grrr\WordpressPublish;

final class Run
{
    public static function createFromGitHubResponse(array $response): self
    {
        return new self(
            $response["status"],
            $response["updated_at"],
            $response["conclusion"],
            $response["run_started_at"]
        );
    }

    public function __construct(
        public string $status,
        public string $updated_at,
        public ?string $conclusion,
        public string $run_started_at
    ) {
    }

    public function isPublishing(): bool
    {
        return $this->status !== "completed" && $this->status !== "canceled";
    }
}
