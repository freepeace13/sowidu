# API Context

Load this context with `/context-api` when working on API endpoints.

## Success Responses

```php
// Single resource
return response()->json([
    'data' => new InvoiceResource($invoice),
], 200);

// Collection with pagination
return response()->json([
    'data' => InvoiceResource::collection($invoices),
    'meta' => [
        'current_page' => $invoices->currentPage(),
        'last_page' => $invoices->lastPage(),
        'per_page' => $invoices->perPage(),
        'total' => $invoices->total(),
    ],
], 200);

// Action success
return response()->json([
    'message' => 'Invoice sent successfully.',
    'data' => new InvoiceResource($invoice),
], 200);
```

## Error Responses

```php
// Validation error (422)
return response()->json([
    'message' => 'The given data was invalid.',
    'errors' => [
        'email' => ['The email field is required.'],
        'amount' => ['The amount must be greater than 0.'],
    ],
], 422);

// Not found (404)
return response()->json([
    'message' => 'Invoice not found.',
], 404);

// Forbidden (403)
return response()->json([
    'message' => 'You do not have permission to perform this action.',
], 403);

// Server error (500)
return response()->json([
    'message' => 'An unexpected error occurred. Please try again.',
], 500);
```

## HTTP Status Codes

| Code | Usage |
|------|-------|
| 200 | Success (GET, PUT, PATCH) |
| 201 | Created (POST) |
| 204 | No Content (DELETE) |
| 400 | Bad Request |
| 401 | Unauthenticated |
| 403 | Forbidden (Unauthorized) |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Server Error |

## API Resources

Always use API Resources for consistent output:

```php
namespace Modules\Invoicify\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'total' => $this->total,
            'status' => $this->status,
            'client' => new ClientResource($this->whenLoaded('client')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
```

## Inertia Responses

For Inertia.js pages:

```php
// Return Inertia page
return inertia('Invoicify/Invoices/Show/index', [
    'invoice' => new InvoiceResource($invoice),
    'clients' => ClientResource::collection($clients),
]);

// With flash message
return redirect()->route('invoices.show', $invoice)
    ->with('success', 'Invoice created successfully.');
```

## Frontend Performance

- Use Inertia partial reloads
- Lazy load components
- Paginate large lists
- Debounce search inputs
