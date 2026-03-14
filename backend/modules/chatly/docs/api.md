# Chatly API

All endpoints reside under `/api/v1/chats` and require Sanctum authentication. Controllers return the module’s JSON resources.

| Method | Endpoint | Payload | Response |
| --- | --- | --- | --- |
| GET | `/` | Query params: `page`, `limit` | Paginated conversations with last message and participants (`ConversationResource`). |
| POST | `/` | JSON: `recipients[]` (URNs), optional `message` | Newly created conversation resource; seeds an initial message when provided. |
| GET | `/{conversation}` | — | Conversation with participants (requires `show` policy). |
| PATCH | `/{conversation}` | JSON: `name`, `avatar` | Updated conversation resource. |
| DELETE | `/{conversation}` | — | Confirmation payload after invoking the deletion action. |
| GET | `/{conversation}/participants` | — | List of participants via `ParticipantResource` (requires `showParticipants`). |
| POST | `/{conversation}/participants` | JSON: `urn` | Newly added participant resource. |
| DELETE | `/{conversation}/participants/{participation}` | — | Acknowledgement payload after removal. |
| GET | `/{conversation}/messages` | Query params: `page`, `limit` | Paginated messages transformed by `MessageResource`. |
| POST | `/{conversation}/messages` | JSON: `message`, `type` (`text|image|attachment`), optional `data` | Newly sent message resource. |
| DELETE | `/{conversation}/messages/{message}` | — | Confirmation payload; broadcaster notifies remaining participants when enabled. |

## Sample Request

```http
POST /api/v1/chats
Authorization: Bearer <token>
Content-Type: application/json

{
  "recipients": ["urn:user:42", "urn:team-membership:9"],
  "message": "Welcome to the project!"
}
```

## Sample Response (`201 Created`)

```json
{
  "id": 123,
  "name": null,
  "photo": null,
  "directMessage": false,
  "private": false,
  "createdAt": "2024-06-18 09:10:42",
  "updatedAt": "2024-06-18 09:10:42",
  "lastMessage": {
    "id": 456,
    "body": "Welcome to the project!",
    "type": "text",
    "data": [],
    "participantId": 789,
    "conversationId": 123,
    "sender": {
      "id": 789,
      "conversationId": 123,
      "urn": "urn:user:17",
      "userId": 17,
      "name": "Alice Example",
      "firstName": "Alice",
      "lastName": "Example",
      "email": "alice@example.com",
      "teamId": null,
      "teamMembershipId": null,
      "photo": "https://cdn.sowidu.com/avatars/17.png",
      "createdAt": "2024-06-18 09:10:42",
      "updatedAt": "2024-06-18 09:10:42"
    },
    "createdAt": "2024-06-18T09:10:42.000000Z",
    "updatedAt": "2024-06-18T09:10:42.000000Z"
  },
  "participants": [
    {
      "id": 789,
      "conversationId": 123,
      "urn": "urn:user:17",
      "userId": 17,
      "name": "Alice Example",
      "firstName": "Alice",
      "lastName": "Example",
      "email": "alice@example.com",
      "teamId": null,
      "teamMembershipId": null,
      "photo": "https://cdn.sowidu.com/avatars/17.png",
      "createdAt": "2024-06-18 09:10:42",
      "updatedAt": "2024-06-18 09:10:42"
    },
    {
      "id": 790,
      "conversationId": 123,
      "urn": "urn:team-membership:9",
      "userId": 23,
      "name": "Project Bot",
      "firstName": "Project",
      "lastName": "Bot",
      "email": "bot@example.com",
      "teamId": 5,
      "teamMembershipId": 9,
      "photo": "https://cdn.sowidu.com/avatars/23.png",
      "createdAt": "2024-06-18 09:10:42",
      "updatedAt": "2024-06-18 09:10:42"
    }
  ]
}
```

Consult `ConversationResource`, `MessageResource`, and `ParticipantResource` for the authoritative field list.
