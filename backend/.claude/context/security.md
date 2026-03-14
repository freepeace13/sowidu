# Security Context

Load this context with `/context-security` when working on security-related code.

## Authorization

- ALWAYS use Policies for model authorization
- Check permissions in Controllers, not Actions
- Use Gates for non-model permissions

```php
// Controller
public function update(UpdateInvoiceRequest $request, Invoice $invoice)
{
    $this->authorize('update', $invoice);  // Policy check

    return $this->updateAction->execute($invoice, $request->validated());
}
```

## Input Validation

- ALWAYS use FormRequest classes
- Never trust user input
- Sanitize HTML output with `{{ }}` (not `{!! !!}`)
- Validate file uploads (type, size, content)

```php
namespace Modules\Invoicify\Http\Requests;

class StoreInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'due_date' => ['required', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
```

## SQL Injection Prevention

- NEVER use raw queries with user input
- Always use Eloquent or query builder bindings
- Use `whereIn()` with validated arrays

```php
// GOOD
User::where('email', $request->email)->first();
DB::select('SELECT * FROM users WHERE id = ?', [$id]);

// BAD - SQL Injection vulnerable
DB::select("SELECT * FROM users WHERE id = $id");
```

## XSS Prevention

- Use Blade `{{ }}` for output (auto-escapes)
- Sanitize rich text with HTMLPurifier
- Validate URLs before redirects
- Set proper Content-Type headers

## Mass Assignment

- Always define `$fillable` or `$guarded` on models
- Never use `$guarded = []` in production
- Use DTOs for complex data transfer

## Sensitive Data

- Never log passwords, tokens, or PII
- Use encryption for sensitive database fields
- Store secrets in `.env`, never commit them
- Mask sensitive data in error messages

## Error Handling

### Exception Hierarchy
```
modules/{module}/src/Exceptions/
├── {Module}Exception.php          # Base exception for module
├── ValidationException.php         # Input validation errors
├── AuthorizationException.php      # Permission denied
├── NotFoundException.php           # Resource not found
└── BusinessRuleException.php       # Domain logic violations
```

### Exception Pattern
```php
namespace Modules\Invoicify\Exceptions;

class InvoicifyException extends \Exception
{
    public static function invoiceNotFound(int $id): self
    {
        return new self("Invoice #{$id} not found", 404);
    }

    public static function cannotModifyPaidInvoice(): self
    {
        return new self("Cannot modify a paid invoice", 422);
    }
}
```

### Error Response Standards
- Never expose internal errors to users
- Log full exception details server-side
- Return user-friendly messages client-side
- Use consistent error format:

```php
return response()->json([
    'message' => 'The invoice could not be found.',
    'errors' => ['invoice_id' => ['Invoice does not exist.']],
], 404);
```

### Try-Catch Guidelines
- Catch specific exceptions, not generic `\Exception`
- Always log unexpected exceptions
- Re-throw when you can't handle appropriately

## Security Scan Checklist

### SQL Injection
- [ ] Raw queries with variables
- [ ] `DB::raw()` with user input
- [ ] Dynamic column/table names

### XSS Vulnerabilities
- [ ] `{!! !!}` usage without sanitization
- [ ] `innerHTML` in Vue components
- [ ] Unescaped user content

### Authentication/Authorization
- [ ] Missing `$this->authorize()` calls
- [ ] Missing middleware on routes
- [ ] Hardcoded credentials

### Sensitive Data
- [ ] Passwords/tokens in logs
- [ ] Secrets in code (not .env)
- [ ] PII in error messages
