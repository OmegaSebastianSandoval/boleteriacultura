# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

"Boletería Cultural" — a PHP ticketing/reservation system for Club El Nogal's gala event ("Fiesta de Gala" / "Cena de Gala"). It handles table/seat reservations, guest management, QR-code ticket generation and PDF tickets, PlacetoPay payment integration, and door-scan validation of tickets. Built on a small in-house MVC framework (`omega/framework`) rather than a mainstream PHP framework — there is no Laravel/Symfony app skeleton to look for.

## Running the app locally

There is no `npm run dev`/`composer start` script; use Grunt or run PHP's built-in server directly:

```bash
# install PHP deps
composer install
# install frontend components (jQuery, Bootstrap, TinyMCE, etc. into public_html/components)
bower install
# serve public_html/ on http://0.0.0.0:8043 (matches RUTA in development config)
php -S 0.0.0.0:8043 -t public_html/
# or via Grunt (runs bower_install + composer_install + server)
node_modules/.bin/grunt development
```

The dev DB config (`app/config/config.php`, `development` key) expects a local MySQL database named `boleteriacultural` on `localhost` with user `root` / no password. Import `public_html/sql/boleteria.sql` for schema, plus any files in `public_html/sql/migraciones/`.

There is no automated test suite — `phpunit` is a composer dependency but no `phpunit.xml` or test files exist. There is no linter config (`php_codesniffer` is a dev dependency but unconfigured). Verify changes by exercising the relevant route in a browser.

## Environment detection

`app/bootstrap.php` picks `APPLICATION_ENV` (development/staging/production) by matching `HTTP_HOST` against hardcoded hostnames, not from a config file. Each env has its own DB credentials and PlacetoPay keys in `app/config/config.php`, and its own `RUTA`/`URL_BASE` constants. When adding a new environment/host, update both `bootstrap.php`'s host matching and `config.php`.

## Request lifecycle

