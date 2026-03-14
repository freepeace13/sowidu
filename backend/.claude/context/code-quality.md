# Code Quality Context

Load this context with `/context-quality` when working on code quality.

## SOLID Principles

All code MUST follow SOLID principles:

### S - Single Responsibility Principle

Each class should have only one reason to change.

```php
// BAD - Multiple responsibilities
class InvoiceService
{
    public function create(array $data): Invoice { }
    public function generatePdf(Invoice $invoice): string { }
    public function sendEmail(Invoice $invoice): void { }
    public function calculateTax(Invoice $invoice): float { }
}

// GOOD - Single responsibility per class
class CreateInvoiceAction { }
class GenerateInvoicePdfAction { }
class SendInvoiceEmailAction { }
class InvoiceTaxCalculator { }
```

### O - Open/Closed Principle

Open for extension, closed for modification. Use interfaces and inheritance.

```php
// BAD - Must modify class to add new payment type
class PaymentProcessor
{
    public function process(Payment $payment): void
    {
        if ($payment->type === 'credit_card') { }
        elseif ($payment->type === 'bank_transfer') { }
        elseif ($payment->type === 'paypal') { } // Adding new type requires modification
    }
}

// GOOD - Extend without modifying
interface PaymentGatewayContract
{
    public function process(Payment $payment): PaymentResult;
}

class CreditCardGateway implements PaymentGatewayContract { }
class BankTransferGateway implements PaymentGatewayContract { }
class PayPalGateway implements PaymentGatewayContract { } // New type, no modification needed
```

### L - Liskov Substitution Principle

Subtypes must be substitutable for their base types.

```php
// BAD - Subclass changes expected behavior
class Rectangle
{
    public function setWidth(int $w): void { $this->width = $w; }
    public function setHeight(int $h): void { $this->height = $h; }
}

class Square extends Rectangle
{
    public function setWidth(int $w): void { $this->width = $this->height = $w; } // Breaks expectation
}

// GOOD - Use composition or separate abstractions
interface Shape
{
    public function area(): float;
}

class Rectangle implements Shape { }
class Square implements Shape { }
```

### I - Interface Segregation Principle

Many specific interfaces are better than one general-purpose interface.

```php
// BAD - Fat interface forces unnecessary implementations
interface WorkerContract
{
    public function work(): void;
    public function eat(): void;
    public function sleep(): void;
}

class Robot implements WorkerContract
{
    public function eat(): void { } // Robot can't eat - forced empty implementation
}

// GOOD - Segregated interfaces
interface WorkableContract
{
    public function work(): void;
}

interface FeedableContract
{
    public function eat(): void;
}

class Human implements WorkableContract, FeedableContract { }
class Robot implements WorkableContract { }
```

### D - Dependency Inversion Principle

Depend on abstractions, not concretions.

```php
// BAD - Depends on concrete class
class InvoiceController
{
    public function __construct(
        private InvoicePdfGenerator $pdfGenerator  // Concrete class
    ) {}
}

// GOOD - Depends on abstraction
class InvoiceController
{
    public function __construct(
        private GeneratesPdfContract $pdfGenerator  // Interface
    ) {}
}
```

## Design Patterns

### Accepted Patterns

Use these patterns consistently across the codebase:

#### Action Pattern
Single-purpose classes for business logic.

```php
namespace Modules\Invoicify\Actions;

class CreateInvoiceAction implements CreatesInvoiceContract
{
    public function __construct(
        private InvoiceRepository $repository,
        private InvoiceNumberGenerator $numberGenerator,
    ) {}

    public function execute(User $user, InvoiceData $data, ?int $teamId = null): Invoice
    {
        $invoice = new Invoice([
            'number' => $this->numberGenerator->next($teamId),
            'client_id' => $data->clientId,
            'total' => $data->total,
        ]);

        return $this->repository->save($invoice);
    }
}
```

#### Repository Pattern
Encapsulate data access logic.

```php
namespace Modules\Invoicify\Repositories;

class InvoiceRepository
{
    public function find(int $id): ?Invoice
    {
        return Invoice::find($id);
    }

    public function save(Invoice $invoice): Invoice
    {
        $invoice->save();
        return $invoice;
    }

    public function forCompany(int $companyId): Builder
    {
        return Invoice::where('company_id', $companyId);
    }
}
```

#### DTO (Data Transfer Object) Pattern
Type-safe data containers.

```php
namespace Modules\Invoicify\Data;

use Spatie\LaravelData\Data;

class InvoiceData extends Data
{
    public function __construct(
        public int $clientId,
        public string $number,
        public float $total,
        public Carbon $dueDate,
        public ?string $notes = null,
        /** @var array<InvoiceItemData> */
        public array $items = [],
    ) {}
}
```

#### Factory Pattern
Create complex objects.

#### Strategy Pattern
Interchangeable algorithms.

#### Observer Pattern (Events)
Decouple components using Laravel events.

#### Decorator Pattern
Add behavior without modifying original class.

### Anti-Patterns to Avoid

| Anti-Pattern | Problem | Solution |
|--------------|---------|----------|
| God Object | Class does too much | Split into focused classes |
| Spaghetti Code | Tangled dependencies | Use proper layering |
| Golden Hammer | Same solution everywhere | Choose right tool for job |
| Copy-Paste | Duplicated logic | Extract to shared class/trait |
| Magic Numbers | Unexplained values | Use constants/enums/config |
| Premature Optimization | Optimizing before measuring | Profile first, optimize second |
| Anemic Domain Model | Models with only getters/setters | Add behavior to models |
| Service Locator | Hiding dependencies | Use constructor injection |

### Pattern Usage by Layer

| Layer | Patterns |
|-------|----------|
| Controllers | Thin controllers, delegate to Actions |
| Actions | Action pattern, single responsibility |
| Services | Strategy, Factory, Decorator |
| Repositories | Repository pattern for data access |
| Models | Active Record (Eloquent), Observer (events) |
| Data | DTO pattern for data transfer |
| External | Adapter pattern for contracts |

## Code Smells Detection

When working on modules, AI agents MUST identify and fix these issues:

### Anti-Patterns to Find

| Code Smell | Detection | Fix |
|------------|-----------|-----|
| Direct App Imports | `use App\` in module src/ | Create external contract + adapter |
| Static Calls | `Class::method()` | Use constructor injection |
| Fat Controllers | Business logic in controllers | Extract to Action classes |
| Missing Contracts | Actions without interfaces | Add to `Contracts/Actions/` |
| Raw Arrays | Array returns instead of typed data | Create DTOs in `Data/` |
| Magic Values | Hardcoded strings/numbers | Move to config or Enums |
| God Classes | Classes >300 lines | Split into focused classes |
| Missing Validation | No FormRequest usage | Create Request classes |

### Before Refactoring Checklist

When moving code to modules, check for:
1. [ ] `use App\Models\*` - needs external contract
2. [ ] `use App\Services\*` - needs external contract
3. [ ] `Auth::user()` or `auth()->user()` - use injected user from controller
4. [ ] `config('app.*')` - define module-specific config
5. [ ] Direct DB queries - encapsulate in repositories
6. [ ] Event dispatching - ensure events are module-namespaced

### Type Safety

All PHP files MUST declare strict types:
```php
<?php

declare(strict_types=1);

namespace Modules\Invoicify\Actions;
```

### Type Hints Required
- All method parameters MUST have type hints
- All methods MUST have return types
- Use union types for nullable: `?string` or `string|null`
- Use `mixed` only when truly necessary
