<?php

use hypeJunction\Notifications\MassMail;

require_once __DIR__ . '/autoloader.php';

$subtypes = array(
	MassMail::SUBTYPE => MassMail::class,
);

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}