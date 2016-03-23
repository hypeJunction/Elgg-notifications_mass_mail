<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_input('select', [
	'name' => 'params[groups_mass_mail]',
	'value' => $entity->groups_mass_mail,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
	'label' => elgg_echo('notifications:mass_mail:groups_mass_mail'),
]);