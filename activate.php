<?php

use hypeJunction\Notifications\MassMail;
require_once __DIR__ . '/autoloader.php';
$subtypes = array(MassMail::SUBTYPE => MassMail::class);
foreach ($subtypes as $subtype => $class) {
    if (!elgg_set_entity_class('object', $subtype, $class)) {
        elgg_set_entity_class('object', $subtype, $class);
    }
}