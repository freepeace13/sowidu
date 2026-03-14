# Testing Context

Load this context with `/context-testing` when working on tests.

## Testing Requirements

### Minimum Coverage
- All Actions MUST have unit tests
- All Controllers MUST have feature tests
- External contract adapters MUST have integration tests

### Coverage Thresholds

| Module Status | Required Coverage |
|---------------|-------------------|
| Complete | 80% minimum |
| Partial | 60% minimum |
| New modules | 80% before merging |

**Critical paths** (auth, payments, data mutations) require **90%+ coverage**.

### Test Location
- Unit tests: `modules/{module}/tests/Unit/`
- Feature tests: `modules/{module}/tests/Feature/`
- Integration tests: `modules/{module}/tests/Integration/`

### Test Naming
- Test classes: `{ClassName}Test.php` (e.g., `CreateConversationTest.php`)
- Test methods: `test_{action}_when_{condition}()` or `it_{does_something}()`

### Running Tests

```bash
# Run all module tests
./vendor/bin/sail artisan test modules/{module}/tests

# Run specific test type
./vendor/bin/sail artisan test modules/{module}/tests/Unit
./vendor/bin/sail artisan test modules/{module}/tests/Feature

# Run single test file
./vendor/bin/sail artisan test modules/{module}/tests/Unit/Actions/CreateItemTest.php
```

## Test Structure

### Unit Test Example

```php
namespace Modules\Invoicify\Tests\Unit\Actions;

use PHPUnit\Framework\TestCase;
use Modules\Invoicify\Actions\CreateInvoiceAction;

class CreateInvoiceActionTest extends TestCase
{
    private CreateInvoiceAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateInvoiceAction(
            $this->createMock(InvoiceRepository::class),
            $this->createMock(InvoiceNumberGenerator::class),
        );
    }

    public function test_creates_invoice_with_valid_data(): void
    {
        // Arrange
        $data = new InvoiceData(clientId: 1, total: 100.00);

        // Act
        $result = $this->action->execute($this->user, $data);

        // Assert
        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertEquals(100.00, $result->total);
    }

    public function test_throws_exception_when_client_not_found(): void
    {
        $this->expectException(InvoicifyException::class);

        $data = new InvoiceData(clientId: 999, total: 100.00);
        $this->action->execute($this->user, $data);
    }
}
```

### Feature Test Example

```php
namespace Modules\Invoicify\Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_invoice(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/invoices', [
                'client_id' => 1,
                'amount' => 100.00,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('invoices', [
            'client_id' => 1,
            'total' => 100.00,
        ]);
    }

    public function test_unauthorized_user_cannot_access_invoices(): void
    {
        $response = $this->get('/invoices');

        $response->assertStatus(401);
    }
}
```

### Integration Test Example

```php
namespace Modules\Chatly\Tests\Integration;

use Tests\TestCase;
use App\Services\Chat\UserSearchAdapter;
use Modules\Chatly\Contracts\External\UserSearchContract;

class UserSearchAdapterTest extends TestCase
{
    public function test_adapter_implements_contract(): void
    {
        $adapter = app(UserSearchContract::class);

        $this->assertInstanceOf(UserSearchAdapter::class, $adapter);
    }

    public function test_search_returns_expected_format(): void
    {
        $adapter = app(UserSearchContract::class);

        $results = $adapter->search('john', [], 10);

        $this->assertIsArray($results);
    }
}
```

## Test Best Practices

### Do
- Test one thing per test method
- Use descriptive test names
- Follow Arrange-Act-Assert pattern
- Mock external dependencies
- Test edge cases and error conditions

### Don't
- Test implementation details
- Write tests that depend on order
- Use real external services
- Skip testing error paths
- Write tests just for coverage
