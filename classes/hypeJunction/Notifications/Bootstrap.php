<?php

namespace hypeJunction\Notifications;

use Elgg\Includer;
use Elgg\PluginBootstrap;

class Bootstrap extends PluginBootstrap {

	/**
	 * Get plugin root
	 * @return string
	 */
	protected function getRoot() {
		return $this->plugin->getPath();
	}

	/**
	 * {@inheritdoc}
	 */
	public function load() {
		Includer::requireFileOnce($this->getRoot() . '/autoloader.php');
	}

	/**
	 * {@inheritdoc}
	 */
	public function boot() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function init() {
		$subtype = MassMail::SUBTYPE;

		elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'notifications_mass_mail_permissions');
		elgg_register_plugin_hook_handler('get', 'subscriptions', 'notifications_mass_mail_get_subscriptions');
		elgg_register_notification_event('object', $subtype, ['send']);
		elgg_register_plugin_hook_handler('prepare', "notification:send:object:{$subtype}", 'notifications_mass_mail_prepare_notification');
		elgg_register_plugin_hook_handler('register', 'menu:page', 'notifications_mass_mail_page_menu_setup');
	}

	/**
	 * {@inheritdoc}
	 */
	public function ready() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function shutdown() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function activate() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function deactivate() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function upgrade() {

	}

}
