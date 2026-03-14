# Chatly Testing Guide

This guide explains how to run and write tests for the Chatly module.

## 📦 Test Structure

```
modules/chatly/tests/
├── Unit/
│   └── Actions/
│       ├── AddParticipantTest.php
│       ├── CreateConversationTest.php
│       └── CreateMessageTest.php
├── Feature/
│   └── SearchControllerTest.php
└── Integration/
    └── UserSearchAdapterTest.php
```

## 🏃 Running Tests

### Run All Tests
```bash
php artisan test modules/chatly/tests
```

### Run Specific Test Suites

**Unit Tests Only:**
```bash
php artisan test modules/chatly/tests/Unit
```

**Feature Tests Only:**
```bash
php artisan test modules/chatly/tests/Feature
```

**Integration Tests Only:**
```bash
php artisan test modules/chatly/tests/Integration
```

### Run Specific Test File
```bash
php artisan test modules/chatly/tests/Unit/Actions/AddParticipantTest.php
```

### Run Specific Test Method
```bash
php artisan test --filter=it_authorizes_before_adding_participant
```

### With Coverage Report
```bash
php artisan test modules/chatly/tests --coverage
```

### With Detailed Output
```bash
php artisan test modules/chatly/tests --verbose
```

## 📝 Writing Tests

### Unit Tests for Actions

Unit tests should mock all external dependencies:

```php
<?php

namespace Modules\Chatly\Tests\Unit\Actions;

use Mockery;
use Modules\Chatly\Actions\YourAction;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Tests\TestCase;

class YourActionTest extends TestCase
{
    protected $authorization;
    protected $action;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock external contracts
        $this->authorization = Mockery::mock(AuthorizationContract::class);
        
        // Instantiate action with mocks
        $this->action = new YourAction($this->authorization);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_does_something()
    {
        // Arrange
        $user = Mockery::mock('User');
        
        $this->authorization->shouldReceive('authorize')
            ->once()
            ->with($user, 'someAbility', Mockery::any());

        // Act
        $result = $this->action->someMethod($user, $params);

        // Assert
        $this->assertSomething($result);
    }
}
```

### Feature Tests for Controllers

Feature tests should mock contracts and test HTTP responses:

```php
<?php

namespace Modules\Chatly\Tests\Feature;

use Mockery;
use Modules\Chatly\Contracts\External\UserSearchContract;
use Tests\TestCase;

class YourControllerTest extends TestCase
{
    /** @test */
    public function it_performs_action()
    {
        // Mock the contract
        $mockContract = Mockery::mock(UserSearchContract::class);
        $this->app->instance(UserSearchContract::class, $mockContract);

        $mockContract->shouldReceive('method')
            ->once()
            ->with('param')
            ->andReturn(['result']);

        // Create authenticated user
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Make request
        $response = $this->getJson(route('chatly.route', ['param' => 'value']));

        // Assert response
        $response->assertStatus(200)
            ->assertJson(['expected' => 'data']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
```

### Integration Tests for Adapters

Integration tests test the actual adapter implementations with database:

```php
<?php

namespace Modules\Chatly\Tests\Integration;

use App\Models\User;
use App\Services\Chat\UserSearchAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class YourAdapterTest extends TestCase
{
    use RefreshDatabase;

    protected $adapter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = new UserSearchAdapter();
    }

    /** @test */
    public function it_works_with_real_data()
    {
        // Create real database records
        $user = User::factory()->create(['name' => 'Test User']);
        
        // Act as authenticated user
        $currentUser = User::factory()->create();
        $this->actingAs($currentUser);

        // Test actual adapter functionality
        $result = $this->adapter->search('Test', [], 10);

        // Assert real results
        $this->assertArrayHasKey('people', $result);
        $this->assertNotEmpty($result['people']);
    }
}
```

## 🎯 Test Guidelines

### DO ✅

1. **Mock external contracts** in unit tests
2. **Use factories** for creating test data
3. **Clean up mocks** in `tearDown()`
4. **Test one thing** per test method
5. **Use descriptive test names** (e.g., `it_authorizes_before_creating_message`)
6. **Follow AAA pattern** (Arrange, Act, Assert)
7. **Test happy paths AND edge cases**

### DON'T ❌

1. **Don't use real models** in unit tests (use mocks)
2. **Don't skip tearDown()** when using Mockery
3. **Don't test framework features** (test your code)
4. **Don't create God tests** (tests that test everything)
5. **Don't forget to clean database** in integration tests

## 🔍 Common Test Scenarios

### Testing Authorization

