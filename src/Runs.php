<?php namespace Grrr\WordpressPublish;

final class Runs
{
    public static function createFromGitHubResponse(array $response): self
    {
        $runs = array_map(function (array $data) {
            return Run::createFromGitHubResponse($data);
        }, $response);
        return new self($runs);
    }

    /**
     * @param Run[] $runs
     */
    public function __construct(private array $runs)
    {
    }

    /**
     * Get the run that was started most recently. GitHub can change the
     * `updated_at` of old runs long after they finished, so that field can not
     * be used to determine the last run.
     */
    public function getLastRun(): ?Run
    {
        $runs = $this->runs;

        usort($runs, function (Run $a, Run $b) {
            return strtotime($a->run_started_at) <=>
                strtotime($b->run_started_at);
        });

        return array_pop($runs);
    }
}
