# TDD Workflow Guide (Red-Green-Refactor)

Follow this strict cycle for every new feature or bug fix.

## 1. RED: Write a Failing Test
- **Action**: Before writing any production code, write a test that defines the expected behavior.
- **Goal**: The test MUST fail. This proves the test is valid and that the feature doesn't exist yet.
- **Command**: `php artisan test --filter=YourTestName`

## 2. GREEN: Make it Pass
- **Action**: Write the *minimum* amount of code necessary to make the test pass.
- **Goal**: Don't worry about perfection or elegance yet. Just get to green.
- **Command**: `php artisan test` (Verify success)

## 3. REFACTOR: Clean Up
- **Action**: Improve the code quality while keeping the tests passing.
- **Goal**: Remove duplication, improve naming, and ensure adherence to SOLID principles.
- **Command**: `php artisan test` (Ensure no regressions)

## 4. REPEAT
- Move to the next small piece of functionality.
