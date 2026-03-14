# Directory Structure Guide

Start here before adding or reshaping code. It outlines the layout conventions that keep the monorepo understandable.

## modules/*
- Core feature areas of the product, each structured like a standalone Laravel package (service provider, routes, config, migrations, tests).
- Every module must include:
  - `README.md` describing its purpose, key workflows, and directory map.
  - `agents.md` documenting orchestration surfaces exposed by the module.
  - `docs/` folder for deeper guides (architecture notes, workflows, ADRs, etc.).
- Use modules to encapsulate domain logic that powers the primary Sowidu experience.

### modules/*/docs
- Acts as the module’s knowledge base: break down monolithic READMEs into topic-specific guides (features, usage, API, schema, ADRs).
- Include an index or map (e.g., `module-guide.md`) that links to the sub-guides so contributors can navigate quickly.
- Keep endpoints, workflows, and database schema explanations here—update them alongside behavioural code changes.

## packages/*
- Reusable libraries and tooling shared across modules or other applications.
- Mirrors Laravel package conventions but should avoid hard coupling to module-specific code.
- Promote functionality into `packages/` when it becomes generic enough to reuse or open-source.

## features/*
- Soon to be removed.
- All files inside it is unused and will be deleted soon.

## Actions/*
- Repository of small, focused classes that perform a single operation.
- Keep every action class inside the module or package `src/Actions/` directory—never scatter them across controllers or agents.
- Actions should be invokable or expose a clear `handle`/`execute` method and remain side-effect aware.
- Keep orchestration logic inside modules; actions stay composable so modules and packages can reuse them.

## Documentation Expectations
- Add module or package level READMEs, agent docs, and supporting guides when introducing new features.
- Link newly written deep-dive docs from the relevant module/package `docs/` index so contributors can discover them.
- Keep this file up to date when structural conventions change.
- Whenever AI-assisted changes touch behaviour, ensure the corresponding module README, `agents.md`, and `docs/` content are revised before closing the task.

## Contribution Checklist
- Confirm whether functionality belongs in `modules/` or `packages/` before scaffolding.
- Reach for an action when you need a straightforward, reusable operation.
- Audit the module docs (README, `agents.md`, `docs/`) before merging to ensure they describe new workflows or breaking changes.
