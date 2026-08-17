# Modernization Plan — Proposal Acceptance System

## 1. What this project is

A Persian-language university workflow app (originally `21-uni-project`, ~2017/1396 era PHP).
Three roles, session-based:

- **Student** — registers, submits a thesis proposal to one or more chosen professors
  (`student/submit-proposal.php`), tracks status (`tracking_proposal.php`).
- **Professor** — sees pending proposals assigned to them, accepts one
  (`accept-proposal-process.php`) or sends back a correction/"corrigendum"
  (`proposal-corigendum.php`), manages capacity.
- **Admin** — role exists in the login switch but `admin/admin.php` is literally
  `echo 'hi im admin';`. Never implemented.

Stack: raw PHP (procedural entry points + a small hand-rolled OOP class layer:
`member` → `student`/`professor`/`adminClass`), PDO against MySQL, no framework,
no Composer, no build step. Front end is plain HTML/CSS + vendored jQuery/TinyMCE/
Font Awesome, RTL Persian UI.

## 2. Problems found (why this needs work, not just a Dockerfile)

**Security — the serious stuff**
- SQL injection throughout: almost every query in `class/*.php` interpolates
  `$this->username`/`$this->password`/IDs directly into SQL strings
  (`class/student-class.php`, `class/professor-class.php`, `class/proposal-class.php`,
  `class/member-class.php`), even though PDO prepared statements are used *elsewhere*
  in the same files. Login queries are the worst case — an attacker controls the
  string that decides authentication.
- Passwords stored and compared as plaintext (`password = '$this->password'` in every
  login query). No hashing anywhere.
- Zero output escaping — proposal titles/content/names are `echo`'d raw into HTML
  in `class/proposal-class.php` and `class/student-class.php` → stored XSS.
- No CSRF protection on any state-changing form (login, register, submit proposal,
  accept proposal, corrigendum, delete proposal).
- State-changing action via bare GET with no ownership/role check:
  `professor/accept-proposal-process.php` accepts `$_GET['proposal']` and
  `$_SESSION['user_id']` with no verification the proposal was ever assigned to
  that professor.
- DB credentials committed in plaintext in `connection/conn.php`
  (`masoud` / `111qweasdzxc`), checked into git history.
- No `session_regenerate_id()` on login (session fixation).
- `class/member-class.php::member_login()` is dead/misleading code: it always
  queries `t_student` regardless of the role passed in, and runs the same login
  query twice (once prepared, once raw) for no reason — this method appears unused
  by any real login path but is confusing and unsafe if it ever gets called.

**Portability / config**
- Hardcoded absolute Windows paths (`C:\xampp\htdocs\21-uni-project/...`) in
  `functions.php`, `inc/head.php`, `connection/login-process.php`,
  `connection/register-process.php`, and others — the app cannot run anywhere
  except that one machine's XAMPP install as currently written.
- No `.env` / config abstraction — DB host, name, user, password are hardcoded twice
  (`$con` for `uni_pp`, `$con2` for `uni_pp2`).
- `inc/head.php` hotlinks a favicon from `piau.ac.ir` (a real third-party
  university's domain) — should never have been pointed at an external live site.

**Data layer**
- Two separate databases (`uni_pp`, `uni_pp2`) used interchangeably in ways that
  don't obviously map to a reason (e.g. student/professor login uses `$con2`,
  admin uses `$con`) — looks like leftover experimentation rather than a design.
- **No schema file exists in the repo at all.** Table/column names (`student`,
  `professor`, `propozal`, `proposal_pending`, `t_student`, columns like `tittle`,
  `field_lvl`, `day_present`) are only recoverable by reading every query. Naming
  is inconsistent (`propozal` vs `proposal`, `tittle` vs `title`).
- No foreign keys evident from the queries; referential integrity is done by hand
  in PHP loops.

**Code quality**
- Model classes (`proposal`, `student`, `professor`) directly `echo` HTML —
  no separation between data access and presentation.
- Duplicate/dead queries (e.g. `member_login` runs the login query twice).
- Errors are swallowed and replaced with generic `echo` messages; nothing is
  logged.
- No input validation (e.g. `register-process.php` trusts `$_POST['field_level']`
  and other fields as-is).

**Repo hygiene**
- `css/PHP Login and Registration Script with PDO and OOP _ Coding Cage_files/`
  is an entire saved tutorial webpage (~140 files: ad-tech JS, tracking pixels,
  unrelated images, HTML snapshots) that has nothing to do with this app and
  should never have been committed. Delete it.
- Vendored `js/tinymce/`, `css/font-awesome-4.7.0/` checked into git instead of
  pulled via a package manager or CDN.
- No `README.md`, no `composer.json`, no tests, no CI, no `.env.example`.
- `doc.pdf` sits at repo root, undocumented (likely the original assignment spec —
  worth checking before deciding to keep/remove).

## 3. Target shape

**Baseline principle:** get the app running in Docker *before* refactoring it, so
there's always a working checkpoint to compare against — not a big-bang rewrite
landing all at once.

