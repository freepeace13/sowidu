# Database Context

Load this context with `/context-database` when working on database-related code.

## Migration Standards

```php
// Migration naming: {date}_{action}_{table}_table.php
// 2024_01_15_create_invoices_table.php
// 2024_01_16_add_status_to_invoices_table.php

Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->foreignId('client_id')->constrained()->nullOnDelete();
    $table->string('number', 50)->unique();
    $table->decimal('total', 12, 2)->default(0);
    $table->enum('status', ['draft', 'sent', 'paid', 'cancelled']);
    $table->timestamp('paid_at')->nullable();
    $table->timestamps();
    $table->softDeletes();

    // Indexes for common queries
    $table->index(['company_id', 'status']);
    $table->index(['company_id', 'created_at']);
});
```

## Indexing Rules

- Index all foreign keys
- Index columns used in WHERE clauses
- Index columns used in ORDER BY
- Use composite indexes for multi-column queries
- Don't over-index (slows writes)

## Relationships

```php
// Always define both sides of relationships
class Invoice extends Model
{
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}

class Client extends Model
{
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
```

## Soft Deletes

- Use soft deletes for user-facing data
- Include `deleted_at` in unique constraints when needed
- Clean up soft-deleted records periodically

## Performance Standards

### Prevent N+1 Queries

```php
// BAD - N+1 problem
$invoices = Invoice::all();
foreach ($invoices as $invoice) {
    echo $invoice->client->name;  // Query per iteration
}

// GOOD - Eager loading
$invoices = Invoice::with('client')->get();
foreach ($invoices as $invoice) {
    echo $invoice->client->name;  // No additional queries
}
```

### Query Optimization

- Use `select()` to limit columns
- Use `chunk()` for large datasets
- Add database indexes for frequently queried columns
- Use `explain()` to analyze slow queries

```php
// Efficient pagination
Invoice::select(['id', 'number', 'total', 'client_id'])
    ->with('client:id,name')
    ->where('status', 'pending')
    ->orderByDesc('created_at')
    ->paginate(25);
```

### Caching Strategy

```php
// Cache expensive queries
$stats = Cache::remember('invoice.stats.'.$companyId, 3600, function () use ($companyId) {
    return Invoice::where('company_id', $companyId)
        ->selectRaw('COUNT(*) as total, SUM(amount) as revenue')
        ->first();
});

// Cache tags for easier invalidation
Cache::tags(['invoices', "company.{$companyId}"])->put($key, $data, 3600);
```

### Queue Heavy Operations

- PDF generation → Queue job
- Email sending → Queue job
- Report generation → Queue job
- External API calls → Queue job

```php
// Dispatch to queue
GenerateInvoicePdfJob::dispatch($invoice)->onQueue('documents');
SendInvoiceEmailJob::dispatch($invoice)->delay(now()->addSeconds(10));
```
