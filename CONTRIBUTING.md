# Contributing to CareerHub

This project follows the **Master → Phase → Leaf** delivery model defined in [issue #1](https://github.com/M9nx/CareerHub/issues/1).

## Workflow

1. Pick a **leaf issue** assigned to you (e.g. `[P0-M9nx]` → issue #10).
2. Branch from latest `main` — never push directly to `main`.
3. Implement **only** the files listed in the phase appendix for your WBS ID.
4. Open a PR that links the leaf issue and request reviewers from the issue metadata.

## Branch naming

```
feat/#<ISSUE_NUMBER>-<short-slug>
fix/#<ISSUE_NUMBER>-<short-slug>
chore/#<ISSUE_NUMBER>-<short-slug>
test/#<ISSUE_NUMBER>-<short-slug>
```

Example: `feat/#10-foundation-governance`

## Commits

Use [Conventional Commits](https://www.conventionalcommits.org/):

```
feat(panel): isolate super-admin Filament panel
fix(auth): enforce employer middleware on job routes
test(applications): cover employee cancel flow
```

## Pull requests

- PR title: `[WBS-ID] Short description` (e.g. `[P0-M9nx] SuperAdmin panel isolation`)
- PR body **must** include `Closes #<leaf-issue-number>`
- Fill in the PR template (WBS ID, hours, test evidence)
- Required reviewers are listed on the leaf issue
- All CI checks and Pest tests for touched areas must pass

## Code style

- Run `vendor/bin/pint --dirty` before pushing PHP changes
- Run targeted tests: `php artisan test --compact tests/Feature/Path/ToTest.php`

## Scope discipline

- Do not edit files outside your WBS file contract without approval from M9nx
- If a dependency from another leaf is not merged, record an approved handoff on the issue before proceeding

## Protected `main`

`main` requires a pull request, CODEOWNERS review, and passing checks ([Lockdown Main ruleset](https://github.com/M9nx/CareerHub/rules/22685374)).
