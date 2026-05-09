<?php

use hypeJunction\Notifications\MassMail;

$guid = get_input('guid');
$container_guid = get_input('container_guid');
$title = get_input('title', '');
$description = get_input('description', '');
$method = get_input('method', '_preferred');

if (empty($title) || empty($description) || empty($method)) {
	register_error(elgg_echo('notifications:mass_mail:missing_field'));
	forward(REFERRER);
}

if ($guid) {
	$entity = get_entity($guid);
	if (!$entity instanceof MassMail || !$entity->canEdit()) {
		register_error(elgg_echo('actionunauthorized'));
		forward(REFERRER);
	}
} else {
	$container = get_entity($container_guid);
	if (!$container || !$container->canWriteToContainer(0, 'object', MassMail::SUBTYPE)) {
		register_error(elgg_echo('actionunauthorized'));
		forward(REFERRER);
	}

	$entity = new MassMail();
	$entity->container_guid = $container_guid;
	$entity->access_id = $container instanceof ElggGroup ? $container->group_acl : ACCESS_LOGGED_IN;
}

$entity->title = $subject;
$entity->description = $description;
$entity->method = $method;

if ($entity->save()) {
	system_message(elgg_echo('notifications:mass_mail:send:success'));
	elgg_trigger_event('send', 'object', $entity);
	forward($entity->getURL());
} else {
	register_error(elgg_echo('notifications:mass_mail:send:success'));
	forward(REFERRER);
}
