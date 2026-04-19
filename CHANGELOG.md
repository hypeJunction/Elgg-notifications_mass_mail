<a name="4.0.0"></a>
# 4.0.0 (2026-04-19)

### Migration: Elgg 3.x → 4.x

* Converted to declarative `elgg-plugin.php` with Bootstrap class; removed `start.php`
* Removed `manifest.xml`; composer.json is now sole metadata source
* Replaced `elgg_view_input()` with `elgg_view_field()` using `#type` prefix
* Replaced `label` with `#label` in form field configs
* Renamed route to `mass_mail:send` with explicit `{container_guid}` parameter
* Bumped `composer/installers` to `^2.0`, added `elgg/elgg ^4.0` constraint
* Added per-plugin Docker test stack with PHPUnit (21 tests) and Playwright (6 tests)

<a name="1.0.0"></a>
# 1.0.0 (2016-03-23)


### Features

* **releases:** initial commit ([8d4cefd](https://github.com/hypeJunction/Elgg-notifications_mass_mail/commit/8d4cefd))



