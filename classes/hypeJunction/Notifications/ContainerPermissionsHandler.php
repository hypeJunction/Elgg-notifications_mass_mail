<?php

namespace hypeJunction\Notifications;

use Elgg\Event;

/**
 * ContainerPermissionsHandler class.
 */
class ContainerPermissionsHandler {

	/**
	 * Filter container permissions for mass mail entities
	 *
	 * @param Event $hook Event
	 *
	 * @return bool|void
	 */
	public function __invoke(Event $hook) {
		$subtype = $hook->getParam('subtype');
		if ($subtype !== MassMail::SUBTYPE) {
			return;
		}

		$container = $hook->getParam('container');
		if (!$container instanceof \ElggEntity) {
			return;
		}

		switch ($container->getType()) {
			case 'object':
			case 'user':
				return false;
			case 'site':
				return;
			case 'group':
				return $container->canEdit();
		}
	}
}
