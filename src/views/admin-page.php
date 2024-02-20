<div class="wrap github-deploy-page js-github-deploy">

    <!-- see https://wordpress.stackexchange.com/a/220735 -->
    <h2 style="display: none;"></h2>

    <div class="card">
        <section>
            <h1><?= get_admin_page_title() ?></h1>
            <h2>Publication status</h2>

            <div data-type="status" class="notice notice-info inline">
                <p class="wp-clearfix">Fetching publication status...</p>
            </div>

            <div class="wp-clearfix" style="margin-bottom: 15px;">
                <form
                    class="alignleft"
                    data-type="github-deploy-form"
                    action="<?= $forms["deploy"]->action ?>"
                    method="<?= $forms["deploy"]->method ?>"
                    style="margin-right: 10px;"
                    >
                    <?= wp_nonce_field("wp_rest") ?>
                    <button
                        class="button button-primary button-large"
                        type="submit"
                        >
                        Generate &amp; deploy
                    </button>
                </form>
            </div>
            <div class="wp-clearfix" style="margin-bottom: 15px; display: none;">
                <form
                    class="alignleft"
                    data-type="github-poll-form"
                    action="<?= $forms["poll"]->action ?>"
                    method="<?= $forms["poll"]->method ?>"
                    style="margin-right: 10px;"
                    >
                    <?= wp_nonce_field("wp_rest") ?>
                    <button
                        class="button button-primary button-large"
                        type="submit"
                        >
                        Poll
                    </button>
                </form>
            </div>
        </section>
    </div>

</div>
