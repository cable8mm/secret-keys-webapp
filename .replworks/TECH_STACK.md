# TECH_STACK

## 1. Languages / runtimes / frameworks / libraries

- PHP `^8.4`
- Node.js current LTS (verify)
- Laravel `^13.0`
- Filament `^5.0` (verify)
- Livewire `^4.0` (verify)
- Tailwind CSS `^4.0`
- SQLite (database engine bundled with PHP)
- Vite with `laravel-vite-plugin` at the version required by the Laravel 13 skeleton (verify)
- Composer (PHP dependency manager)
- npm (JS dependency manager)
- Pest `^4.0` (verify)
- PCOV (coverage driver)
- Larastan `^3.0` (verify)
- Laravel Pint (formatter, version required by the Laravel 13 skeleton)
- GitHub Actions (CI)

## 2. Framework- or tool-mandated paths and file names only

- `composer.json`, `composer.lock`
- `package.json`, `package-lock.json`
- `vite.config.js`
- `.env`, `.env.example`
- `config/`
- `database/migrations/`
- `database/database.sqlite`
- `app/Models/`
- `app/Providers/Filament/` (verify)
- `app/Filament/` (verify)
- `resources/css/`, `resources/js/`
- `tests/Feature/`, `tests/Unit/`, `tests/Pest.php`
- `phpunit.xml`
- `phpstan.neon`
- `pint.json`
- `.github/workflows/`
- `LICENSE`

## 3. Coding conventions specific to the chosen stack

- PHP code MUST pass Laravel Pint with the default Laravel preset.
- UI MUST be built with Filament components.
- UI MUST NOT use any other UI component library.
- Interactive behavior MUST be implemented with Livewire.
- A separate JavaScript framework MUST NOT be added.
- Styling MUST use Tailwind CSS utility classes.
- Tailwind CSS MUST be imported with a CSS `@import` statement.
- Tailwind CSS v3 directives (`@tailwind base`, `@tailwind components`, `@tailwind utilities`) MUST NOT be used.
- Tailwind CSS utilities removed in v4 MUST NOT be used.
- Tailwind CSS MUST be built through Vite.
- Database access MUST use Eloquent models.
- Database schema changes MUST be made through Laravel migrations.

## 4. Testing rules

- Tests MUST use Pest.
- Test files MUST be located in `tests/Feature/` or `tests/Unit/`.
- Test file names MUST end with `Test.php`.
- UI tests MUST use the Livewire and Filament testing helpers.
- Tests MUST run against an in-memory SQLite database.
- Coverage MUST be measured with PCOV through `pest --coverage`.
- Coverage MUST be at least 70%.
- Larastan MUST report zero errors with the configuration in `phpstan.neon`.
- `pint --test` MUST report no violations.

## 5. Configuration rules

- Settings MUST be defined in `.env` and read through `config/*.php`.
- `env()` MUST NOT be called outside `config/`.
- `.env` MUST be git-ignored.
- `.env.example` MUST list every setting key without values.
- `DB_CONNECTION` MUST be `sqlite`.
- `APP_KEY` MUST be set.
- `APP_KEY` MUST NOT be committed.
- `APP_KEY` MUST be backed up separately from `database/database.sqlite`.
- The Filament panel MUST be configured without a login page.
- The Filament panel MUST be configured without authentication middleware.

## 6. Build, packaging, and release rules

- Dependencies MUST be installed with `composer install` and `npm ci`.
- `composer.lock` and `package-lock.json` MUST be committed.
- The database MUST be initialized with `php artisan migrate`.
- Development assets MUST be served with `npm run dev`.
- Production assets MUST be built with `npm run build`.
- The product MUST be started with `php artisan serve`.
- A packaged release artifact MUST NOT be produced.
- The product MUST be run from a source checkout.
- The CI workflow MUST be located in `.github/workflows/`.
- The CI workflow MUST run `composer install`, `npm ci`, `npm run build`, `pint --test`, Larastan, and `pest --coverage --min=70`.
- The license MUST be MIT.
- `LICENSE` MUST exist at the repository root.
- The `license` field in `composer.json` MUST be `MIT`.

## 7. Security rules tied to the chosen stack

- The server MUST bind to `127.0.0.1` only.
- The server MUST NOT be exposed through a proxy or tunnel.
- The Value column MUST use the Laravel `encrypted` cast.
- The Value MUST NOT be stored in a plain text column.
- Values MUST NOT be written to Laravel logs.
- `APP_DEBUG` MUST be `false` in any run that holds real Values.
- `database/database.sqlite` MUST be git-ignored.

## 8. Non-goals (technologies and tools explicitly excluded)

- Flux UI, free and Pro, MUST NOT be used.
- A login page or authentication system MUST NOT be used.
- Tailwind CSS v3 MUST NOT be used.
