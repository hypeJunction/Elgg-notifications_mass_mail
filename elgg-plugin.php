<?php

use hypeJunction\Notifications\Bootstrap;
use hypeJunction\Notifications\ContainerPermissionsHandler;
use hypeJunction\Notifications\MassMail;
use hypeJunction\Notifications\PageMenuHandler;
use hypeJunction\Notifications\PrepareNotificationHandler;
use hypeJunction\Notifications\SubscriptionsHandler;

return [
	'plugin' => [
		'name' => 'Mass Mail',
		'version' => '4.0.0',
		'dependencies' => [
			'mustache' => [
				'position' => 'before',
			],
		],
	],

	'bootstrap' => Bootstrap::class,

	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'notification_mass_mail',
			'class' => MassMail::class,
			'searchable' => false,
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

	'hooks' => [
		'container_permissions_check' => [
			'object' => [
				ContainerPermissionsHandler::class => [],
			],
		],
		'get' => [
			'subscriptions' => [
				SubscriptionsHandler::class => [],
			],
		],
		'prepare' => [
			'notification:send:object:notification_mass_mail' => [
				PrepareNotificationHandler::class => [],
			],
		],
		'register' => [
			'menu:page' => [
				PageMenuHandler::class => [],
			],
		],
	],

	'notifications' => [
		'object' => [
			'notification_mass_mail' => [
				'send' => true,
			],
		],
	],
];
