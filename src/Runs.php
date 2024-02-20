<?php namespace Grrr\WordpressPublish;

final class Runs {

    public static function createFromGitHubResponse(array $response): self {
        $runs = array_map(function (array $data) {
            return Run::createFromGitHubResponse($data);
        }, $response);
        return new self($runs);
    }

    /**
     * @param Run[] $runs
     */
    public function __construct(private array $runs) {

    }

    public function getLastRun(): ?Run {
        $runs = $this->runs;

        usort($runs, function (Run $a, Run $b) {
            return strtotime($a->updated_at) - strtotime($b->updated_at);
        });

        return array_pop($runs);
    }
}