- **PHP 8.x**, Composer-managed, PSR-4 autoloading replacing the manual
  `include_once` chain.
- **Fix every security item in §2** as its own reviewable step: prepared
  statements everywhere, `password_hash`/`password_verify`, CSRF tokens,
  consistent output escaping (or a templating engine like Twig that
  auto-escapes), role/ownership checks on every state-changing endpoint,
  `session_regenerate_id()` on login.
- **One database**, schema captured as versioned SQL migrations in the repo
  (currently zero source of truth for schema — this needs to be reconstructed
  from the queries and confirmed against your real DB dump if you still have one).
- **Config via environment variables** (`.env`, not committed), read through
  `docker-compose.yml` — no more hardcoded paths or credentials.
- **Docker**: multi-stage `Dockerfile` (PHP-FPM + nginx, or Apache — nginx+FPM
  recommended), `docker-compose.yml` with `app` + `mysql` + `adminer` for local
  dev, healthchecks, named volume for DB data, `.env.example` committed instead
  of real secrets.
- **Repo cleanup**: delete the junk tutorial folder under `css/`, move
  TinyMCE/Font Awesome to Composer/npm or CDN, add `README.md`, tighten
  `.gitignore` (`vendor/`, `node_modules/`, `.env`).

## 4. Decisions (locked in 2026-08-17)

1. **Multi-language** → i18n only: Persian + English UI via translation files,
   single codebase.
2. **Modernization depth** → Lightweight: Slim (PSR-7/15) router + our own
   structure, not a full framework.
3. **Multi-purpose** → Generic workflow engine: domain is genericized to
   "submission → reviewer approval" (config-driven roles/workflow types), not
   hardcoded to students/professors. The university proposal flow becomes one
   configuration of the generic engine, not the only thing it can model.
4. **Database** → PostgreSQL (schema rebuilt from scratch as migrations, so no
   MySQL legacy-format constraint).
5. **`doc.pdf`** → not yet decided; left in place at repo root for now.

Because the target domain model (generic submissions/reviewers/workflow types)
doesn't map cleanly onto patching the old hardcoded student/professor/admin
code in place, this is a fresh build rather than an in-place patch. The
original app's code is preserved under `legacy/` for reference/parity-checking
during the rebuild, not deleted.

## 5. Target architecture

- **Framework**: Slim 4 (PSR-7/15), Twig for templates (auto-escaping — kills
  the XSS problem by construction), `slim/csrf` for CSRF protection,
  `vlucas/phpdotenv` for env config. Composer-managed, PSR-4 autoload under
  `src/`.
- **Domain** (generic, config-driven):
  - `roles` — e.g. `submitter`, `reviewer`, `admin` (labels in fa/en).
  - `actors` — a user with a role; a JSONB `profile` column holds role-specific
    extra fields (e.g. `field_level` for a submitter, `present_day` for a
    reviewer) instead of hardcoded columns per role.
  - `workflow_types` — e.g. `thesis_proposal`; what kind of submission this is.
  - `submissions` — title/content/status, belongs to a submitter + workflow type.
  - `submission_reviewers` — assignment of a submission to one or more
    reviewers, each with their own `decision` (pending / changes_requested /
    approved / withdrawn) and comment. This directly replaces
    `proposal_pending` + the ad-hoc corrigendum column.
- **Auth**: `password_hash`/`password_verify`, session regeneration on login,
  role + ownership middleware guarding every state-changing route.
- **Data access**: PDO with prepared statements everywhere (no string
  interpolation into SQL, ever).
- **i18n**: `fa` (default, RTL) and `en`, simple array-based translation files
  keyed by string id.
- **Docker**: `docker-compose.yml` with `app` (PHP-FPM + nginx) + `postgres` +
  `adminer` for local dev; `.env.example` committed, real `.env` gitignored.

## 6. Rollout phases

1. **Repo hygiene** — remove the junk tutorial folder under `css/`; move the
   entire legacy app into `legacy/` (kept for reference, not deleted).
2. **New app skeleton** — Composer, Slim bootstrap, Docker Compose (app +
   Postgres + adminer), Postgres migrations for the generic schema above.
3. **Auth + roles** — register/login/logout, hashed passwords, CSRF, session
   regen, role middleware.
4. **Core workflow** — submitter creates a submission and assigns reviewers;
   reviewer sees assigned submissions, approves or requests changes with a
   comment; submitter tracks status. This is full parity with the old
   student/professor proposal flow, expressed generically.
5. **Admin** — minimal real admin views (actor/submission overview) replacing
   the old `echo 'hi im admin'` stub.
6. **i18n** — fa/en translation files wired through every screen built above.
7. **Tests + CI** — at minimum smoke tests for auth and the core workflow, plus
   a GitHub Actions job running them.

Each phase lands as its own reviewable slice rather than one giant commit;
`version.md` gets an entry per meaningful change (see project convention).

---
*Built on branch `2026dev`. Nothing is committed without explicit approval —
see repo conventions.*
