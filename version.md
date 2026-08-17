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
