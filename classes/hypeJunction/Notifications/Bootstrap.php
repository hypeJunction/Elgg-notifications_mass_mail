<?php

namespace hypeJunction\Notifications;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {

	/**
	 * {@inheritdoc}
	 */
	public function load() {
		$root = $this->plugin->getPath();
		if (file_exists("{$root}/vendor/autoload.php")) {
			require_once "{$root}/vendor/autoload.php";
		}
	}
}
