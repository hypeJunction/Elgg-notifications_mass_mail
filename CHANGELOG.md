<a name="6.0.0"></a>
# 6.0.0 (2026-05-09)

### Breaking Changes

* **elgg:** raise minimum to Elgg 6.x (PHP 8.1+). Plugins on Elgg 5.x must stay on notifications_mass_mail 5.x.

### Migration (5.x → 6.x)

* **composer:** `elgg/elgg ~6.1.0`, PHP `>=8.1`, added `ext-intl`.
* **docker:** added `docker/elgg6/` per-plugin test stack.
* No PHP API changes required — no AMD/RequireJS JS, no removed function calls.

### Dependency Updates

* `elgg/elgg ~6.1.0`, PHP `>=8.1`, version bumped to `6.0.0`

---

<a name="5.0.0"></a>
# 5.0.0 (2026-05-08)

### Breaking Changes

* **elgg:** raise minimum to Elgg 5.x (PHP 8.2+). Plugins on Elgg 4.x must stay on notifications_mass_mail 4.x.

### Migration (4.x → 5.x)

* **events:** `elgg-plugin.php` `hooks` key renamed to `events`.
* **handlers:** all four handler classes updated to `\Elgg\Event` type hint (was `\Elgg\Hook`).
* **docker:** stack updated to `php:8.2-apache`, `mysql:8.0`, `elgg/elgg 5.1.12`.
* **tests:** PHPUnit mocks updated — `Event` needs `disableOriginalConstructor()`; `MassMail` mock uses constructor for proper `__set()`.

### Dependency Updates

* `elgg/elgg ^5.0`, PHP `>=8.2`, version bumped to `5.0.0`

---

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



