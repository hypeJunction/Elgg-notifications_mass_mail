<?php

use hypeJunction\Notifications\Bootstrap;
use hypeJunction\Notifications\MassMail;

return [
	'bootstrap' => Bootstrap::class,

	'entities' => [
		[
			'type' => 'object',
			'subtype' => MassMail::SUBTYPE,
			'class' => MassMail::class,
		],
	],

	'routes' => [
		'mass_mail' => [
			'path' => '/mass_mail/{segments}',
			'resource' => 'mass_mail',
			'requirements' => ['segments' => '.+'],
			'defaults' => ['segments' => ''],
		],
	],

	'actions' => [
		'mass_mail/send' => [],
	],
];
