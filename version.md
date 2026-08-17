# Version History

This file logs every change made to this project going forward, paired with a
version number. Once the app has a real settings/config layer (see `plan.md`),
the same version number will also be surfaced in-app.

## Unreleased

- `0.1.0` — Initial assessment: wrote `plan.md` documenting current architecture,
  security/hygiene problems, and open decisions for the Docker/multi-language
  rework. No application code changed yet. Branch: `2026dev`.
- `0.2.0` — Locked in modernization decisions in `plan.md`: i18n-only
  multi-language, lightweight Slim framework, a generic "submission → reviewer
  approval" workflow engine domain instead of a university-specific rewrite,
  and PostgreSQL. Revised the rollout plan accordingly. Also reflects the
  repo hygiene done in the previous commit (legacy app moved to `legacy/`,
  junk tutorial webpage removed).
- `0.3.0` — New application skeleton: Composer (Slim 4, Twig, slim/csrf,
  phpdotenv), Docker Compose (app + nginx + PostgreSQL + adminer), Postgres
  migrations for the generic schema (`roles`, `actors`, `workflow_types`,
  `submissions`, `submission_reviewers`), seeded with the default
  submitter/reviewer/admin + thesis-proposal configuration. `.env`/`vendor/`/
  `var/` added to `.gitignore`.
- `0.4.0` — Implemented the core workflow backend: hashed-password auth with
  CSRF protection and role/ownership middleware (fixes the SQL injection,
  plaintext-password, missing-CSRF, and unauthenticated-GET issues from the
  legacy app), submitter create-submission + reviewer-selection flow, reviewer
  approve/request-changes flow (replacing the old accept/corrigendum pages), a
  real admin overview (replacing the `echo 'hi im admin'` stub), and
  `bin/create-actor.php` for creating reviewer/admin accounts (matching the
  legacy app's design where only submitters self-register).
- `0.5.0` — Added Twig templates, fa/en translations, and app styling for
  every screen (Twig's auto-escaping fixes the legacy XSS issues by
  construction), the `VERSION` file surfaced in the footer and admin
  dashboard, and the project `README.md`. Verified the whole stack end to
  end in a live Docker Compose run: register → login (submitter/reviewer/
  admin) → create submission → approve → reviewer-capacity decrement, CSRF
  protection on every form, role-guard 403s for cross-role access and
  302-to-login for anonymous access, and fa/en locale switching — all
  confirmed working against the real containers.
- `0.6.0` — Added `setup.sh` (one-shot dev bootstrap: generates `.env`,
  builds/starts the stack, waits for health) and a "Dev mode setup" section
  in `README.md` explaining live code reload via the bind mount, when a
  rebuild is actually needed, and the day-to-day Compose commands. Added
  `.gitattributes` forcing LF line endings for `*.sh` so a future checkout
  under this repo's `core.autocrlf` setting can't corrupt the shebang line.
- `0.6.1` — Added `migrations/003_seed_demo_accounts.sql`, seeding a
  `student`/`professor`/`admin` account (all password `123456`) for easy
  local login/testing across all three roles, and documented them in a new
  "Demo accounts" README section. Verified all three actually log in against
  the live dev stack.
- `0.7.0` — The fa/en locale choice is now remembered via a dedicated
  long-lived cookie instead of only the PHP session, so it survives closing
  the browser and doesn't depend on being logged in.
- `0.7.1` — Removed the Islamic Azad University branded header image and
  favicon inherited from the legacy app's assets (the app is meant to be
  generic, not tied to one institution) - replaced with a plain generic
  checkmark mark/favicon. Also polished the auth forms and nav: visible
  field labels instead of placeholder-only inputs, `autocomplete` attributes,
  a responsive/wrapping topbar, hover/focus states, and a scrollable wrapper
  around wide tables for small screens.
