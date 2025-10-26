# Refactoring Documentation

## Overview

This document describes the refactoring of the original `DoctorSlotsSynchronizer` class into a clean, maintainable architecture following SOLID principles.

## Architecture Changes

### Before Refactoring

The original `DoctorSlotsSynchronizer` violated several SOLID principles:

- **Single Responsibility Principle**: Handled HTTP requests, business logic, database persistence, and logging
- **Open/Closed Principle**: Hard to extend with new data sources
- **Dependency Inversion**: Directly depended on Doctrine and Monolog concrete classes

### After Refactoring

The new architecture follows clean architecture principles:

```
┌─────────────────────────────────────┐
│   Application Layer                 │
│   - DoctorSynchronizationService    │
│   - DoctorSynchronizationServiceFactory │
├─────────────────────────────────────┤
│   Domain Layer                      │
│   - Doctor (Entity)                 │
│   - Slot (Entity)                   │
│   - DoctorId, DoctorName (Value Objects) │
│   - NameNormalizationService        │
│   - ErrorReportingService           │
├─────────────────────────────────────┤
│   Infrastructure Layer              │
│   - HttpDoctorApiClient             │
│   - StaticDoctorApiClient           │
│   - DoctorRepository                │
│   - SlotRepository                  │
└─────────────────────────────────────┘
```

## Key Improvements

### 1. Single Responsibility Principle

Each class now has a single, well-defined responsibility:

- `DoctorSynchronizationService`: Orchestrates the synchronization process
- `HttpDoctorApiClient`: Handles HTTP communication with external API
- `NameNormalizationService`: Handles business rules for name formatting
- `ErrorReportingService`: Manages error reporting according to business rules
- Repository classes: Handle data persistence

### 2. Dependency Injection

All dependencies are injected via constructor, making the code testable and flexible:

```php
public function __construct(
    private DoctorApiClientInterface $apiClient,
    private DoctorRepositoryInterface $doctorRepository,
    private SlotRepositoryInterface $slotRepository,
    private NameNormalizationService $nameNormalizer,
    private ErrorReportingService $errorReporter,
    private LoggerInterface $logger
) {}
```

### 3. Interface Segregation

Created focused interfaces:
- `DoctorApiClientInterface`: API communication
- `DoctorRepositoryInterface`: Doctor data access
- `SlotRepositoryInterface`: Slot data access

### 4. Value Objects

Introduced value objects for better domain modeling:
- `DoctorId`: Ensures valid doctor identifiers
- `DoctorName`: Handles name validation and normalization

### 5. Exception Hierarchy

Created specific exceptions for better error handling:
- `DoctorSynchronizationException`: Base exception
- `ApiConnectionException`: API communication failures
- `InvalidApiResponseException`: Invalid API responses
- `InvalidNameException`: Name validation failures

## Usage Examples

### Using the New Service

```php
use App\Service\DoctorSynchronizationServiceFactory;

// Create service with HTTP client
$service = DoctorSynchronizationServiceFactory::createWithHttpClient($entityManager);

// Synchronize doctors
$result = $service->synchronize();

// Check results
if ($result->isSuccessful()) {
    echo "Successfully synchronized {$result->getSuccessCount()} doctors";
} else {
    echo "Errors occurred: " . implode(', ', $result->getErrors());
}
```

### Using Static Data for Testing

```php
// Create service with static data client
$service = DoctorSynchronizationServiceFactory::createWithStaticClient($entityManager);
$result = $service->synchronize();
```

### Backward Compatibility

For existing code, use the legacy wrapper (with deprecation notice):

```php
use App\LegacyDoctorSlotsSynchronizer;

$synchronizer = new LegacyDoctorSlotsSynchronizer($entityManager);
$synchronizer->synchronizeDoctorSlots(); // Triggers deprecation warning
```

## Testing

### Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run only unit tests
vendor/bin/phpunit tests/Unit

# Run with coverage (if configured)
vendor/bin/phpunit --coverage-html coverage
```

### Test Structure

- `tests/Unit/`: Unit tests for individual components
- `tests/Integration/`: Integration tests for database/API interactions (to be added)

## Entity Mapping Fixes

Fixed Doctrine mapping inconsistencies:

### Doctor Entity
- Changed ID column type from `integer` to `string` to match property type
- Proper mapping for string-based doctor IDs

### Slot Entity  
- Changed ID property from `string` to `?int` to match auto-generated integer IDs
- Proper nullable type for auto-generated primary keys

## Business Rules Preserved

All original business rules are maintained:

1. **Name Normalization**: Irish surnames (O'Name) get special capitalization
2. **Slot Staleness**: Slots older than 5 minutes can be updated
3. **Error Reporting**: No error reporting on Sundays
4. **Graceful Degradation**: Individual doctor failures don't stop the entire process

## Future Improvements

The new architecture enables easy implementation of:

1. **Batch Processing**: Process multiple doctors in batches for better performance
2. **Async Processing**: Use message queues for background synchronization
3. **Caching**: Add caching layer for API responses
4. **Circuit Breaker**: Add circuit breaker pattern for API failures
5. **Retry Logic**: Add exponential backoff for failed requests
6. **Configuration**: Move configuration to environment variables

## Performance Considerations

Current implementation maintains the same performance characteristics as the original while enabling future optimizations:

- Each doctor is still processed individually
- Database operations are still immediate (no batching)
- API calls are still synchronous

These can be improved in future iterations without changing the public API.

## Migration Guide

1. **Immediate**: Use `DoctorSynchronizationServiceFactory` for new code
2. **Short term**: Replace `DoctorSlotsSynchronizer` with `LegacyDoctorSlotsSynchronizer`
3. **Long term**: Migrate to `DoctorSynchronizationService` directly
4. **Eventually**: Remove legacy classes once all code is migrated
