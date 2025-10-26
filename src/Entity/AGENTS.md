# Entity Module - Agent Context

> **Parent**: [Root AGENTS.md](../../AGENTS.md)
> **Purpose**: Domain models and business entities
> **Module Path**: `src/Entity/`

## Module Responsibility

The Entity module contains the core domain models representing the business concepts of Doctor appointment scheduling. These entities encapsulate business rules and maintain data integrity.

## Domain Entities

### Doctor Entity

**Location**: `Doctor.php`

**Purpose**: Represents a medical practitioner with appointment availability

**Attributes**:
```php
class Doctor
{
    private string $id;        // Unique identifier from external system
    private string $name;      // Normalized full name
    private bool $error;       // Error flag for synchronization issues
}
```

**Business Rules**:
1. **Identity**: Doctor ID is immutable once set
2. **Name Normalization**: Names follow specific formatting rules
3. **Error State**: Doctors can be marked with error flag during sync failures
4. **Error Recovery**: Error flag can be cleared when synchronization succeeds

**Key Methods**:
- `getId()`: Get unique identifier
- `getName()`: Get normalized name
- `setName(string)`: Update name (triggers normalization)
- `markError()`: Flag doctor as having sync errors
- `clearError()`: Remove error flag
- `hasError()`: Check if doctor has errors

### Slot Entity

**Location**: `Slot.php`

**Purpose**: Represents an available appointment time slot for a doctor

**Attributes**:
```php
class Slot
{
    private string $id;          // Auto-generated primary key
    private int $doctorId;       // Reference to doctor
    private DateTime $start;     // Appointment start time
    private DateTime $end;       // Appointment end time  
    private DateTime $createdAt; // When slot was first synced
}
```

**Business Rules**:
1. **Immutable Start Time**: Start time cannot be changed after creation
2. **Flexible End Time**: End time can be updated if slot is stale
3. **Staleness**: Slots become stale after 5 minutes
4. **Time Ordering**: Start time must be before end time
5. **Doctor Association**: Must belong to a valid doctor

**Key Methods**:
- `getStart()`: Get appointment start time
- `setEnd(DateTime)`: Update end time (only if stale)
- `isStale()`: Check if slot needs updating (>5 minutes old)

## Entity Relationships

```
Doctor (1) ←→ (many) Slot

Doctor:
- One doctor can have many appointment slots
- Doctor ID is referenced by slots

Slot:
- Each slot belongs to exactly one doctor
- Slots are identified by doctorId + start time combination
```

## Current Architecture Issues

### Doctrine Mapping Issues

**Problem**: Inconsistent type mapping
```php
// In Doctor.php - string ID but integer column type annotation
/**
 * @ORM\Id
 * @ORM\Column(type="integer")  // ❌ Mismatch with string property
 */
private string $id;

// In Slot.php - similar issue
/**
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="AUTO")
 * @ORM\Column(type="integer")  // ❌ Mismatch with string property
 */
private string $id;
```

### Missing Validation

**Problem**: No input validation in constructors
```php
public function __construct(string $id, string $name)
{
    $this->id = $id;      // No validation of ID format
    $this->name = $name;  // No validation of name content
}
```

### Inconsistent Immutability

**Problem**: Some properties are mutable when they should be immutable
```php
// Doctor name can be changed after creation
public function setName(string $name): self 
{
    $this->name = $name;  // Should this be allowed?
    return $this;
}
```

## Refactoring Recommendations

### 1. Fix Doctrine Mapping

```php
// Doctor.php - Fix type consistency
/**
 * @ORM\Id
 * @ORM\Column(type="string", length=50)  // ✅ Match property type
 */
private string $id;

// Slot.php - Auto-generated integer ID
/**
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="AUTO")
 * @ORM\Column(type="integer")
 */
private ?int $id = null;  // ✅ Proper auto-generated ID
```

### 2. Add Value Objects

**DoctorId Value Object**:
```php
final class DoctorId
{
    public function __construct(private string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Doctor ID cannot be empty');
        }
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Doctor ID must be numeric');
        }
    }
    
    public function toString(): string
    {
        return $this->value;
    }
}
```

