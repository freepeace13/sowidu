# Offer Agents

Agents coordinate multi-step offer workflows, keeping controllers, jobs, and listeners thin while reusing actions and services.

## Existing Agents
- _None documented yet_: add entries here as agents are introduced (for example, coordinating offer approval with notifications and order generation).

## Authoring Guidelines
- Store agents under `src/Agents/` (create the directory if it does not already exist).
- Use explicit entrypoints such as `run`, `handle`, or `execute`, and document accepted payloads and expected side effects.
- Compose existing actions (`src/Actions`) and services (`OfferService`, repositories) to avoid duplicating business logic.
- Cover branching logic with module tests (`modules/offer/tests`) to protect offer state transitions.

## Usage Scenarios
- Automating status transitions when an offer is accepted or rejected, including financial recalculations and notifications.
- Preparing downstream order payloads by orchestrating calculations, history logging, and address lookups.
- Handling background processes such as scheduled reminders or tax recalculations.

Remember to update this document whenever agents change so downstream teams can quickly understand available orchestration flows.
