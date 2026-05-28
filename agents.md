# agents.md — twofactor_backup_codes

## Repository Overview

An ownCloud Server (OC10) app providing backup recovery codes for two-factor authentication. Allows users to recover account access when they lose their second-factor device.

- **Classification:** Classic (OC10)
- **Activity Status:** Active
- **License:** GPL-3.0
- **Language:** PHP, JavaScript (Starlark listed for CI)

## Architecture & Key Paths

- `appinfo/` — ownCloud app metadata (info.xml, routes)
- `lib/` — PHP backend (backup code generation, verification)
- `js/` — Frontend JavaScript
- `l10n/` — Localization/translation files
- `templates/` — PHP templates for UI
- `screenshots/` — UI screenshots
- `tests/` — PHPUnit test suites
- `Makefile` — Build and test orchestration
- `composer.json` — PHP dependencies
- `phpunit.xml` — PHPUnit configuration

## Development Conventions

- Standard ownCloud OC10 app structure
- Code style enforced by phpcs
- GitHub Actions CI

## Build & Test Commands

```bash
make all                    # Install all dependencies
make dist                   # Build distribution package
make clean                  # Clean build artifacts
make test-php-unit          # Run PHP unit tests
make test-php-style         # Check code style
make test-php-style-fix     # Auto-fix style issues
make test-php-phan          # Run Phan static analysis
make test-php-phpstan       # Run PHPStan static analysis
make test-acceptance-api    # Run API acceptance tests
make test-acceptance-cli    # Run CLI acceptance tests
make test-acceptance-webui  # Run webUI acceptance tests
```

## Important Constraints

- **GPL-3.0 copyleft license:** This repository is GPL-3.0. The OSPO Apache 2.0 migration requires auditing copyleft dependencies and contributor agreements.
- **Companion app:** Works alongside `twofactor_totp` for a complete 2FA solution.
- **Security-sensitive:** Handles authentication recovery codes -- changes must be carefully reviewed.


## OSPO Policy Constraints

### GitHub Actions
- **Only** use actions owned by `owncloud`, created by GitHub (`actions/*`), verified on the GitHub Marketplace, or verified by the ownCloud Maintainers.
- Pin all actions to their full commit SHA (not tags): `uses: actions/checkout@<SHA> # vX.Y.Z`
- Never introduce actions from unverified third parties.

### Dependency Management
- Dependabot is configured for automated dependency updates.
- Review and merge Dependabot PRs as part of regular maintenance.
- Do not introduce new dependencies without discussion in an issue first.

### Git Workflow
- **Rebase policy**: Always rebase; never create merge commits. Use `git pull --rebase` and `git rebase` before pushing.
- **Signed commits**: All commits **must** be PGP/GPG signed (`git commit -S -s`).
- **DCO sign-off**: Every commit needs a `Signed-off-by` line (`git commit -s`).
- **Conventional Commits & Squash Merge**: Use the [Conventional Commits](https://www.conventionalcommits.org/) format where the repository enforces it. Many repos use squash merge, where the PR title becomes the commit message on the default branch — apply Conventional Commits format to PR titles as well. A reusable GitHub Actions workflow enforces this.

## Context for AI Agents

- This is an ownCloud Server (OC10) app, not an oCIS extension.
- The `lib/` directory contains backup code generation and verification logic.
- The `l10n/` directory contains translations for the UI.
- Works in conjunction with the `twofactor_totp` app.
