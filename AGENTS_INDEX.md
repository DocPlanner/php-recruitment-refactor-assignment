# AGENTS Documentation Index

> **Purpose**: Navigation map for all AGENTS.md documentation files
> **Usage**: Find relevant context documentation for any file path

## Documentation Structure

### Root Documentation
- **[AGENTS.md](AGENTS.md)** - Main system documentation
- **[ai-review-prompt.md](ai-review-prompt.md)** - AI code review instructions

### Module Documentation

| Path | AGENTS.md Location | Purpose |
|------|-------------------|---------|
| `src/` | [src/AGENTS.md](src/AGENTS.md) | Core synchronization logic and patterns |
| `src/Entity/` | [src/Entity/AGENTS.md](src/Entity/AGENTS.md) | Domain models and business entities |

## File Path to Documentation Mapping

### Core Files

| File Path | Relevant Documentation |
|-----------|----------------------|
| `src/DoctorSlotsSynchronizer.php` | [src/AGENTS.md](src/AGENTS.md) § "Current Implementation" |
| `src/StaticDoctorSlotsSynchronizer.php` | [src/AGENTS.md](src/AGENTS.md) § "StaticDoctorSlotsSynchronizer" |
| `src/Entity/Doctor.php` | [src/Entity/AGENTS.md](src/Entity/AGENTS.md) § "Doctor Entity" |
| `src/Entity/Slot.php` | [src/Entity/AGENTS.md](src/Entity/AGENTS.md) § "Slot Entity" |

### Configuration Files

| File Path | Relevant Documentation |
|-----------|----------------------|
| `composer.json` | [AGENTS.md](AGENTS.md) § "Development Guidelines" |
| `docker-compose.yml` | [README.md](README.md) § "Installation" |
| `README.md` | [AGENTS.md](AGENTS.md) § "Project Overview" |

## Documentation Topics

### Architecture & Design Patterns
- **Main Reference**: [AGENTS.md](AGENTS.md) § "Refactoring Goals"
- **Patterns**: [AGENTS.md](AGENTS.md) § "Design Patterns to Apply"
- **SOLID Principles**: [AGENTS.md](AGENTS.md) § "SOLID Principles Application"

### Business Logic
- **Synchronization Flow**: [src/AGENTS.md](src/AGENTS.md) § "Business Logic Analysis"
- **Business Rules**: [src/AGENTS.md](src/AGENTS.md) § "Business Rules"
- **Entity Relationships**: [src/Entity/AGENTS.md](src/Entity/AGENTS.md) § "Entity Relationships"

### Testing Strategy
- **Overall Strategy**: [AGENTS.md](AGENTS.md) § "Testing Strategy"
- **Unit Tests**: [src/AGENTS.md](src/AGENTS.md) § "Testing Strategy"
- **Entity Tests**: [src/Entity/AGENTS.md](src/Entity/AGENTS.md) § "Testing Strategy"

### Performance & Security
- **Performance**: [src/AGENTS.md](src/AGENTS.md) § "Performance Considerations"
- **Security**: [src/Entity/AGENTS.md](src/Entity/AGENTS.md) § "Current Security Issues"
- **Error Handling**: [src/AGENTS.md](src/AGENTS.md) § "Error Handling Strategy"

## Quick Reference for Common Scenarios

### "I need to understand the current code structure"
→ **Start with**: [src/AGENTS.md](src/AGENTS.md) § "Current Implementation"

### "I want to refactor this class"
→ **Reference**: [AGENTS.md](AGENTS.md) § "Refactoring Goals" + [src/AGENTS.md](src/AGENTS.md) § "Refactoring Strategy"

### "I need to understand business rules"
→ **Reference**: [src/AGENTS.md](src/AGENTS.md) § "Business Rules" + [src/Entity/AGENTS.md](src/Entity/AGENTS.md) § "Business Rules"

### "I want to add tests"
→ **Reference**: [AGENTS.md](AGENTS.md) § "Testing Strategy" + specific module testing sections

### "I need to fix architectural issues"
→ **Reference**: [AGENTS.md](AGENTS.md) § "Current Architecture Issues" + § "SOLID Principles Application"

### "I want to understand entities"
→ **Reference**: [src/Entity/AGENTS.md](src/Entity/AGENTS.md) § "Domain Entities"

### "I need to handle errors properly"
→ **Reference**: [src/AGENTS.md](src/AGENTS.md) § "Error Handling Strategy"

## Code Review Context

When reviewing changes to specific files, consult these documentation sections:

### For Entity Changes
- **Doctor.php**: [src/Entity/AGENTS.md](src/Entity/AGENTS.md) § "Doctor Entity" + § "Business Rules"
- **Slot.php**: [src/Entity/AGENTS.md](src/Entity/AGENTS.md) § "Slot Entity" + § "Business Rules"

### For Service Changes  
- **Synchronizer changes**: [src/AGENTS.md](src/AGENTS.md) § "Current Implementation" + § "Architectural Issues"
- **New services**: [src/AGENTS.md](src/AGENTS.md) § "Extracted Services"

### For Test Changes
- **Test structure**: [AGENTS.md](AGENTS.md) § "Testing Guidelines"
- **Entity tests**: [src/Entity/AGENTS.md](src/Entity/AGENTS.md) § "Entity Unit Tests" 
- **Service tests**: [src/AGENTS.md](src/AGENTS.md) § "Unit Tests"

## Documentation Maintenance

### When Adding New Files
1. Update this index with file path mapping
2. Add relevant AGENTS.md section if needed
3. Update navigation links in affected modules

### When Refactoring
1. Update architectural documentation in [AGENTS.md](AGENTS.md)
2. Update implementation details in module AGENTS.md files
3. Update this index if file paths change

### When Adding Features
1. Document business rules in appropriate AGENTS.md
2. Update testing strategy sections
3. Add file path mappings to this index

## Quick Navigation

- **🏠 Home**: [AGENTS.md](AGENTS.md) - Start here for system overview
- **⚙️ Implementation**: [src/AGENTS.md](src/AGENTS.md) - Current code analysis  
- **🏛️ Domain**: [src/Entity/AGENTS.md](src/Entity/AGENTS.md) - Business entities
- **🧪 Testing**: All AGENTS.md files have testing sections
- **📋 Requirements**: [README.md](README.md) - Original task description

---

**Usage**: When reviewing or working on any file, find its path in the mapping above to locate the most relevant documentation context.