**DoctorName Value Object**:
```php
final class DoctorName
{
    public function __construct(private string $value)
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Doctor name cannot be empty');
        }
        if (strlen($value) > 255) {
            throw new InvalidArgumentException('Doctor name too long');
        }
    }
    
    public function toString(): string
    {
        return $this->value;
    }
    
    public static function fromRawName(string $rawName): self
    {
        $normalized = self::normalize($rawName);
        return new self($normalized);
    }
    
    private static function normalize(string $fullName): string
    {
        [, $surname] = explode(' ', $fullName, 2);
        
        if (0 === stripos($surname, "o'")) {
            return ucwords($fullName, ' \'');
        }
        
        return ucwords($fullName);
    }
}
```

### 3. Improved Entity Design

**Enhanced Doctor Entity**:
```php
final class Doctor
{
    private DoctorId $id;
    private DoctorName $name;
    private bool $hasError = false;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    
    public function __construct(DoctorId $id, DoctorName $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->hasError = false;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }
    
    public function updateName(DoctorName $name): void
    {
        if (!$this->name->equals($name)) {
            $this->name = $name;
            $this->updatedAt = new DateTime();
        }
    }
    
    public function markError(): void
    {
        $this->hasError = true;
        $this->updatedAt = new DateTime();
    }
    
    public function clearError(): void
    {
        $this->hasError = false;
        $this->updatedAt = new DateTime();
    }
}
```

**Enhanced Slot Entity**:
```php
final class Slot
{
    private ?int $id = null;
    private DoctorId $doctorId;
    private DateTime $start;
    private DateTime $end;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    
    public function __construct(DoctorId $doctorId, DateTime $start, DateTime $end)
    {
        $this->validateTimeRange($start, $end);
        
        $this->doctorId = $doctorId;
        $this->start = clone $start;
        $this->end = clone $end;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }
    
    public function updateEndTime(DateTime $end): void
    {
        if (!$this->isStale()) {
            throw new SlotNotStaleException('Cannot update non-stale slot');
        }
        
        $this->validateTimeRange($this->start, $end);
        $this->end = clone $end;
        $this->updatedAt = new DateTime();
    }
    
    public function isStale(): bool
    {
        return $this->createdAt < new DateTime('-5 minutes');
    }
    
    private function validateTimeRange(DateTime $start, DateTime $end): void
    {
        if ($start >= $end) {
            throw new InvalidTimeRangeException('Start time must be before end time');
        }
    }
}
```

## Domain Exceptions

### Slot-Specific Exceptions

```php
abstract class SlotException extends DomainException {}

class InvalidTimeRangeException extends SlotException {}
class SlotNotStaleException extends SlotException {}
class SlotOverlapException extends SlotException {}
```

### Doctor-Specific Exceptions

```php
abstract class DoctorException extends DomainException {}

class InvalidDoctorIdException extends DoctorException {}
class InvalidDoctorNameException extends DoctorException {}
class DoctorAlreadyExistsException extends DoctorException {}
```

## Entity Factory Pattern

### DoctorFactory

```php
class DoctorFactory
{
    public function createFromApiData(array $apiData): Doctor
    {
        $this->validateApiData($apiData);
        
        $id = new DoctorId((string)$apiData['id']);
        $name = DoctorName::fromRawName($apiData['name']);
        
        return new Doctor($id, $name);
    }
    
    private function validateApiData(array $data): void
    {
        if (!isset($data['id'], $data['name'])) {
            throw new InvalidApiDataException('Missing required fields: id, name');
        }
    }
}
```

### SlotFactory

```php
class SlotFactory
{
    public function createFromApiData(array $apiData, DoctorId $doctorId): Slot
    {
        $this->validateApiData($apiData);
        
        $start = new DateTime($apiData['start']);
        $end = new DateTime($apiData['end']);
        
        return new Slot($doctorId, $start, $end);
    }
    
    private function validateApiData(array $data): void
    {
        if (!isset($data['start'], $data['end'])) {
            throw new InvalidApiDataException('Missing required fields: start, end');
        }
        
        if (!strtotime($data['start']) || !strtotime($data['end'])) {
            throw new InvalidApiDataException('Invalid datetime format');
        }
    }
}
```

## Repository Interfaces

### DoctorRepositoryInterface

```php
interface DoctorRepositoryInterface
{
    public function findById(DoctorId $id): ?Doctor;
    public function save(Doctor $doctor): void;
    public function findAll(): array;
    public function findByName(string $namePattern): array;
    public function findWithErrors(): array;
}
```

### SlotRepositoryInterface

```php
interface SlotRepositoryInterface
{
    public function findById(int $id): ?Slot;
    public function save(Slot $slot): void;
    public function findByDoctorAndTime(DoctorId $doctorId, DateTime $start): ?Slot;
    public function findByDoctor(DoctorId $doctorId): array;
    public function findStaleSlots(DateTime $before): array;
}
```

