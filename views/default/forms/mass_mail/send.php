<?php

$entity = elgg_extract('entity', $vars);
if ($entity instanceof \hypeJunction\Notifications\MassMail) {
	$container = $entity->getContainerEntity();
} else {
	$container = elgg_extract('container', $vars);
}

if (!$container instanceof ElggEntity) {
	return;
}

if (!$container->canWriteToContainer(0, 'object', \hypeJunction\Notifications\MassMail::SUBTYPE)) {
	return;
}

if ($container instanceof ElggSite) {
	$count = elgg_get_entities([
		'types' => 'user',
		'count' => true,
	]);
} else {
	$count = elgg_get_entities_from_relationship([
		'types' => 'user',
		'relationship' => 'member',
		'inverse_relationship' => true,
		'relationship_guid' => $container->guid,
		'count' => true,
	]);
}

echo elgg_format_element('p', ['class' => 'elgg-text-help'], elgg_echo('notifications:mass_mail:recipient_count', [$count]));

$fields = [
	[
		'name' => 'title',
		'type' => 'text',
		'label' => elgg_echo('notifications:mass_mail:subject'),
		'value' => elgg_extract('title', $vars, $entity->title),
		'required' => true,
	],
	[
		'name' => 'description',
		'type' => 'plaintext',
		'label' => elgg_echo('notifications:mass_mail:message'),
		'value' => elgg_extract('description', $vars, $entity->description),
		'required' => true,
	]
];

if (elgg_is_admin_logged_in()) {
	$methods = array_keys(_elgg_services()->notifications->getMethods());
	$method_options = ['_preferred' => elgg_echo('notifications:mass_mail:method:_preferred')];
	foreach ($methods as $method) {
		$method_options[$method] = elgg_echo("notification:method:$method");
	}
	$fields[] = [
		'type' => 'radio',
		'name' => 'method',
		'value' => elgg_extract('method', $vars, $entity->method),
		'options' => array_flip($method_options),
		'label' => elgg_echo('notifications:mass_mail:method'),
		'required' => true,
	];
}

foreach ($fields as $opts) {
	$type = elgg_extract('type', $opts);
	echo elgg_view_input($type, $opts);
}

echo elgg_view_input('hidden', [
	'name' => 'guid',
	'value' => $entity->guid,
]);

echo elgg_view_input('hidden', [
	'type' => 'hidden',
	'name' => 'container_guid',
	'value' => $container->guid,
]);

echo elgg_format_element('p', ['class' => 'elgg-text-help'], nl2br(elgg_echo('notifications:mass_mail:dynamic_fields')));

echo elgg_view_input('submit', [
	'value' => ($entity) ? elgg_echo('notifications:mass_mail:resend') : elgg_echo('notifications:mass_mail:send'),
	'field_class' => 'elgg-foot',
]);
