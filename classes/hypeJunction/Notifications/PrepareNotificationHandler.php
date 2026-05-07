<?php

namespace hypeJunction\Notifications;

use Elgg\Hook;
use Elgg\Notifications\Notification;
use Elgg\Notifications\SubscriptionNotificationEvent;

/**
 * PrepareNotificationHandler class.
 */
class PrepareNotificationHandler {

	/**
	 * Prepare notification
	 *
	 * @param Hook $hook Hook
	 *
	 * @return Notification|void
	 */
	public function __invoke(Hook $hook) {
		$notification = $hook->getValue();
		if (!$notification instanceof Notification) {
			return;
		}

		$event = $hook->getParam('event');
		if (!$event instanceof SubscriptionNotificationEvent) {
			return;
		}

		$action = $event->getAction();
		$actor = $event->getActor();
		$object = $event->getObject();

		if (!$object instanceof MassMail) {
			return;
		}

		$target = $object->getContainerEntity();

		$template_params = [
			'action' => $action,
			'actor' => $actor,
			'object' => $object,
			'target' => $target,
			'recipient' => $notification->recipient,
			'sender' => $notification->sender,
			'language' => $notification->language,
			'site' => \elgg_get_site_entity(),
			'params' => $notification->params,
		];

		$mustache = new \Mustache_Engine();
		$notification->subject = $mustache->render($object->title, $template_params);
		$notification->summary = $notification->subject;
		$notification->body = $mustache->render($object->description, $template_params);

		return $notification;
	}
}
