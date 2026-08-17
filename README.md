# Approval Workflow System

A generic "submission → reviewer approval" workflow engine (Slim 4, PostgreSQL,
Twig, i18n fa/en). The default seed data configures it as a university thesis
proposal system (submitter=student, reviewer=professor), but roles and
workflow types are data, not code — see `migrations/002_seed_reference_data.sql`.

The original 2017-era raw-PHP version of this app is kept under [`legacy/`](legacy/)
for reference during the rebuild; see [`plan.md`](plan.md) for the full
rationale and rollout plan, and [`version.md`](version.md) for a changelog.

## Dev mode setup

```bash
./setup.sh
```

This is the one-shot path: it creates `.env` from `.env.example` (generating a
random `DB_PASSWORD` if `.env` doesn't exist yet), builds and starts the
Docker Compose stack, and waits for the app to answer before printing the
URLs. Safe to re-run — it won't touch an existing `.env`.

"Dev mode" here means `APP_ENV=local` / `APP_DEBUG=true` in `.env`, which:
- disables the Twig template cache, so template edits show up on refresh
  with no rebuild;
- makes Slim's error middleware render full stack traces instead of a
  generic error page.

The whole repo is bind-mounted into the `app` container (`docker-compose.yml`),
so **PHP/template/translation changes are picked up immediately** — just
refresh the browser. You only need to rebuild (`docker compose up --build`)
when `composer.json`/`Dockerfile` change, i.e. when dependencies change.

Useful commands once it's running:

```bash
docker compose logs -f app     # tail app logs
docker compose exec app sh     # shell into the app container
docker compose down            # stop
docker compose down -v         # stop and wipe the database volume
```

## Running locally (Docker, manual steps)

```bash
cp .env.example .env    # edit DB_PASSWORD at minimum
docker compose up --build
```

- App: http://localhost:8080 (port configurable via `APP_PORT`)
- Adminer (DB browser): http://localhost:8081 (`ADMINER_PORT`), server `db`,
  user/db/password from `.env`
- Postgres schema + seed data are applied automatically on first boot from
  `migrations/*.sql` (Postgres image convention: anything in
  `/docker-entrypoint-initdb.d` runs once against an empty data volume).

## Running locally (without Docker)

Requires PHP 8.2+ with `pdo_pgsql`, Composer, and a PostgreSQL instance.

```bash
composer install
cp .env.example .env    # point DB_HOST etc at your local Postgres
psql -f migrations/001_create_schema.sql
psql -f migrations/002_seed_reference_data.sql
php -S localhost:8080 -t public
```

## Demo accounts

`migrations/003_seed_demo_accounts.sql` seeds a submitter, reviewer, and admin
account for easy local login/testing (applied automatically on first boot,
same as the other migrations):

| Role      | Username    | Password |
|-----------|-------------|----------|
| Submitter | `student`   | `123456` |
| Reviewer  | `professor` | `123456` |
| Admin     | `admin`     | `123456` |

These are for local/dev use only — never seed accounts like this against a
real deployment.

## Creating additional reviewer/admin accounts

Matching the original app's design, only submitters can self-register through
the web UI. Additional reviewer/admin accounts are created via CLI:

```bash
docker compose exec app php bin/create-actor.php reviewer prof.smith secret Jane Smith 3
docker compose exec app php bin/create-actor.php admin admin secret Site Admin
```

## Project layout

- `public/` — web root / front controller (`index.php`) + static assets
- `src/Config`, `src/Support` — DB connection, i18n translator
- `src/Repository` — PDO data access (prepared statements only)
- `src/Service` — business rules (auth, workflow transitions)
- `src/Http/Controllers`, `src/Http/Middleware` — routing layer
- `templates/` — Twig templates (auto-escaped)
- `translations/fa.php`, `translations/en.php` — UI strings
- `migrations/` — versioned SQL schema + seed data
- `legacy/` — the original pre-rewrite PHP app, kept for reference only
