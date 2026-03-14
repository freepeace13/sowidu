# Integration Points

## Authentication and Authorization
- API routes run behind `auth:sanctum`.
- Web routes add `auth`, `permission:` middleware for `Permissions::CAN_ACCESS_CHAT`, and `HandleInertiaRequests`.
- Actions and controllers check abilities through Laravel gates (e.g., `sendMessage`, `addParticipants`, `show`).

## Shared Models and Transformers
- Module models extend `Musonza\Chat` base classes but Sowidu also uses `App\Models\ChatParticipation` for enriched participation data.
- Transformers from `App\Transformers\ConversationTransformer` and `MessageTransformer` are referenced inside repository providers to shape payloads for the SPA.

## Cross-cutting Packages
- `Packages\RestApi` supplies the base action (`RestApiAction`), controller (`RestfulController`), and JSON resource helpers.
- `Packages\Urn\UrnManager` resolves URN strings into actual user/team models, keeping identifiers consistent across modules.

## Eventing and Broadcasting
- Message providers wire in `ChatBroadcaster`; when `chatly.broadcasts` is enabled the broadcaster triggers the app’s configured broadcast driver.

## Front-end Collaboration
- `vite.config.mjs` maps `@Chatly` to module assets, so other modules (e.g., Todo) can reuse attachment viewers and other shared UI primitives.

## Service Container Bindings
- `App\Providers\ApiServiceProvider` binds top-level contracts (`App\Contracts\Actions\CreatesMessages`, etc.) to Chatly implementations, allowing other modules to depend on interfaces instead of concrete classes from Chatly.
