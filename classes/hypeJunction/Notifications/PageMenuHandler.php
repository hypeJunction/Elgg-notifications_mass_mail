<?php

namespace hypeJunction\Notifications;

use Elgg\Hook;

/**
 * PageMenuHandler class.
 */
class PageMenuHandler {

	/**
	 * Setup page menu
	 *
	 * @param Hook $hook Hook
	 *
	 * @return \ElggMenuItem[]
	 */
	public function __invoke(Hook $hook) {
		$return = $hook->getValue();

		if (\elgg_in_context('admin')) {
			$return[] = \ElggMenuItem::factory([
				'name' => 'mass_mail',
				'parent_name' => 'administer_utilities',
				'section' => 'administer',
				'text' => \elgg_echo('notifications:mass_mail'),
				'href' => 'mass_mail/send',
			]);
		} else if (\elgg_in_context('groups')) {
			$page_owner = \elgg_get_page_owner_entity();
			if ($page_owner instanceof \ElggGroup && \elgg_get_plugin_setting('groups_mass_mail', 'notifications_mass_mail')) {
				$return[] = \ElggMenuItem::factory([
					'name' => 'mass_mail',
					'text' => \elgg_echo('notifications:mass_mail:groups'),
					'href' => "mass_mail/send/{$page_owner->guid}",
				]);
			}
		}

		return $return;
	}
}
