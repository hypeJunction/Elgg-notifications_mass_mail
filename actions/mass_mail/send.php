<?php

use hypeJunction\Notifications\MassMail;

$guid = get_input('guid');
$container_guid = get_input('container_guid');
$title = get_input('title', '');
$description = get_input('description', '');
$method = get_input('method', '_preferred');

if (empty($title) || empty($description) || empty($method)) {
	return elgg_error_response(elgg_echo('notifications:mass_mail:missing_field'));
}

if ($guid) {
	$entity = get_entity($guid);
	if (!$entity instanceof MassMail || !$entity->canEdit()) {
		return elgg_error_response(elgg_echo('actionunauthorized'));
	}
} else {
	$container = get_entity($container_guid);
	if (!$container || !$container->canWriteToContainer(0, 'object', MassMail::SUBTYPE)) {
		return elgg_error_response(elgg_echo('actionunauthorized'));
	}

	$entity = new MassMail();
	$entity->container_guid = $container_guid;
	$entity->access_id = ACCESS_LOGGED_IN;
	if ($container instanceof \ElggGroup) {
		$acl = $container->getOwnedAccessCollection('group_acl');
		if ($acl) {
			$entity->access_id = $acl->id;
		}
	}
}

$entity->title = $title;
$entity->description = $description;
$entity->method = $method;

if ($entity->save()) {
	elgg_trigger_event('send', 'object', $entity);
	return elgg_ok_response('', elgg_echo('notifications:mass_mail:send:success'), $entity->getURL());
} else {
	return elgg_error_response(elgg_echo('notifications:mass_mail:send:error'));
}
