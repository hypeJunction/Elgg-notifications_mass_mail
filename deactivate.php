<?php

require_once __DIR__ . '/autoloader.php';

$subtypes = array(
	hypeJunction\Notifications\MassMail::SUBTYPE,
);

foreach ($subtypes as $subtype => $class) {
	update_subtype('object', $subtype);
}