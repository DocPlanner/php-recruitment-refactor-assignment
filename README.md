# Old Doctor Slots Synchronizer: Analysis
To add unit tests for `DoctorSlotsSynchronizer.php`, I will first analyze the existing code to understand the business logic.
Then, I will refactor the code where needed to improve testability and follow best practices. Finally, I will write unit tests using PHPUnit to cover the business logic.

### 1. **Understanding the Business Logic**

The class `DoctorSlotsSynchronizer` handles the synchronization of doctor slots from an external API. Here's a breakdown of its functionality:

1. **Fetching Doctors Data**: It retrieves a list of doctors from an external API endpoint.
2. **Normalizing Doctor Names**: The names of doctors are normalized, e.g., handling special cases like surnames with prefixes.
3. **Handling Existing Doctors**: For each doctor fetched from the API, it checks if they exist in the database; if not, a new `Doctor` entity is created.
4. **Synchronizing Slots**: For each doctor, it fetches their available slots from another API endpoint and updates the database with these slots.
5. **Error Handling**: If any slot cannot be fetched or parsed, it marks the doctor with an error status.

### 2. **Refactoring for Better Testability**

To make the code more testable, I will introduce some changes:

- **Extract Methods**: Break down large methods into smaller, more focused methods.
- **Dependency Injection for External Services**: Use dependency injection for services like fetching data from external APIs and logging, which makes it easier to mock them in tests.
- **Single Responsibility Principle**: Ensure that each method has a single responsibility to simplify unit testing.

### 3. **Refactoring `DoctorSlotsSynchronizer.php`**

### 4. **Writing Unit Tests**

I will use PHPUnit to write unit tests for the refactored `DoctorSlotsSynchronizer` class.
I will mock dependencies like `ApiClientInterface`, `EntityManagerInterface`, and `Logger` to isolate the tests and focus on the business logic.

### 5. **Conclusion**

- **Refactoring Benefits**: The refactoring improves the readability, maintainability, and testability of the `DoctorSlotsSynchronizer` class.
- **Mocking Dependencies**: Mocking external dependencies allows us to write isolated unit tests that focus on business logic rather than implementation details.
- **Unit Testing**: The tests cover various scenarios, such as creating new doctors, updating existing ones, handling errors, and managing slots synchronization.

---

# New Doctor Slots Synchronizer

This project is a PHP refactor of an application designed to synchronize doctor information and their available slots from an external API into a local database.
It includes features such as retry mechanisms for transient failures, improved logging for better observability, unit and integration tests, and adherence to coding standards using PHP-CS-Fixer.


## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running Tests](#running-tests)
- [Project Structure](#project-structure)
- [Code Standards](#code-standards)

## Prerequisites

- PHP 8.0 or higher
- Composer
- Docker (optional, for containerization)
- A database supported by Doctrine ORM (e.g., MySQL, PostgreSQL, SQLite)

## Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/yourusername/doctor-slots-synchronizer.git
   ```

2. **Navigate to the project directory:**

   ```bash
   cd doctor-slots-synchronizer
   ```

3. **Install dependencies using Composer:**

   ```bash
   composer install
   ```

4. **Set up the environment variables:**

   Copy the `.env.example` file to `.env` and configure it accordingly.

   ```bash
   cp .env.example .env
   ```

## Configuration

### Environment Variables

The application uses environment variables for configuration. These are defined in the `.env` file.

- **API_ENDPOINT**: The endpoint of the external API to fetch doctor and slot data.
- **API_USERNAME**: Username for API authentication.
- **API_PASSWORD**: Password for API authentication.

Example `.env` file:

```env
API_ENDPOINT=http://localhost:2137/api/doctors
API_USERNAME=docplanner
API_PASSWORD=docplanner
```
### Docker Setup

1. **Build and Start Containers:**

   Run the following command to build the Docker images and start the containers:

   ```bash
   docker-compose up --build
   ```

   This will start the application at `http://localhost:2137/api/doctors`.

   ```bash
   curl --location 'http://localhost:2137/api/doctors' --header 'Authorization: Basic ZG9jcGxhbm5lcjpkb2NwbGFubmVy'
   ```

2. **Stop Containers:**

   To stop the running containers, press `Ctrl+C` or run:

   ```bash
   docker-compose down
   ```

### Application Flow

1. **Fetch Doctors**: Retrieves a list of doctors from the external API.
2. **Process Each Doctor**:
    - Normalizes the doctor's name.
    - Checks if the doctor exists in the local database; creates or updates as necessary.
3. **Fetch and Process Slots**:
    - Fetches available slots for each doctor.
    - Processes each slot, saving it to the database.
4. **Error Handling**:
    - Implements retry mechanisms for transient API failures.
    - Logs errors with detailed context.

## Running Tests

The project includes both unit and integration tests using PHPUnit.

### Unit Tests

To run unit tests:

```bash
composer test -- --testsuite Unit
```
## Project Structure

```
project_root/
├── .env
├── bin/
├── docker/
│   ├── Dockerfile
│   ├── entrypoint.sh
│   ├── docker-compose.yml
├── src/
│   ├── Entity/
│   │   ├── Doctor.php
│   │   └── Slot.php
│   ├── Service/
│   │   ├── DoctorSlotsSynchronizer.php
│   │   └── StaticDoctorSlotsSynchronizer.php
├── tests/
│   └── Unit/
│       ├── Entity/
│       │   ├── DoctoTest.php
│       │   └── SlotTest.php
│       └── Service/
│           ├── DoctorSlotsSynchronizerTest.php
│           └── StaticDoctorSlotsSynchronizerTest.php
├── vendor/
├── .php-cs-fixer.dist.php
├── composer.json
├── composer.lock
├── phpunit.xml
├── public/
│   └── index.php
```

## Code Standards

The project adheres to the PSR-12 coding standard and uses PHP-CS-Fixer for code formatting.

### Running PHP-CS-Fixer

To check for coding standard violations:

```bash
composer cs-fix -- --dry-run --diff
```

To automatically fix coding standard violations:

```bash
composer cs-fix
```

### Configuration

The `.php-cs-fixer.dist.php` file contains the configuration for PHP-CS-Fixer.

---

Rafa Terrero