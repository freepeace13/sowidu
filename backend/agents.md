# Agents

Agents coordinate multi-step workflows by orchestrating actions, domain services, and external integrations. They encapsulate decision-making so controllers, jobs, and command handlers stay thin.

## Responsibilities
- Translate user or system intent into a series of actions and service calls.
- Delegate side effects to the appropriate action classes, repositories, or events.
- Maintain minimal state; rely on dependency injection for persistence and integrations.

## Implementation Guidelines
- Place agent classes within the owning module (for example `modules/<feature>/src/Agents`).
- Expose an explicit entrypoint such as `run`, `handle`, or `execute` and document arguments, outputs, and failure modes.
- Prefer composing existing actions instead of embedding business logic directly in agents.
- Cover branching or long-running workflows with focused tests (module tests, feature tests, or contract tests).

## Coding Standards
- Action classes must reside in `src/Actions/`; keep orchestration in agents and single-responsibility logic in actions.
- Bind implementations of cross-module service contracts inside the providing module's service provider.
- Use constructor injection for dependencies to keep agents testable and predictable.

## Cross-Module Communication
- Inter-module collaboration happens through service contracts (interfaces) published by the providing module.
- Depend on the contract, not the concrete class—inject the interface so implementations can evolve without breaking consumers.
- Register implementations inside the owning module's service provider to keep bindings discoverable and testable.
- When agents span modules, document the expected interface in each module's `agents.md` and link to the contract definition in `docs/`.

## Documentation Expectations
- Use this root `agents.md` as the index for shared guidance and patterns.
- Keep module-specific details inside each module's `agents.md`; reserve the module `docs/` folder for deeper write-ups, diagrams, and ADRs.
- Update the relevant README, `agents.md`, and `docs/` entries whenever AI-assisted or manual changes adjust agent behaviour.

## AI / Cursor Protocol
- Before taking any action, drafting code, or responding to prompts, review the root-level `docs/*` resources that apply to the task so you understand global conventions and constraints.
- After confirming repo-wide guidance, drill into the affected module or package and read its `docs/*` content (starting with its index such as `docs/module-guide.md`) before touching nested documentation or code.
- If multiple topics might apply, traverse the documentation map to locate the correct deep dive before proceeding.
- Only continue once the documentation context has been considered; note any discrepancies or missing guidance back in the docs.

## Laravel Sail Commands
**IMPORTANT**: This project uses Laravel Sail for local development. All composer, artisan, and PHP commands MUST be executed through Sail:
- ✅ `./vendor/bin/sail composer install`
- ✅ `./vendor/bin/sail artisan migrate`
- ✅ `./vendor/bin/sail php --version`
- ❌ `composer install` (direct execution)
- ❌ `php artisan migrate` (direct execution)

Always prefix commands with `./vendor/bin/sail` or use the `sail` alias if configured.

## Related Concepts
- `Actions/*`: source of reusable operations agents orchestrate.
- `modules/*`: primary home for feature-specific agents and their documentation.
- `packages/*`: host shared agents only when they are truly application-agnostic.
