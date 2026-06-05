<?php

use hypeJunction\Notifications\Bootstrap;
use hypeJunction\Notifications\ContainerPermissionsHandler;
use hypeJunction\Notifications\MassMail;
use hypeJunction\Notifications\PageMenuHandler;
use hypeJunction\Notifications\PrepareNotificationHandler;
use hypeJunction\Notifications\Seeder;
use hypeJunction\Notifications\SubscriptionsHandler;

return [
	'plugin' => [
		'name' => 'Mass Mail',
		'version' => '6.0.0',
		'dependencies' => [],
	],

	'bootstrap' => Bootstrap::class,

	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'notification_mass_mail',
			'class' => MassMail::class,
		],
	],

	'routes' => [
		'mass_mail:send' => [
			'path' => '/mass_mail/send/{container_guid}',
			'resource' => 'mass_mail/send',
			'defaults' => ['container_guid' => 0],
		],
	],

	'actions' => [
		'mass_mail/send' => [],
	],

	'events' => [
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
		'seeds' => [
			'database' => [
				Seeder::class . '::addSeed' => [],
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
