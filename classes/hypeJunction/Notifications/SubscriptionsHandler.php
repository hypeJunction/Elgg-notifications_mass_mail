<?php

namespace hypeJunction\Notifications;

use Elgg\Event;

/**
 * SubscriptionsHandler class.
 */
class SubscriptionsHandler {

	/**
	 * Prepare recipients for mass mail
	 *
	 * @param Event $hook Event
	 *
	 * @return array|void
	 */
	public function __invoke(Event $hook) {
		$event = $hook->getParam('event');
		if (!$event) {
			return;
		}

		$mass_mail = $event->getObject();
		if (!$mass_mail instanceof MassMail) {
			return;
		}

		// we don't care what other hooks want
		$return = [];

		$container = $mass_mail->getContainerEntity();
		if ($container instanceof \ElggSite) {
			$recipients = \elgg_get_entities([
				'type' => 'user',
				'limit' => false,
				'batch' => true,
			]);
		} else {
			$recipients = \elgg_get_entities([
				'type' => 'user',
				'relationship' => 'member',
				'inverse_relationship' => true,
				'relationship_guid' => $container->guid,
				'limit' => false,
				'batch' => true,
			]);
		}

		$method = $mass_mail->method ?: '_preferred';
		foreach ($recipients as $recipient) {
			if ($method == '_preferred') {
				$notification_settings = $recipient->getNotificationSettings();
				$enabled_methods = [];
				foreach ($notification_settings as $m => $enabled) {
					if ($enabled) {
						$enabled_methods[] = $m;
					}
				}

				$return[$recipient->guid] = $enabled_methods;
			} else {
				$return[$recipient->guid] = [$method];
			}
		}

		return $return;
	}
}