## Testing Strategy

### Entity Unit Tests

**DoctorTest.php**:
```php
class DoctorTest extends TestCase
{
    public function testCreateDoctorWithValidData(): void
    {
        $id = new DoctorId('123');
        $name = new DoctorName('John Doe');
        
        $doctor = new Doctor($id, $name);
        
        $this->assertEquals('123', $doctor->getId()->toString());
        $this->assertEquals('John Doe', $doctor->getName()->toString());
        $this->assertFalse($doctor->hasError());
    }
    
    public function testMarkAndClearError(): void
    {
        $doctor = $this->createDoctor();
        
        $doctor->markError();
        $this->assertTrue($doctor->hasError());
        
        $doctor->clearError();
        $this->assertFalse($doctor->hasError());
    }
}
```

**SlotTest.php**:
```php
class SlotTest extends TestCase
{
    public function testCreateSlotWithValidTimeRange(): void
    {
        $doctorId = new DoctorId('123');
        $start = new DateTime('2025-01-01 10:00:00');
        $end = new DateTime('2025-01-01 11:00:00');
        
        $slot = new Slot($doctorId, $start, $end);
        
        $this->assertEquals($start, $slot->getStart());
        $this->assertFalse($slot->isStale()); // Just created
    }
    
    public function testCannotCreateSlotWithInvalidTimeRange(): void
    {
        $this->expectException(InvalidTimeRangeException::class);
        
        $doctorId = new DoctorId('123');
        $start = new DateTime('2025-01-01 11:00:00');
        $end = new DateTime('2025-01-01 10:00:00'); // End before start
        
        new Slot($doctorId, $start, $end);
    }
    
    public function testSlotBecomesStaleAfterFiveMinutes(): void
    {
        $slot = $this->createSlotFiveMinutesAgo();
        
        $this->assertTrue($slot->isStale());
    }
}
```

## Business Rule Validation

### Name Normalization Tests

```php
class DoctorNameTest extends TestCase
{
    /**
     * @dataProvider nameNormalizationProvider
     */
    public function testNameNormalization(string $input, string $expected): void
    {
        $name = DoctorName::fromRawName($input);
        
        $this->assertEquals($expected, $name->toString());
    }
    
    public static function nameNormalizationProvider(): array
    {
        return [
            ['john doe', 'John Doe'],
            ['JANE SMITH', 'Jane Smith'],
            ["mary o'connor", "Mary O'Connor"],
            ["patrick o'brien", "Patrick O'Brien"],
            ['dr. house', 'Dr. House'],
        ];
    }
}
```

## Performance Considerations

### Entity Optimization

**Lazy Loading**: Use repository patterns to avoid N+1 queries
**Immutable Objects**: Value objects are naturally thread-safe
**Memory Usage**: Clone DateTime objects to prevent mutation

### Database Optimization

**Indexes**:
```sql
-- Doctor lookups
CREATE INDEX idx_doctor_name ON doctor(name);

-- Slot lookups
CREATE INDEX idx_slot_doctor_start ON slot(doctor_id, start);
CREATE INDEX idx_slot_created_at ON slot(created_at);
```

## Common Pitfalls

### ❌ Mutable DateTime Objects

```php
// Bad: Shared DateTime reference
$this->start = $start;  // Reference to mutable object
```

```php
// Good: Clone to prevent mutation
$this->start = clone $start;  // Safe copy
```

### ❌ Missing Validation

```php
// Bad: No validation
public function __construct(string $id, string $name)
{
    $this->id = $id;    // What if empty?
    $this->name = $name; // What if null?
}
```

```php
// Good: Validation with value objects
public function __construct(DoctorId $id, DoctorName $name)
{
    $this->id = $id;    // Already validated
    $this->name = $name; // Already validated
}
```

### ❌ Inconsistent State Changes

```php
// Bad: No timestamp update
public function markError(): void
{
    $this->hasError = true;  // When did this happen?
}
```

```php
// Good: Track when changes occur
public function markError(): void
{
    $this->hasError = true;
    $this->updatedAt = new DateTime();
}
```

## Navigation

- **⬆️ Back to**: [Root AGENTS.md](../../AGENTS.md)
- **➡️ Related**: [Source Module](../AGENTS.md)
- **📋 Task Requirements**: [README.md](../../README.md)
