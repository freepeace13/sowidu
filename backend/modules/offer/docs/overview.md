# Offer Module Overview

The Offer module enables sales teams to draft, revise, and finalise offers before turning them into downstream orders. It integrates calculations, policy checks, notifications, and UI composition in one package.

## Core Flow
1. Controllers in `src/Controllers` validate requests and delegate to actions or agents.
2. `OfferService` and `Support\OfferCalculationService` compute totals, taxes, and derived values.
3. Events and listeners broadcast lifecycle changes (history tracking, notifications, cache updates).
4. Policies and middleware guard access, while Inertia renders the offer workspace.

## Important Concepts
- **Offer**: Aggregate root representing the commercial proposal, including items, pricing, and status.
- **OfferItem**: Line items associated with an offer used for calculations and downstream order data.
- **History & Actions**: Audit trail that records user activity using `OfferActionType` and dedicated events.

## Extension Points
- Add new calculations inside `Support\OfferCalculationService` and cover them with module tests.
- Introduce orchestration logic in `src/Agents` and document it in `../agents.md`.
- Use the `docs/` folder for deep dives (e.g., pricing strategy, integration diagrams, ADRs).

Keep this overview updated whenever the offer lifecycle or integrations change so new contributors can ramp up quickly.
