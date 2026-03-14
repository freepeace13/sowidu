# Using Chatly

## Enable Web Routes (Optional)
Set `chatly.should_load_routes` to `true` to mount the module’s web routes under the configured prefix (defaults to `chatly`). These routes expect authenticated users who possess the `Permissions::CAN_ACCESS_CHAT` ability and will render Inertia-powered views.

## Invoke Actions Programmatically
Resolve invokable actions from the container and pass the authenticated participant:

```php
use Modules\Chatly\Actions\CreateConversation;

$conversation = app(CreateConversation::class)->create($actingUser, [
    'recipients' => ['urn:user:42'],
    'message' => 'Hello there!',
]);
```

`CreateMessage`, `AddParticipant`, `RemoveParticipant`, and their companions use consistent validation and authorization workflows so they can be reused in jobs, controllers, or agents.

## Work with the Chat Repository
`Modules\Chatly\Repositories\ChatRepository` chains higher-level operations:

```php
$chat = app(ChatRepository::class)->setRequest($request);

$thread = $chat->conversation($conversationId);
$messages = $thread->messages();      // `MessageProvider`
$thread->readAll();                   // marks unread messages as read
```

Providers encapsulate pagination, attachment processing, broadcasting, and impersonation helpers for admin tooling.

## Expose HTTP APIs
Routes under `routes/api/v1/chats.php` map Sanctum-authenticated endpoints to module controllers. Responses are already transformed by module resources and align with the platform-wide `Packages\RestApi` structure.

## Front-end Consumption
Vue components, mixins, and partials live beneath `modules/chatly/resources/js`. Import them using the Vite alias:

```js
import ChatsList from '@Chatly/Partials/ChatsList.vue'
```

This keeps module assets encapsulated while still accessible to other areas of the Sowidu SPA.
