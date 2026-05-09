<?php

/**
 * Mass mailer
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2016, Ismayil Khayredinov
 */
use Elgg\Notifications\Event;
use Elgg\Notifications\Notification;
use hypeJunction\Notifications\MassMail;

require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'notifications_mass_mail_init');

/**
 * Initialize
 * @return void
 */
function notifications_mass_mail_init() {

	elgg_register_page_handler('mass_mail', 'notifications_mass_mail_page_handler');

	elgg_register_action('mass_mail/send', __DIR__ . '/actions/mass_mail/send.php');
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'notifications_mass_mail_permissions');

	$subtype = MassMail::SUBTYPE;
	elgg_register_plugin_hook_handler('get', 'subscriptions', 'notifications_mass_mail_get_subscriptions');
	elgg_register_notification_event('object', $subtype, ['send']);
	elgg_register_plugin_hook_handler('prepare', "notification:send:object:$subtype", 'notifications_mass_mail_prepare_notification');

	elgg_register_plugin_hook_handler('register', 'menu:page', 'notifications_mass_mail_page_menu_setup');
}

/**
 * Mass mail page handler
 * /mass_mail/send/<container_guid>
 *
 * @param array $segments URL segments
 * @return bool
 */
function notifications_mass_mail_page_handler($segments) {

	$page = array_shift($segments);

	switch ($page) {
		case 'send' :
			$container_guid = array_shift($segments);
			echo elgg_view_resource('mass_mail/send', [
				'container_guid' => $container_guid,
			]);
			return true;
	}

	return false;
}

/**
 * Filter container permissions
 *
 * @param string $hook   "container_permissions_check"
 * @param string $type   "object"
 * @param bool   $return Permission
 * @param array  $params Hook params
 * @return bool
 */
function notifications_mass_mail_permissions($hook, $type, $return, $params) {

	$container = elgg_extract('container', $params);
	$subtype = elgg_extract('subtype', $params);

	if ($subtype !== MassMail) {
		return;
	}

	switch ($container->getType()) {
		case 'object' :
		case 'user' :
			return false;

		case 'site':
			return;

		case 'group':
			return $container->canEdit();
	}
}

/**
 * Prepare recipients for mass mail
 *
 * @param string $hook   "get"
 * @param string $type   "subscriptions"
 * @param array  $return Subscriptions
 * @param array  $params Hook params
 * @return array
 */
function notifications_mass_mail_get_subscriptions($hook, $type, $return, $params) {

	$mass_mail = $params['event']->getObject();

	if (!elgg_instanceof($mass_mail, 'object', MassMail::SUBTYPE)) {
		return;
	}

	$return = []; // we don't care what other hooks want
	$container = $mass_mail->getContainerEntity();

	if ($container instanceof ElggSite) {
		$recipients = new ElggBatch('elgg_get_entities', [
			'types' => 'user',
			'callback' => false,
			'limit' => 0,
		]);
	} else {
		$recipients = new ElggBatch('elgg_get_entities_from_relationship', [
			'types' => 'user',
			'relationship' => 'member',
			'inverse_relationship' => true,
			'relationship_guid' => $container->guid,
			'callback' => false,
			'limit' => 0,
		]);
	}

	$method = $mass_mail->method ? : '_preferred';
	foreach ($recipients as $recipient) {
		if ($method == '_preferred') {
			$methods = (array) get_user_notification_settings($recipient->guid);
			$return[$recipient->guid] = array_keys($methods);
		} else {
			$return[$recipient->guid] = [$method];
		}
	}

	return $return;
}

/**
 * Prepare notification
 *
 * @param string       $hook         "prepare"
 * @param string       $type         "notification:send:object:mass_mail"
 * @param Notification $notification Notification
 * @param array        $params       Hook params
 * @return Notification
 */
function notifications_mass_mail_prepare_notification($hook, $type, $notification, $params) {

	$event = elgg_extract('event', $params);
	if (!$event instanceof Event) {
		return;
	}
	$action = $event->getAction();
	$actor = $event->getActor();
	$object = $event->getObject();
	if (!$object instanceof MassMail) {
		return;
	}
	$target = $object->getContainerEntity();

	$template_params = array(
		'action' => $action,
		'actor' => $actor,
		'object' => $object,
		'target' => $target,
		'recipient' => $notification->getRecipient(),
		'sender' => $notification->getSender(),
		'language' => $notification->language,
		'site' => elgg_get_site_entity(),
		'params' => $notification->params,
	);
	
	$notification->subject = mustache()->render($object->title, $template_params);
	$notification->summary = $notification->subject;
	$notification->body = mustache()->render($object->description, $template_params);

	return $notification;
}

/**
 * Setup page menu
 * 
 * @param string         $hook   "register"
 * @param string         $type   "menu:page"
 * @param ElggMenuItem[] $return Menu
 * @param array          $params Hook params
 * @return ElggMenuItem[]
 */
function notifications_mass_mail_page_menu_setup($hook, $type, $return, $params) {

	if (elgg_in_context('admin')) {
		$return[] = ElggMenuItem::factory([
					'name' => 'mass_mail',
					'parent_name' => 'administer_utilities',
					'section' => 'administer',
					'text' => elgg_echo('notifications:mass_mail'),
					'href' => "mass_mail/send",
		]);
	} else if (elgg_in_context('groups')) {
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner instanceof ElggGroup && elgg_get_plugin_setting('groups_mass_mail', 'notifications_mass_mail')) {
			$return[] = ElggMenuItem::factory([
						'name' => 'mass_mail',
						'text' => elgg_echo('notifications:mass_mail:groups'),
						'href' => "mass_mail/send/$page_owner->guid",
			]);
		}
	}

	return $return;
}
