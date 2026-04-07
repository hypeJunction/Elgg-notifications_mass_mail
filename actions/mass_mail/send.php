<?php

use hypeJunction\Notifications\MassMail;

$guid = get_input('guid');
$container_guid = get_input('container_guid');
$title = get_input('title', '');
$description = get_input('description', '');
$method = get_input('method', '_preferred');

if (empty($title) || empty($description) || empty($method)) {
	elgg_register_error_message(elgg_echo('notifications:mass_mail:missing_field'));
	return elgg_redirect(REFERRER);
}

if ($guid) {
	$entity = get_entity($guid);
	if (!$entity instanceof MassMail || !$entity->canEdit()) {
		elgg_register_error_message(elgg_echo('actionunauthorized'));
		return elgg_redirect(REFERRER);
	}
} else {
	$container = get_entity($container_guid);
	if (!$container || !$container->canWriteToContainer(0, 'object', MassMail::SUBTYPE)) {
		elgg_register_error_message(elgg_echo('actionunauthorized'));
		return elgg_redirect(REFERRER);
	}

	$entity = new MassMail();
	$entity->container_guid = $container_guid;
	$entity->access_id = $container instanceof \ElggGroup ? $container->group_acl : ACCESS_LOGGED_IN;
}

$entity->title = $title;
$entity->description = $description;
$entity->method = $method;

if ($entity->save()) {
	elgg_register_success_message(elgg_echo('notifications:mass_mail:send:success'));
	elgg_trigger_event('send', 'object', $entity);
	return elgg_redirect($entity->getURL());
} else {
	elgg_register_error_message(elgg_echo('notifications:mass_mail:send:error'));
	return elgg_redirect(REFERRER);
}
