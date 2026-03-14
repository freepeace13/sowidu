# Chatly Agents

This guide is for both AI automation and newly onboarded developers. It spells out how Chatly orchestrates multi-step workflows and how to extend those flows safely.

## Overview
Agents provide high-level orchestration for complex chat workflows while delegating individual operations to the `Actions` layer and to service contracts exposed by other modules.

## Existing Agents
- _None yet_: introduce an agent when you need to coordinate multiple actions (for example, when creating a conversation with initial participants and broadcasting events).

## Authoring Guidelines
- Place new agents under `src/Agents/` (create the directory if it does not exist).
- Keep agent APIs explicit—expose entrypoints such as `run`, `handle`, or `execute` so that controllers, jobs, or other modules can interact with them predictably.
- Inject dependencies (repositories, actions, external services) through the constructor to simplify testing.
- Depend on service contracts (interfaces) published by other modules when cross-module calls are required.
- **Use external contracts** from `src/Contracts/External/` instead of directly importing main app classes (User, Employee, etc.).
- Cover branching logic with PHPUnit feature tests or module-level tests in `tests/`.

## External System Contracts
Chatly defines **outgoing ports** (interfaces) for all external dependencies in `src/Contracts/External/`:
- `UserSearchContract` - Search users and team members
- `UrnResolverContract` - Resolve URN identifiers
- `MediaManagerContract` - File/media management
- `AuthorizationContract` - Permission checking
- `BroadcasterContract` - Real-time event broadcasting
- `UserDisplayContract` - Avatar and display name resolution

See [docs/external-contracts.md](docs/external-contracts.md) for implementation details.

## Coding Standards
- Action classes must always live in `src/Actions/`; avoid embedding orchestration or side effects inside agents themselves.
- Prefer small, composable actions to keep agents focused on sequencing work.
- Document any long-running or stateful behaviour directly in the agent class via concise code comments where intent is not obvious.

## Example
```php
namespace Modules\Chatly\Agents;

use Modules\Chatly\Actions\CreateConversation;
use Modules\Chatly\Actions\AddParticipant;
use Modules\Chatly\Contracts\Notifications\ConversationNotifier;

class CreateConversationAgent
{
    public function __construct(
        protected CreateConversation $createConversation,
        protected AddParticipant $addParticipant,
        protected ConversationNotifier $notifier,
    ) {}

    public function run(array $payload): void
    {
        $conversation = ($this->createConversation)($payload['topic'], $payload['owner_id']);

        foreach ($payload['participant_ids'] as $participantId) {
            ($this->addParticipant)($conversation, $participantId);
        }

        $this->notifier->notifyCreated($conversation);
    }
}
```
- Register the notifier interface binding inside `ChatlyServiceProvider` so Laravel resolves the concrete implementation.
- Keep `run` thin by pushing validation into requests or value objects.

## Documentation Organization

### Documentation Structure
Follow these guidelines for organizing documentation:

#### Module-Specific Documentation
Place in `modules/chatly/docs/`:
- Module architecture and design
- Module features and capabilities
- API documentation for module endpoints
- Module-specific testing guides
- Internal module workflows

#### Main App Integration Documentation  
Place in `docs/chatly/` (root docs folder):
- Service provider setup
- Adapter implementations (in main app)
- Integration steps with main application
- Main app configuration for Chatly

#### Shared/Cross-Cutting Documentation
Place in root `docs/`:
- Project-wide standards
- Overall architecture decisions
- Cross-module patterns
- General development guidelines

### Documentation Guidelines
- Use this `agents.md` strictly for agent summaries, cross-module contracts, and quick-start examples.
- Maintain deeper explanations, diagrams, and ADRs under `docs/` (for module-specific: `modules/chatly/docs/`).
- Update both this file and relevant docs whenever AI-assisted or manual changes modify agent behaviour.
- When creating new documentation, always place it in the appropriate `docs/` folder based on scope.
- Cross-reference documentation between main app and module as needed.

## When To Use Agents
- Orchestrating multi-step chat flows that touch several actions (e.g., notifying participants after message delivery).
- Coordinating cross-module behaviour (chat + offers) while keeping domain rules encapsulated.
- Implementing background workflows where a queue job should delegate to a reusable coordinator.

Update this document whenever agents are added or modified so new contributors can understand the orchestration surface quickly.
