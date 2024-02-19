<?php

namespace Grrr\WordpressPublish;


class Plugin
{
	public function activate(): void
	{
		flush_rewrite_rules();
	}

	public function deactivate(): void
	{
		flush_rewrite_rules();
	}

	public function init(): void
	{
		echo 'Hello, world!';
	}
}
