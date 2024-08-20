<?php namespace Grrr\WordpressPublish;

use WP_Post;

/**
 * Schedule a publish event the moment a post is scheduled to be published.
 */
class ScheduledPublish
{
    public function register(): void
    {
        add_action("publish_static_site", [$this, "publish_site"]);
        add_action(
            "transition_post_status",
            [$this, "maybe_trigger_publish"],
            10,
            3
        );
    }

    /**
     * Trigger a publish event when a post is scheduled to be published.
     */
    public function maybe_trigger_publish(
        string $newStatus,
        string $oldStatus,
        WP_Post $post
    ): void {
        if ($newStatus === "publish" && $oldStatus === "future") {
            $this->schedule_publish($post);
        }
    }

    /**
     * Schedule the 'publish_static_event' on the post's publish date.
     */
    public function schedule_publish(WP_Post $post): void
    {
        $timestamp = strtotime($post->post_date_gmt . " GMT");
        if ($timestamp === false) {
            error_log("Could fetch post_date_gmt for post ID: " . $post->ID);
            return;
        }
        wp_schedule_single_event($timestamp, "publish_static_site");
    }

    /**
     * Publish the site. Should be called by the 'publish_static_site' event.
     */
    public function publish_site(): void
    {
        $config = Config::fromConstants();
        $api = new Api(
            $config->applicationId,
            $config->privateKey,
            $config->workflowPath,
            $config->ref ?: "main"
        );

        $deployed = $api->deploy(null);
        if (is_wp_error($deployed)) {
            error_log("Error deploying: " . $deployed->get_error_message());
        }
    }
}