```php
/** @test */
public function it_authorizes_user_before_action()
{
    $this->authorization->shouldReceive('authorize')
        ->once()
        ->with($user, 'ability', $resource);

    $this->action->method($user, $resource);
}

/** @test */
public function it_throws_exception_when_unauthorized()
{
    $this->authorization->shouldReceive('authorize')
        ->once()
        ->andThrow(new AuthorizationException());

    $this->expectException(AuthorizationException::class);

    $this->action->method($user, $resource);
}
```

### Testing URN Resolution

```php
/** @test */
public function it_resolves_urn_to_entity()
{
    $entity = Mockery::mock('User');
    
    $this->urnResolver->shouldReceive('resolve')
        ->once()
        ->with('urn:user:42')
        ->andReturn($entity);

    $result = $this->action->method('urn:user:42');
    
    $this->assertSame($entity, $result);
}
```

### Testing Validation

```php
/** @test */
public function it_validates_required_fields()
{
    $this->expectException(ValidationException::class);

    $this->action->create($user, [
        // Missing required fields
    ]);
}

/** @test */
public function it_accepts_valid_data()
{
    $result = $this->action->create($user, [
        'field' => 'valid value',
    ]);

    $this->assertNotNull($result);
}
```

### Testing HTTP Responses

```php
/** @test */
public function it_returns_json_response()
{
    $response = $this->getJson(route('chatly.route'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);
}

/** @test */
public function it_requires_authentication()
{
    $response = $this->getJson(route('chatly.route'));

    $response->assertStatus(401);
}
```

## 🐛 Debugging Tests

### Enable Verbose Output
```bash
php artisan test modules/chatly/tests --verbose
```

### Stop on First Failure
```bash
php artisan test modules/chatly/tests --stop-on-failure
```

### Run Specific Test in Debug Mode
```bash
php artisan test --filter=test_name --debug
```

### Check Mockery Expectations
```php
// Add this at the end of tests to see what was expected vs received
Mockery::close(); // This validates all expectations
```

## 📊 Coverage Goals

| Test Type | Target Coverage |
|-----------|----------------|
| Actions | 90%+ |
| Controllers | 80%+ |
| Resources | 70%+ |
| Adapters | 85%+ |

## 🔄 Continuous Testing

### Watch Mode (with package)
```bash
# Install phpunit-watcher
composer require --dev spatie/phpunit-watcher

# Run in watch mode
phpunit-watcher watch modules/chatly/tests
```

### Pre-commit Hook
```bash
#!/bin/bash
# .git/hooks/pre-commit

php artisan test modules/chatly/tests
if [ $? -ne 0 ]; then
    echo "Tests failed. Commit aborted."
    exit 1
fi
```

## 📚 Additional Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Mockery Documentation](http://docs.mockery.io/)
- [Laravel Testing](https://laravel.com/docs/testing)
- [Test-Driven Development](https://martinfowler.com/bliki/TestDrivenDevelopment.html)

## 🎓 Best Practices

### 1. Test Naming Convention
```php
// Good
public function it_creates_conversation_with_valid_data()
public function it_throws_exception_when_unauthorized()

// Bad
public function test1()
public function testCreate()
```

### 2. One Assertion Focus
```php
// Good - focused on one behavior
public function it_authorizes_user()
{
    $this->authorization->shouldReceive('authorize')->once();
    $this->action->method($user);
}

// Bad - testing multiple things
public function it_works()
{
    // Tests authorization, validation, creation, and response
    // Too much in one test!
}
```

### 3. Use Data Providers for Similar Tests
```php
/**
 * @test
 * @dataProvider invalidDataProvider
 */
public function it_validates_data($invalidData)
{
    $this->expectException(ValidationException::class);
    $this->action->create($user, $invalidData);
}

public function invalidDataProvider()
{
    return [
        'missing recipients' => [['message' => 'test']],
        'empty recipients' => [['recipients' => []]],
        'invalid type' => [['type' => 'invalid']],
    ];
}
```

## ✅ Checklist for New Tests

When adding new tests, ensure:

- [ ] Test is in correct directory (`Unit/`, `Feature/`, or `Integration/`)
- [ ] External contracts are mocked (unit tests)
- [ ] Mockery::close() in tearDown()
- [ ] Descriptive test name (starts with `it_` or `test_`)
- [ ] Follows AAA pattern (Arrange, Act, Assert)
- [ ] Tests one specific behavior
- [ ] Includes both happy path and error cases
- [ ] No hardcoded values (use factories/fixtures)
- [ ] Test is green before commit

---

Happy Testing! 🎉

