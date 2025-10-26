# PHP Recruitment Refactor Assignment - Agent Context

> **Purpose**: Demonstrate clean architecture, design patterns, and refactoring best practices
> **Project Type**: PHP refactoring challenge for senior developer position
> **Architecture**: PHP 8.1+ / Clean Architecture / SOLID Principles / DDD

## Project Overview

This is a **PHP refactoring challenge** designed to evaluate senior developer skills in:
- Code architecture and design patterns
- Business logic understanding
- Clean code practices  
- Unit testing strategies
- Modern PHP features

## Business Domain

### Core Functionality
The system synchronizes **Doctor Appointment Slots** from an external API to a local database.

**Business Flow**:
```
1. Fetch doctors list from external API
2. For each doctor:
   - Update doctor information (name normalization)
   - Fetch their available appointment slots
   - Synchronize slots to local database
   - Handle errors gracefully (mark doctor with error flag)
3. Log errors (except on Sundays for business reasons)
```

### Domain Entities

| Entity | Purpose | Key Attributes |
|--------|---------|----------------|
| **Doctor** | Medical practitioner | ID, Name, Error flag |
| **Slot** | Available appointment slot | Doctor ID, Start/End time, Created timestamp |

### Business Rules

1. **Name Normalization**: 
   - Standard names: `ucwords()` formatting
   - Irish names (O'Name): Special capitalization handling
   
2. **Slot Management**:
   - Slots are updated if they exist and are "stale" (>5 minutes old)
   - New slots are created if they don't exist
   
3. **Error Handling**:
   - Doctors are marked with error flag if slot fetching fails
   - Error logging is disabled on Sundays (business rule)
   
4. **Data Persistence**:
   - Immediate persistence (no batching)
   - Uses Doctrine ORM

## Current Architecture Issues

### Identified Problems

**Single Responsibility Principle Violations**:
- `DoctorSlotsSynchronizer` handles HTTP requests, business logic, data persistence, logging

**Tight Coupling**:
- Direct dependency on Doctrine EntityManager
- Hardcoded credentials and endpoints
- Mixed concerns (HTTP + business logic + persistence)

**Testability Issues**:
- Hard to mock external API calls
- Complex constructor dependencies
- Side effects in methods

**Error Handling**:
- Generic exception catching
- Inconsistent error reporting

## Refactoring Goals

### Target Architecture

```
┌─────────────────────────────────────┐
│   Application Layer                 │
│   - DoctorSynchronizationService    │
├─────────────────────────────────────┤
│   Domain Layer                      │
│   - Doctor (Entity)                 │
│   - Slot (Entity)                   │
│   - Business Rules                  │
├─────────────────────────────────────┤
│   Infrastructure Layer              │
│   - ApiClient (HTTP)                │
│   - Repository (Database)           │
│   - Logger                          │
└─────────────────────────────────────┘
```

### Recommended Patterns

1. **Repository Pattern**: Abstract data access
2. **Strategy Pattern**: Handle different data sources (API vs Static)
3. **Factory Pattern**: Create entities with validation
4. **Command Pattern**: Separate business operations
5. **Dependency Injection**: Reduce coupling

## Module Structure

### Suggested Organization

```
src/
├── Entity/                    # Domain entities
│   ├── Doctor.php            # Doctor domain model
│   ├── Slot.php              # Slot domain model  
│   └── AGENTS.md             # Entity documentation
├── Repository/                # Data access layer
│   ├── DoctorRepositoryInterface.php
│   ├── SlotRepositoryInterface.php
│   └── Doctrine/             # Doctrine implementations
├── Service/                   # Application services
│   ├── DoctorSynchronizationService.php
│   ├── NameNormalizationService.php
│   └── ErrorReportingService.php
├── Infrastructure/            # External concerns
│   ├── ApiClient/
│   │   ├── DoctorApiClientInterface.php
│   │   ├── HttpDoctorApiClient.php
│   │   └── StaticDoctorApiClient.php
│   └── Logger/
├── Exception/                 # Domain exceptions
│   ├── DoctorSynchronizationException.php
│   └── ApiConnectionException.php
└── AGENTS.md                 # Module documentation
```

## SOLID Principles Application

### Single Responsibility Principle (SRP)

**Problem**: `DoctorSlotsSynchronizer` does everything
```php
// Current: One class handles HTTP, business logic, persistence
class DoctorSlotsSynchronizer {
    // Handles HTTP requests
    // Processes business logic  
    // Manages database operations
    // Handles logging
}
```

**Solution**: Separate concerns
```php
class DoctorSynchronizationService {
    // Orchestrates synchronization process
}

class DoctorApiClient {
    // Handles HTTP communication only
}

class DoctorRepository {  
    // Handles database operations only
}
```

### Open/Closed Principle (OCP)

**Goal**: Support multiple data sources without modification

```php
interface DoctorDataSourceInterface {
    public function getDoctors(): array;
    public function getDoctorSlots(int $doctorId): array;
}

// Implementations:
// - HttpDoctorDataSource (current API)
// - StaticDoctorDataSource (test data)
// - DatabaseDoctorDataSource (future requirement)
```

### Liskov Substitution Principle (LSP)

**Implementation**: Ensure all data source implementations are interchangeable

### Interface Segregation Principle (ISP)

**Implementation**: Create focused interfaces
- `DoctorRepositoryInterface` - Only doctor operations
- `SlotRepositoryInterface` - Only slot operations
- `LoggerInterface` - Only logging operations

### Dependency Inversion Principle (DIP)

**Problem**: Depends on concrete classes
```php
// Bad: Direct dependency on Doctrine
$this->repository = $em->getRepository(Doctor::class);
```

**Solution**: Depend on abstractions
```php
public function __construct(
    private DoctorRepositoryInterface $doctorRepository,
    private SlotRepositoryInterface $slotRepository,
    private DoctorApiClientInterface $apiClient,
    private LoggerInterface $logger,
) {}
```

## Design Patterns to Apply

### Repository Pattern

**Purpose**: Abstract data access layer

```php
interface DoctorRepositoryInterface {
    public function findById(string $id): ?Doctor;
    public function save(Doctor $doctor): void;
    public function findAll(): array;
}

interface SlotRepositoryInterface {
    public function findByDoctorAndTime(int $doctorId, DateTime $start): ?Slot;
    public function save(Slot $slot): void;
}
```

### Strategy Pattern

**Purpose**: Handle different data sources

```php
interface DoctorDataSourceInterface {
    public function getDoctors(): array;
    public function getDoctorSlots(int $doctorId): array;
}

class HttpDoctorDataSource implements DoctorDataSourceInterface {
    // API implementation
}

class StaticDoctorDataSource implements DoctorDataSourceInterface {
    // Static data implementation  
}
```

### Factory Pattern

**Purpose**: Create entities with validation

```php
class DoctorFactory {
    public function createFromApiData(array $data): Doctor {
        $normalizedName = $this->nameNormalizer->normalize($data['name']);
        return new Doctor((string)$data['id'], $normalizedName);
    }
}

class SlotFactory {
    public function createFromApiData(array $data, int $doctorId): Slot {
        $start = new DateTime($data['start']);
        $end = new DateTime($data['end']);
        return new Slot($doctorId, $start, $end);
    }
}
```

## Testing Strategy

### Unit Test Structure

```
tests/
├── Unit/
│   ├── Entity/
│   │   ├── DoctorTest.php           # Entity behavior
│   │   └── SlotTest.php             # Entity behavior
│   ├── Service/
│   │   ├── DoctorSynchronizationServiceTest.php
│   │   └── NameNormalizationServiceTest.php
│   └── Infrastructure/
│       └── StaticDoctorApiClientTest.php
├── Integration/
│   ├── Repository/
│   │   ├── DoctorRepositoryTest.php  # Database integration
│   │   └── SlotRepositoryTest.php
│   └── Infrastructure/
│       └── HttpDoctorApiClientTest.php
└── Feature/
    └── DoctorSlotsSynchronizationTest.php  # Full flow
```

### Test Guidelines

**Unit Tests Focus**:
- Business logic validation
- Entity behavior 
- Service coordination
- Error handling

**Integration Tests Focus**:
- Database operations
- HTTP client behavior
- External API integration

**Feature Tests Focus**:
- Complete synchronization flow
- Error scenarios
- Business rule validation

### Mock Strategy

```php
class DoctorSynchronizationServiceTest extends TestCase {
    private DoctorRepositoryInterface $doctorRepository;
    private SlotRepositoryInterface $slotRepository; 
    private DoctorApiClientInterface $apiClient;
    private LoggerInterface $logger;
    
    protected function setUp(): void {
        $this->doctorRepository = $this->createMock(DoctorRepositoryInterface::class);
        $this->slotRepository = $this->createMock(SlotRepositoryInterface::class);
        $this->apiClient = $this->createMock(DoctorApiClientInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }
}
```

## Error Handling Strategy

### Exception Hierarchy

```php
abstract class DoctorSynchronizationException extends Exception {}

class ApiConnectionException extends DoctorSynchronizationException {}
class InvalidApiResponseException extends DoctorSynchronizationException {}
class DoctorNotFoundException extends DoctorSynchronizationException {}
class SlotValidationException extends DoctorSynchronizationException {}
```

### Error Recovery

**Strategies**:
1. **Graceful degradation**: Mark doctor with error, continue with others
2. **Retry logic**: Retry failed API calls with exponential backoff
3. **Circuit breaker**: Stop trying if too many failures
4. **Dead letter queue**: Store failed operations for later retry

## Performance Considerations

### Current Issues
- Individual API calls for each doctor's slots (N+1 problem)
- Immediate database persistence (no batching)
- No caching mechanism

### Optimization Opportunities
1. **Batch API calls**: Fetch multiple doctors' slots in single request
2. **Database batching**: Batch database operations
3. **Caching**: Cache doctor data with TTL
4. **Async processing**: Use message queues for heavy operations

## Security Considerations

### Current Security Issues
- Hardcoded credentials in code
- No input validation on API responses
- No rate limiting on API calls

### Security Improvements
1. **Environment variables**: Move credentials to environment
2. **Input validation**: Validate all API response data
3. **Rate limiting**: Implement API call throttling
4. **HTTPS enforcement**: Ensure encrypted communication

## Development Guidelines

### Code Quality Standards

**PSR Compliance**:
- PSR-1: Basic Coding Standard
- PSR-4: Autoloading Standard  
- PSR-12: Extended Coding Style

**Type Safety**:
```php
// Use strict typing
declare(strict_types=1);

// Type all parameters and return types
public function synchronize(int $doctorId): SynchronizationResult
```

**Error Handling**:
```php
// Specific exceptions with context
throw new ApiConnectionException(
    sprintf('Failed to connect to %s', $endpoint),
    previous: $exception
);
```

### Naming Conventions

**Classes**: PascalCase with descriptive names
- `DoctorSynchronizationService` (not `Synchronizer`)
- `HttpDoctorApiClient` (not `Client`)

**Methods**: camelCase with verb-noun pattern
- `synchronizeDoctorSlots()` (not `sync()`)
- `findDoctorById()` (not `find()`)

**Variables**: camelCase with meaningful names
- `$normalizedName` (not `$name`)
- `$doctorSlots` (not `$slots`)

## Refactoring Checklist

### Pre-Refactoring
- [ ] Understand current business logic completely
- [ ] Identify all responsibilities in current class
- [ ] Map external dependencies
- [ ] Document current behavior (tests)

### During Refactoring
- [ ] Apply Single Responsibility Principle
- [ ] Extract interfaces for dependencies
- [ ] Implement dependency injection
- [ ] Add proper exception handling
- [ ] Write comprehensive tests

### Post-Refactoring
- [ ] Verify business logic unchanged
- [ ] Confirm all tests pass
- [ ] Check performance hasn't degraded
- [ ] Document architectural decisions
- [ ] Add TODO comments for future improvements

## Common Pitfalls to Avoid

### ❌ Over-Engineering
```php
// Bad: Complex abstraction for simple logic
interface NameProcessorInterface {
    public function process(string $input, NameProcessingStrategy $strategy): string;
}
```

```php
// Good: Simple, focused service
class NameNormalizationService {
    public function normalize(string $fullName): string;
}
```

### ❌ Incomplete Separation
```php  
// Bad: Still mixing concerns
class DoctorService {
    public function synchronize(): void {
        $data = file_get_contents($url); // Still doing HTTP
        // ...
    }
}
```

### ❌ Missing Tests for Business Logic
```php
// Bad: Only testing infrastructure
class HttpClientTest extends TestCase {
    public function testMakesHttpRequest(): void
}
```

```php
// Good: Testing business rules
class NameNormalizationServiceTest extends TestCase {
    public function testNormalizesIrishNames(): void {
        $result = $this->service->normalize("john o'connor");
        $this->assertEquals("John O'Connor", $result);
    }
}
```

## Module-Specific Documentation

For detailed information about specific parts:

- **[Entity Documentation](src/Entity/AGENTS.md)** - Domain models and relationships
- **[Source Code Patterns](src/AGENTS.md)** - Implementation patterns and synchronization logic

## Navigation

- **📋 Task Requirements**: See [README.md](README.md)
- **🏗️ Architecture**: This document
- **🧪 Testing Strategy**: Unit/Integration/Feature test approach
- **📦 Domain Models**: [Entity/AGENTS.md](src/Entity/AGENTS.md)

---

**Remember**: This is a demonstration of professional PHP development skills. Focus on clean architecture, SOLID principles, and comprehensive testing rather than complex features.
