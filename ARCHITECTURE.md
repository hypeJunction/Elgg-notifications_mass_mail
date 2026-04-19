# notifications_mass_mail — Architecture (Elgg 4.x)

## Summary

Allows site admins (and optionally group admins) to send bulk email notifications
to all site members or all members of a group. Each send creates a persisted
`notification_mass_mail` entity, triggering Elgg's notification pipeline so
every recipient is notified via their preferred delivery method.

## Directory Structure

```
notifications_mass_mail/
├── actions/mass_mail/send.php      # Action: create/resend mass mail entity
├── classes/hypeJunction/Notifications/
│   ├── Bootstrap.php               # Loads plugin vendor autoload
│   ├── ContainerPermissionsHandler.php  # Restricts container types
│   ├── MassMail.php                # Entity: object/notification_mass_mail
│   ├── PageMenuHandler.php         # Adds mass_mail items to page menu
│   ├── PrepareNotificationHandler.php   # Renders Mustache subject/body
│   └── SubscriptionsHandler.php   # Resolves recipient list for notification event
├── views/default/
│   ├── forms/mass_mail/send.php   # Send form (title, body, method select)
│   ├── plugins/notifications_mass_mail/settings.php  # Plugin settings view
│   └── resources/mass_mail/send.php  # Page resource — renders form with layout
├── docker/                         # Per-plugin Elgg 4.x test stack
├── tests/phpunit/integration/      # 21 PHPUnit integration tests
└── tests/playwright/               # 6 Playwright e2e tests
```

## Entity Types

| Type   | Subtype                  | Class                                      |
|--------|--------------------------|--------------------------------------------|
| object | notification_mass_mail   | `hypeJunction\Notifications\MassMail`      |

Not searchable (no `capabilities.searchable` entry).

## Hooks Registered

| Hook name                                         | Type                                    | Handler                          |
|---------------------------------------------------|-----------------------------------------|----------------------------------|
| `container_permissions_check`                     | `object`                                | `ContainerPermissionsHandler`    |
| `get`                                             | `subscriptions`                         | `SubscriptionsHandler`           |
| `prepare`                                         | `notification:send:object:notification_mass_mail` | `PrepareNotificationHandler` |
| `register`                                        | `menu:page`                             | `PageMenuHandler`                |

## Routes

| Name            | Path                              | Resource view         |
|-----------------|-----------------------------------|-----------------------|
| `mass_mail:send` | `/mass_mail/send/{container_guid}` | `mass_mail/send`      |

## Actions

| Path             | Access  |
|------------------|---------|
| `mass_mail/send` | logged_in (default) |

## Notifications

Sends subscription notifications on `send` event for `object:notification_mass_mail`.
Subject and body are Mustache templates rendered by `PrepareNotificationHandler`
with `actor`, `object`, `target`, `recipient`, and `sender` as template variables.

## Permissions Logic

- `ContainerPermissionsHandler`: allows only `site` and `group` containers; rejects
  `object` and `user` containers; for groups, requires `canEdit()` on the group.
- Settings expose `groups_mass_mail` toggle; when enabled, group admins see a
  mass mail link in the group page menu.

## Dependencies

- `mustache/mustache ^2.0` — Mustache template engine for email bodies
- No plugin dependencies declared

## Migration Notes (3.x → 4.x)

- Converted to declarative `elgg-plugin.php` with Bootstrap class (no `start.php`)
- Removed `manifest.xml`; metadata now in `composer.json`
- `elgg_view_input()` calls replaced with `elgg_view_field()` using `#type` prefix
- `label` renamed to `#label` in field configs
- Route renamed from `mass_mail` (segment-based) to `mass_mail:send` with explicit parameter
- `searchable` key removed from entities section (deprecated in 4.1; not searchable so no `capabilities.searchable` entry needed)
- Docker test stack added with PHPUnit + Playwright coverage
