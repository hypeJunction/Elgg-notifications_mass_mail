<?php
/**
 * PHPUnit bootstrap for notifications_mass_mail plugin tests.
 * Plugin must be installed at {elgg_root}/mod/notifications_mass_mail/
 */

// tests/ -> mod/notifications_mass_mail/ -> mod/ -> elgg_root/
$elggRoot = dirname(__DIR__, 3);

require_once $elggRoot . '/vendor/autoload.php';

// Load Elgg test classes (UnitTestCase, IntegrationTestCase, etc.)
$testClassesDir = $elggRoot . '/vendor/elgg/elgg/engine/tests/classes';
spl_autoload_register(function ($class) use ($testClassesDir) {
    $file = $testClassesDir . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Manual PSR-0 autoload for plugin classes (in case plugin isn't active in test DB)
$pluginRoot = dirname(__DIR__);
spl_autoload_register(function ($class) use ($pluginRoot) {
    if (strncmp($class, 'hypeJunction\\Notifications\\', 27) !== 0) {
        return;
    }
    $file = $pluginRoot . '/classes/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

\Elgg\Application::loadCore();
