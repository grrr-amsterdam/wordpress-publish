<?php namespace Grrr\WordpressPublish;

final class Run
{
    public static function createFromGitHubResponse(array $response): self
    {
        return new self(
            $response["status"],
            $response["updated_at"],
            $response["conclusion"]
        );
    }

    public function __construct(
        public string $status,
        public string $updated_at,
        public ?string $conclusion
    ) {
    }

    public function isPublishing(): bool
    {
        return $this->status !== "completed" && $this->status !== "canceled";
    }
}
