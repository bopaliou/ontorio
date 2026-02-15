# Testing Patterns & Mocking

## 1. Unit Tests
- **Scope**: Test a single class or function in isolation.
- **Pattern**: Use `Tests\Unit` namespace.
- **Rule**: NO database access or external APIs. Mock all dependencies.

## 2. Integration / Feature Tests
- **Scope**: Test the interaction between components (Controllers, Services, DB).
- **Pattern**: Use `Tests\Feature` namespace.
- **Rule**: Use `RefreshDatabase` trait. Assert on HTTP responses and DB state.

## 3. Mocking Strategy
- **Service Mocking**: `this->mock(MyService::class, function ($mock) { ... })`
- **Event Faking**: `Event::fake()`
- **Mail Faking**: `Mail::fake()`
- **Storage Faking**: `Storage::fake('public')`

## 4. Useful Assertions
- `assertStatus(200)`
- `assertDatabaseHas('table', [...])`
- `assertJsonFragment([...])`
- `assertViewIs('...')`
