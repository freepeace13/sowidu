# Offer Module

The Offer module manages generation, lifecycle, and delivery of commercial offers inside Sowidu. It owns the UI surface, calculations, history, and integrations required to convert offers into downstream orders.

## Key Capabilities
- Offer authoring and editing via Inertia-powered views registered by `OfferServiceProvider`.
- Status transitions (draft, pending, accepted, rejected, cancelled) with command bus support for batch operations.
- Financial calculations and tax handling through `Support\OfferCalculationService` and related actions.
- History tracking, notifications, and policy enforcement using events, listeners, and `OfferPolicy`.

## Directory Guide
- `resources/`: Inertia layouts, Vue pages, and Blade templates for the offer experience.
- `routes/`: HTTP routes automatically loaded by the service provider.
- `src/Actions/`: Focused operations for manipulating offers, items, and totals.
- `src/Console/`: Artisan commands that support migrations, defaults, and cleanup tasks.
- `src/Events/` & `src/Listeners/`: Domain events fired during offer lifecycle transitions.
- `src/Mail/` & `src/Notifications/`: Outbound messaging triggered by offer updates.
- `src/Policies/`: Authorization checks for offer actions.
- `tests/`: Module-level test suite covering calculations, repositories, and service orchestration.

## Configuration & Bootstrapping
- Views are namespaced under `offer::` and the module sets the Inertia root view to `offer::app`.
- Policies are registered with Laravel's gate for `Modules\Offer\Models\Offer`.
- Console-only commands registered when running in CLI context; ensure the service provider is loaded during Artisan execution.

## Development Checklist
1. Update or add actions when introducing new business rules—controllers should delegate rather than embed logic.
2. Run module tests (`phpunit --testsuite offer`) before pushing changes that touch calculations or policies.
3. Document behavioural changes in `agents.md` and the `docs/` folder; keep this README aligned with the module's architecture.
