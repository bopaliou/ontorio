---
name: tdd-master
description: Expert in Test-Driven Development (TDD) and automated testing. Use when you need to write tests, refactor code using testing cycles, or implement new features following the Red-Green-Refactor workflow.
---

# TDD Master

This skill transforms Gemini CLI into a TDD-focused agent, ensuring code quality through rigorous automated testing.

## Core Testing Workflow

### 1. Planning
- Identify the smallest unit of logic to implement.
- Decide between a Unit Test (logic only) or a Feature Test (integration/HTTP).

### 2. Implementation (Red-Green-Refactor)
- **Step 1 (Red)**: Create the test file in `tests/Unit` or `tests/Feature`. Execute and confirm failure.
- **Step 2 (Green)**: Create/Update the Model, Service, or Controller. Execute and confirm success.
- **Step 3 (Refactor)**: Optimize logic and structure. Re-run all tests.

## Advanced Techniques

### Mocking & Faking
Always isolate unit tests from external dependencies (Database, Filesystem, Mail). Use Laravel's built-in fakes.

### Continuous Integration (CI)
Ensure that any new tests are compatible with the project's CI pipeline (check `.github/workflows/ci.yml`).

## Reference Material

- [TDD Workflow](references/tdd-workflow.md): The Red-Green-Refactor cycle.
- [Testing Patterns](references/testing-patterns.md): Unit vs Feature tests and mocking strategies.
