# Todos Module

> Agent: [.claude/agents/todos.md](/.claude/agents/todos.md)

Kanban board and task management system.

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 0/32 | 32 `use App\` violations to fix |
| Adapters | 0/? | None created |
| Tests | 0 | No tests |
| Vite config | Complete | todos.vite.mjs exists |
| Frontend | Partial | Some pages in main app |

**Last updated**: 2025-01-15 - Initial tracking

## Status: Partial

- [x] Service provider (multiple providers pattern)
- [ ] External contracts needed
- [ ] Routes in main app (need migration)
- [x] Partial test coverage

## Structure

```
todos/src/
├── Providers/
│   ├── TodoServiceProvider.php      # Main provider
│   ├── TodoAuthServiceProvider.php  # Policies
│   └── RouteServiceProvider.php     # Routes
├── Actions/
│   └── Board/
│       ├── CreatesBoard.php
│       ├── UpdateBoard.php
│       ├── DeleteBoard.php
│       ├── DuplicateBoard.php
│       ├── Group/
│       ├── Task/
│       │   ├── CreatesBoardTask.php
│       │   ├── UpdatesBoardTask.php
│       │   ├── Attachment/
│       │   ├── Comment/
│       │   ├── Label/
│       │   ├── Member/
│       │   └── TimeLog/
│       ├── Label/
│       ├── Subscriber/
│       └── Manual/
├── Events/
│   ├── BoardCreated.php
│   ├── BoardUpdated.php
│   ├── Task/
│   └── Subscriber/
├── Http/Controllers/
│   ├── BoardController.php
│   ├── TaskController.php
│   ├── BoardGroupController.php
│   ├── TaskCommentController.php
│   ├── TaskLabelController.php
│   └── ...
├── Models/
│   ├── Board.php
│   ├── Task.php
│   ├── TaskComment.php
│   ├── TaskLabel.php
│   ├── TaskTimeLog.php
│   ├── TaskAttachment.php
│   └── Subscriber.php
├── Transformers/
└── Traits/
```

## Key Patterns

### Multiple Providers
This module uses multiple service providers:
- `TodoServiceProvider` - Main registration
- `TodoAuthServiceProvider` - Policies and gates
- `RouteServiceProvider` - Route registration

### Nested Actions
Actions are organized by entity:
```
Actions/Board/
├── Task/
│   ├── Comment/
│   ├── Label/
│   └── TimeLog/
└── Group/
```

### Rich Event System
20+ domain events for activity tracking:
- `BoardCreated`, `BoardUpdated`, `BoardDeleted`
- `TaskCreated`, `TaskUpdated`, `TaskDeleted`
- `TaskCommentCreated`, `TaskMemberAdded`, etc.

## Models

| Model | Purpose |
|-------|---------|
| `Board` | Kanban board container |
| `Task` | Individual task/card |
| `TaskComment` | Task comments |
| `TaskLabel` | Color labels |
| `TaskTimeLog` | Time tracking |
| `TaskAttachment` | File attachments |
| `Subscriber` | Board subscribers |

## Completion Checklist

- [x] Multiple service providers (Todo, Auth, Route)
- [x] Vite config (`todos.vite.mjs`)
- [x] Rich event system (20+ events)
- [x] Nested action structure
- [x] Transformers
- [x] Policies
- [ ] **Routes NOT active** - commented in `web.php`
- [ ] **Routes need migration** from `routes/todo.php` to module
- [ ] **External contracts NOT defined** - uses main app models directly:
  - [ ] UserContract (currently uses `App\Models\User`)
  - [ ] CompanyContract (currently uses `App\Models\Company`)
  - [ ] MediaContract (for attachments)
- [ ] Adapters in main app (`app/Services/Todos/`)
- [ ] Complete test coverage

## AI Guidelines

When working here:
1. Follow the nested action structure
2. Fire appropriate events for activity tracking
3. Use transformers for API responses
4. Check `ChecksTodoPolicies` trait for authorization
5. **Priority**: Define external contracts and activate routes

## Migration Steps

1. Create external contracts:
   ```
   src/Contracts/External/
   ├── UserContract.php
   ├── CompanyContract.php
   └── MediaContract.php
   ```

2. Update actions to use contracts instead of `App\Models\*`

3. Move routes from `routes/todo.php` to `modules/todos/routes/web.php`

4. Uncomment in `routes/web.php`:
   ```php
   // require base_path('routes/todo.php');
   ```

5. Create adapters in `app/Services/Todos/`
