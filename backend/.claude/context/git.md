# Git Workflow Context

Load this context with `/context-git` when working on git operations.

## Branch Naming

```
feature/{module}-{short-description}
bugfix/{module}-{short-description}
hotfix/{issue-number}-{short-description}
refactor/{module}-{short-description}

# Examples
feature/invoicify-add-recurring-invoices
bugfix/chatly-message-duplication
hotfix/1234-payment-calculation
refactor/todos-extract-contracts
```

## Commit Messages

```
{type}({scope}): {description}

# Types: feat, fix, refactor, test, docs, chore
# Scope: module name or area

# Examples
feat(invoicify): add recurring invoice generation
fix(chatly): resolve message duplication on reconnect
refactor(todos): extract user contract for isolation
test(catalog): add unit tests for CreateItemAction
docs(offer): update API documentation
chore: update dependencies
```

## Pull Request Requirements

- Descriptive title matching commit convention
- Description of changes and why
- Link to related issues
- Screenshots for UI changes
- All tests passing
- No merge conflicts

## Code Review Checklist

Before approving any PR, verify:

### Functionality
- [ ] Code does what it's supposed to do
- [ ] Edge cases are handled
- [ ] No regressions introduced

### Code Quality
- [ ] Follows module isolation (no `use App\` in modules)
- [ ] Actions have interfaces
- [ ] Controllers are thin (delegate to Actions)
- [ ] No code duplication
- [ ] No hardcoded values

### Type Safety
- [ ] `declare(strict_types=1)` present
- [ ] All parameters typed
- [ ] All return types defined

### Security
- [ ] Input validated via FormRequest
- [ ] Authorization checked via Policy
- [ ] No SQL injection vulnerabilities
- [ ] No XSS vulnerabilities
- [ ] Sensitive data not logged

### Performance
- [ ] No N+1 queries
- [ ] Eager loading used appropriately
- [ ] Heavy operations queued
- [ ] Indexes added for new queries

### Testing
- [ ] Unit tests for Actions
- [ ] Feature tests for Controllers
- [ ] Coverage meets threshold
- [ ] Tests are meaningful (not just coverage)

### Database
- [ ] Migrations are reversible
- [ ] Indexes added where needed
- [ ] Foreign keys properly constrained

### Documentation
- [ ] Complex logic is commented
- [ ] Public APIs documented
- [ ] README updated if needed
