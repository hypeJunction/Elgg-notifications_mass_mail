<?php

use hypeJunction\Notifications\MassMail;

$container_guid = elgg_extract('container_guid', $vars);
if (!$container_guid) {
	$container_guid = elgg_get_site_entity()->guid;
}
elgg_entity_gatekeeper($container_guid);

$container = get_entity($container_guid);
if (!$container->canWriteToContainer(0, 'object', MassMail::SUBTYPE)) {
	register_error(elgg_echo('actionunauthorized'));
	forward('', '403');
}

elgg_set_page_owner_guid($container->guid);
elgg_group_gatekeeper($container_guid);

if ($container instanceof ElggGroup) {
	elgg_push_breadcrumb($container->getDisplayName(), $container->getURL());
}

$sticky = [];
if (elgg_is_sticky_form('mass_mail/edit')) {
	$sticky = elgg_get_sticky_values('mass_mail/eidt');
}
$params = array_merge($vars, $sticky);

$title = elgg_echo('notifications:mass_mail:add');
elgg_push_breadcrumb($title);

$content = elgg_view_form('mass_mail/send', [], [
	'entity' => null,
	'container' => $container,
]);

$layout = elgg_view_layout('content', [
	'title' => $title,
	'content' => $content,
	'filter' => '',
]);

echo elgg_view_page($title, $layout);