1. Everything routes through `public_html/index.php` → `.htaccess` rewrites all non-file requests there.
2. `App::run()` (`framework/App.php`) builds `Http_Routes`, `Http_Requests`, `Http_Response`, then dispatches.
3. `Http_Routes` (`framework/Http/Routes.php`) parses the URL path as `/module/controller/action/param1/param2/...` (defaults: `page/index/index`). Anything after the 4th segment lands in a positional `$_routs` array, not named params.
4. The controller class resolved is `{Module}_{controller}Controller`, e.g. `/administracion/eventos/manage` → `Administracion_eventosController`. Autoloading for this naming pattern is done by the custom `framework_autoload()` function in `bootstrap.php` (not Composer's autoloader) — it maps `X_YController` → `app/modules/x/Controllers/Y.php`, and `X_Model_...` → `app/modules/x/Models/....php`. Composer's autoloader (`vendor/autoload.php`) is only used for third-party libs (PHPMailer, TCPDF, PlacetoPay SDK, etc.).
5. Controller action method is `{action}Action()`. After the action runs, `render()` is called, which either renders through a `Layout` (if `setLayout()` was called) or renders the view file directly by `eval()`-ing its PHP source (see `framework/View.php`/`framework/Layout.php` — views are plain PHP files with embedded `<?php ?>`, not a templating engine).

## Modules

- `app/modules/page` — public-facing site: landing pages, event info, reservation flow (`evento`, `reservas`, `guests`, `acomodaciones`), login. `Page_mainController` is the base controller most `page` controllers extend; its `init()` sets up the layout, loads the socio/session, current event config, CSRF token, etc. on every request.
- `app/modules/administracion` — the admin back office (CRUD screens for eventos, mesas, pisos, ambientes, categorías, reservas, invitados, boletas/tickets, usuarios, contenido, publicidad...). `Administracion_mainController::init()` enforces the admin session check (`kt_login_id` in session) and inactivity timeout, except when `modo=validacion&display=1` is passed (an exception carved out for the validation kiosk view).
- `app/modules/core` — shared cross-module models/services: `Core_Model_Mail` / `Core_Model_Sendingemail` (PHPMailer wrapper + email templates in `Views/templatesemail`), `Core_Model_Csrf`, `Core_Model_Upload_Image`/`Upload_Document`, `Core_Model_Dias`, `Core_Model_Image`. Also has the `placetopay` and generic `mesas`/`user` controllers used by other modules (e.g. PlacetoPay payment return webhook).
- `app/modules/validacion` — the door-scan/ticket-validation kiosk flow (scans a boleta's QR/token, marks it validated, has its own audit log via `logAuditoriaBoleta`). Session-gated on a `user` session key, separate from the admin `kt_login_id` session.

Models live under `Models/DbTable/{Name}.php` and extend `Db_Table` (`framework/Db/Table.php`), which provides generic `getById`, `getList`, `getListPages`, `getListCount`, `editField`, `changeOrder`, `deleteRegister` built from raw SQL string concatenation (no query builder/ORM, no prepared statements) against a table name/id column set in the subclass (`$_name`, `$_id`). Model classes are named `{Module}_Model_DbTable_{Table}` — same module-prefixed convention as controllers. Because queries are built by string concatenation, always pass parameters through the sanitization helpers below rather than embedding raw request input.

## Request-parameter sanitization

`Controllers_Abstract` (`framework/Controllers/Abstract.php`) is the base for every controller and provides:
- `_getSanitizedParam($name)` — strips tags, trims, `mysqli_real_escape_string`s, and regex-strips common SQL-injection keywords. Use this (not `$_GET`/`$_POST` directly) for any value that flows into a raw SQL string.
- `_getSanitizedParamHtml($name)` — for rich-text/HTML fields (e.g. TinyMCE content), strips a specific FLMNGR widget-injection payload and `addslashes`.
- `_getDecryptedParam($name)` — for IDs passed through the URL-obfuscation scheme below; reads the raw value (not through the SQL sanitizer, since it's a base64url token).
- `_getSanitizedValue($value)` — sanitizes an already-fetched value (`addslashes`+`strip_tags`+`htmlentities`).

CSRF: each controller/section has a `_csrf_section` key; `Core_Model_Csrf` stores a random per-section token in `Session::get('csrf')[section]`, which state-changing actions compare against a submitted `csrf` param before proceeding.

ID obfuscation: `app/helpers/idcipher.php` provides global `enc_id()`/`dec_id()` (AES-256-CBC) to avoid exposing enumerable sequential DB IDs in URLs (used in the purchase flow). This obfuscates IDs only — it is not a substitute for verifying the session owns the resource; callers must still check ownership.

## Payments and tickets

- `Payment_Placetopay` (`framework/Payment/Placetopay.php`) wraps the `dnetix/redirection` PlacetoPay SDK, configured per-environment from `app/config/config.php`. The return/webhook flow is handled by `Core_placetopayController`.
- Tickets ("boletas") get a QR code (via `phpqrcode`, written to `public_html/images_sales/qrs_news/`) and a PDF (via TCPDF, a custom `MYPDF` class, written to `public_html/pdfs_news/`), generated from a unique `boleta_uid`/`boleta_token` derived from `hash('sha256', ...)`. See `reenviarboleteriaAction` in `app/modules/page/Controllers/indexController.php` for the full generation/resend flow, including guest-data completeness validation before issuing tickets.
- The `validacion` module scans a boleta's QR (uid + token) to validate/check in a guest at the door.

## Sessions

`Session` (`framework/Session.php`) is a thin singleton wrapper over `$_SESSION`. Key session values used across modules: `kt_login_id`/`tiempo` (admin auth + inactivity timeout), `socio`/`sesion` (public-site member session, tied to an external SSO login at `URL_BASE_LOGIN`), `user` (validacion-kiosk auth), `csrf` (per-section CSRF tokens).

## Frontend assets

Not bundled/built — `public_html/skins/{page,administracion}/{css,js,images,fonts}` are hand-authored, and `public_html/components` holds Bower-installed vendor JS/CSS (jQuery, Bootstrap 4, TinyMCE, Font Awesome, etc.), referenced directly by layout/view files. `public_html/componentsold` is a legacy/unused copy — don't edit it.
